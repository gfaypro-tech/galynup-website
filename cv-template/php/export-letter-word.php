<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit('ID invalide.'); }

$db   = getDB();
$stmt = $db->prepare("SELECT company, position, letter_content FROM cv_applications WHERE id = ?");
$stmt->execute([$id]);
$app  = $stmt->fetch();
if (!$app || empty($app['letter_content'])) { http_response_code(404); exit('Lettre introuvable.'); }

$ascii    = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $app['position'] ?? '');
$slug     = strtoupper(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $ascii)));
$slug     = preg_replace('/\s+/', '_', $slug);
$filename = 'LM_GFAY_' . ($slug ?: 'LM') . '_' . date('Y-m-d') . '.docx';

// ─── XML helpers ─────────────────────────────────────────────────────────

function xe(string $s): string {
    return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function wr(string $text, bool $bold = false, bool $italic = false,
            string $color = '', float $sizePt = 0): string
{
    if ($text === '') return '';
    $rPr = '';
    if ($bold || $italic || $color || $sizePt) {
        $rPr = '<w:rPr>';
        if ($bold)   $rPr .= '<w:b/><w:bCs/>';
        if ($italic) $rPr .= '<w:i/><w:iCs/>';
        if ($color)  $rPr .= '<w:color w:val="' . xe(ltrim($color, '#')) . '"/>';
        if ($sizePt) {
            $hp   = (int)round($sizePt * 2);
            $rPr .= '<w:sz w:val="' . $hp . '"/><w:szCs w:val="' . $hp . '"/>';
        }
        $rPr .= '</w:rPr>';
    }
    return '<w:r>' . $rPr . '<w:t xml:space="preserve">' . xe($text) . '</w:t></w:r>';
}

function wp(string $runs, string $jc = '', int $before = 0, int $after = 0): string
{
    $pPr = '';
    if ($jc || $before || $after) {
        $pPr = '<w:pPr>';
        if ($jc)               $pPr .= '<w:jc w:val="' . $jc . '"/>';
        if ($before || $after) $pPr .= '<w:spacing w:before="' . $before . '" w:after="' . $after . '"/>';
        $pPr .= '</w:pPr>';
    }
    return '<w:p>' . $pPr . $runs . '</w:p>';
}

function parseInline(DOMNode $node, bool $bold = false, bool $italic = false,
                     string $color = '', float $sizePt = 0): string
{
    $out = '';
    foreach ($node->childNodes as $child) {
        if ($child->nodeType === XML_TEXT_NODE) {
            if ($child->nodeValue !== '') {
                $out .= wr($child->nodeValue, $bold, $italic, $color, $sizePt);
            }
        } elseif ($child->nodeType === XML_ELEMENT_NODE) {
            $t = strtolower($child->nodeName);
            if ($t === 'br') {
                $out .= '<w:r><w:br/></w:r>';
                continue;
            }
            $b = $bold   || ($t === 'strong') || ($t === 'b');
            $i = $italic || ($t === 'em')     || ($t === 'i');
            $out .= parseInline($child, $b, $i, $color, $sizePt);
        }
    }
    return $out;
}

// ─── Convertisseur HTML lettre → corps Word XML ──────────────────────────

function htmlToLetterBody(string $html): string
{
    $dom = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>',
                   LIBXML_HTML_NOIMPLIED | LIBXML_NOERROR);
    libxml_clear_errors();
    $xp = new DOMXPath($dom);

    $byClass = function(string $cls) use ($xp): ?DOMNode {
        $list = $xp->query('//*[contains(@class,"' . $cls . '")]');
        return ($list && $list->length > 0) ? $list->item(0) : null;
    };

    $body = '';

    // ── Expéditeur ──
    $sender = $byClass('letter-sender');
    if ($sender) {
        $body .= wp(parseInline($sender, false, false, '555555', 10), '', 0, 20);
    }

    // ── Date ──
    $dateEl = $byClass('letter-date');
    if ($dateEl && trim($dateEl->textContent) !== '') {
        $body .= wp(wr(trim($dateEl->textContent), false, false, '555555', 10), '', 0, 240);
    }

    // ── Destinataire ──
    $recipient = $byClass('letter-recipient');
    if ($recipient) {
        $body .= wp(parseInline($recipient, false, false, '', 11), '', 0, 240);
    }

    // ── Objet ──
    $object = $byClass('letter-object');
    if ($object && trim($object->textContent) !== '') {
        $body .= wp(wr(trim($object->textContent), true, false, '', 11), '', 0, 240);
    }

    // ── Corps ──
    $letterBody = $byClass('letter-body');
    if ($letterBody) {
        foreach ($letterBody->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE || strtolower($child->nodeName) !== 'p') continue;
            if (trim($child->textContent) === '') continue;
            $body .= wp(parseInline($child, false, false, '', 11), 'both', 0, 120);
        }
    }

    // ── Signature ──
    $sig = $byClass('letter-signature');
    if ($sig) {
        $body .= wp('', '', 480, 0);
        foreach ($sig->childNodes as $child) {
            if ($child->nodeType !== XML_ELEMENT_NODE) continue;
            $t = strtolower($child->nodeName);
            if ($t === 'p' || $t === 'div') {
                $text = trim($child->textContent);
                if ($text !== '') {
                    $body .= wp(wr($text, false, false, '', 11), '', 0, 20);
                }
            }
        }
    }

    return $body ?: '<w:p><w:r><w:t>Lettre vide</w:t></w:r></w:p>';
}

// ─── Parties du fichier DOCX ──────────────────────────────────────────────

$CONTENT_TYPES = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
  <Override PartName="/word/settings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml"/>
</Types>';

$RELS = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>';

$WORD_RELS = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/settings" Target="settings.xml"/>
</Relationships>';

$SETTINGS = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:defaultTabStop w:val="720"/>
  <w:compat>
    <w:compatSetting w:name="compatibilityMode"
                     w:uri="http://schemas.microsoft.com/office/word"
                     w:val="15"/>
  </w:compat>
</w:settings>';

$STYLES = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:docDefaults>
    <w:rPrDefault><w:rPr>
      <w:rFonts w:ascii="Calibri" w:hAnsi="Calibri" w:cs="Calibri"/>
      <w:sz w:val="22"/><w:szCs w:val="22"/>
      <w:color w:val="1C1C18"/>
      <w:lang w:val="fr-FR"/>
    </w:rPr></w:rPrDefault>
    <w:pPrDefault><w:pPr>
      <w:spacing w:after="0" w:line="276" w:lineRule="auto"/>
    </w:pPr></w:pPrDefault>
  </w:docDefaults>
</w:styles>';

// ─── Génération et envoi ──────────────────────────────────────────────────

if (!class_exists('ZipArchive')) {
    // Fallback si ZipArchive indisponible
    $fallbackFile = str_replace('.docx', '.doc', $filename);
    header('Content-Type: application/msword; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $fallbackFile . '"');
    header('Cache-Control: no-cache');
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $app['letter_content'] . '</body></html>';
    exit;
}

$bodyXml = htmlToLetterBody($app['letter_content']);

$DOC = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:body>
' . $bodyXml . '
    <w:sectPr>
      <w:pgSz w:w="11906" w:h="16838"/>
      <w:pgMar w:top="1418" w:right="1418" w:bottom="1418" w:left="1701"
               w:header="708" w:footer="708" w:gutter="0"/>
    </w:sectPr>
  </w:body>
</w:document>';

$tmp = tempnam(sys_get_temp_dir(), 'lmdocx_');
$zip = new ZipArchive();
if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    exit('Erreur création fichier DOCX.');
}
$zip->addFromString('[Content_Types].xml',          $CONTENT_TYPES);
$zip->addFromString('_rels/.rels',                  $RELS);
$zip->addFromString('word/document.xml',            $DOC);
$zip->addFromString('word/_rels/document.xml.rels', $WORD_RELS);
$zip->addFromString('word/styles.xml',              $STYLES);
$zip->addFromString('word/settings.xml',            $SETTINGS);
$zip->close();

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($tmp));
header('Cache-Control: no-cache');
readfile($tmp);
@unlink($tmp);
exit;
