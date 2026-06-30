<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit('ID invalide.'); }

$db   = getDB();
$stmt = $db->prepare("SELECT * FROM obr_applications WHERE id = ?");
$stmt->execute([$id]);
$app  = $stmt->fetch();
if (!$app || empty($app['cv_content'])) { http_response_code(404); exit('CV introuvable.'); }

$company  = preg_replace('/[^a-zA-Z0-9_-]/', '_', $app['company']);
$position = preg_replace('/[^a-zA-Z0-9_-]/', '_', $app['position']);
$filename = "CV_{$company}_{$position}_" . date('Y-m-d') . ".docx";

// ─── XML helpers ──────────────────────────────────────────────────────────

function xe(string $s): string {
    return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

// Construit un w:r (run de texte) avec mise en forme optionnelle
function wr(string $text, bool $bold = false, bool $italic = false,
            string $color = '', float $sizePt = 0, string $font = ''): string
{
    if ($text === '') return '';
    $rPr = '';
    if ($font || $bold || $italic || $color || $sizePt) {
        $rPr = '<w:rPr>';
        if ($font)   $rPr .= '<w:rFonts w:ascii="' . xe($font) . '" w:hAnsi="' . xe($font) . '" w:cs="' . xe($font) . '"/>';
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

// Construit un w:p (paragraphe)
function wp(string $runs, string $styleId = '', string $jc = '',
            int $before = 0, int $after = 0, int $indent = 0): string
{
    $pPr = '';
    if ($styleId || $jc || $before || $after || $indent) {
        $pPr = '<w:pPr>';
        if ($styleId)        $pPr .= '<w:pStyle w:val="' . xe($styleId) . '"/>';
        if ($jc)             $pPr .= '<w:jc w:val="' . $jc . '"/>';
        if ($before || $after) $pPr .= '<w:spacing w:before="' . $before . '" w:after="' . $after . '"/>';
        if ($indent)         $pPr .= '<w:ind w:left="' . $indent . '"/>';
        $pPr .= '</w:pPr>';
    }
    return '<w:p>' . $pPr . $runs . '</w:p>';
}

// Parcourt récursivement les noeuds inline (strong, em, text…) → runs Word
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
            $b = $bold   || ($t === 'strong') || ($t === 'b');
            $i = $italic || ($t === 'em')     || ($t === 'i');
            $out .= parseInline($child, $b, $i, $color, $sizePt);
        }
    }
    return $out;
}

// Raccourci XPath : premier résultat ou null
function xqFirst(DOMXPath $xp, string $q, ?DOMNode $ctx = null): ?DOMNode {
    $list = $ctx ? $xp->query($q, $ctx) : $xp->query($q);
    return ($list && $list->length > 0) ? $list->item(0) : null;
}

// ─── Table 2 colonnes (Compétences / Certifications) ──────────────────────

function build2ColTable(array $items): string
{
    $half  = (int)ceil(count($items) / 2);
    $col1  = array_slice($items, 0, $half);
    $col2  = array_slice($items, $half);
    $rows  = '';
    $noBorder = '<w:tcBorders>'
        . '<w:top w:val="nil"/><w:left w:val="nil"/>'
        . '<w:bottom w:val="nil"/><w:right w:val="nil"/>'
        . '</w:tcBorders>';

    for ($i = 0, $n = count($col1); $i < $n; $i++) {
        $c1 = isset($col1[$i]) ? $col1[$i] : '';
        $c2 = isset($col2[$i]) ? $col2[$i] : '';
        $cell = function(string $text) use ($noBorder) {
            $inner = ($text !== '')
                ? wp(wr('▸  ', false, false, '6D155D', 9.5) . wr($text, false, false, '', 9.5), '', '', 0, 20)
                : '<w:p/>';
            return '<w:tc><w:tcPr><w:tcW w:w="4680" w:type="dxa"/>'
                . $noBorder . '</w:tcPr>' . $inner . '</w:tc>';
        };
        $rows .= '<w:tr>' . $cell($c1) . $cell($c2) . '</w:tr>';
    }

    $tblBorders = '<w:tblBorders>'
        . '<w:top w:val="nil"/><w:left w:val="nil"/>'
        . '<w:bottom w:val="nil"/><w:right w:val="nil"/>'
        . '<w:insideH w:val="nil"/><w:insideV w:val="nil"/>'
        . '</w:tblBorders>';

    return '<w:tbl>'
        . '<w:tblPr><w:tblW w:w="9360" w:type="dxa"/>' . $tblBorders . '</w:tblPr>'
        . '<w:tblGrid><w:gridCol w:w="4680"/><w:gridCol w:w="4680"/></w:tblGrid>'
        . $rows . '</w:tbl>';
}

// ─── Bloc expérience (cv-job) ─────────────────────────────────────────────

function parseJob(DOMNode $jobDiv, DOMXPath $xp): string
{
    $out = '';

    // Ligne titre + date (tab droit)
    $hdr = xqFirst($xp, './/*[contains(@class,"cv-job-header")]', $jobDiv);
    if ($hdr) {
        $tSpan = xqFirst($xp, './/*[contains(@class,"cv-job-title")]', $hdr);
        $dSpan = xqFirst($xp, './/*[contains(@class,"cv-job-date")]',  $hdr);
        $tRuns = $tSpan ? parseInline($tSpan, true, false, '', 10) : '';
        $dRun  = $dSpan ? wr(trim($dSpan->textContent), false, false, '555555', 9) : '';
        $pPr   = '<w:pPr>'
            . '<w:tabs><w:tab w:val="right" w:pos="9360"/></w:tabs>'
            . '<w:spacing w:before="80" w:after="20"/>'
            . '</w:pPr>';
        $out .= '<w:p>' . $pPr . $tRuns . '<w:r><w:tab/></w:r>' . $dRun . '</w:p>';
    }

    // Contexte (italique)
    $ctx = xqFirst($xp, './/*[contains(@class,"cv-job-context")]', $jobDiv);
    if ($ctx && trim($ctx->textContent) !== '') {
        $out .= wp(parseInline($ctx, false, true, '555555', 9), '', '', 0, 40);
    }

    // Puces
    $ul = xqFirst($xp, './/*[contains(@class,"cv-job-bullets")]', $jobDiv);
    if ($ul) {
        foreach ($ul->childNodes as $li) {
            if ($li->nodeType !== XML_ELEMENT_NODE || strtolower($li->nodeName) !== 'li') continue;
            $out .= wp(
                wr('▸  ', false, false, '6D155D', 9.5) . parseInline($li, false, false, '', 9.5),
                '', '', 0, 20, 200
            );
        }
    }

    // Note optionnelle
    $note = xqFirst($xp, './/*[contains(@class,"cv-job-note")]', $jobDiv);
    if ($note && trim($note->textContent) !== '') {
        $out .= wp(parseInline($note, false, true, '777777', 9), '', '', 0, 40);
    }

    $out .= wp('', '', '', 0, 80); // espace après chaque poste
    return $out;
}

// ─── Convertisseur HTML CV → corps Word XML ───────────────────────────────

function htmlToWordBody(string $html): string
{
    $dom = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>',
                   LIBXML_HTML_NOIMPLIED | LIBXML_NOERROR);
    libxml_clear_errors();
    $xp   = new DOMXPath($dom);
    $body = '';

    // ── En-tête ──
    $hdr = xqFirst($xp, '//*[contains(@class,"cv-header")]');
    if ($hdr) {
        $h1  = xqFirst($xp, './/h1', $hdr);
        $sub = xqFirst($xp, './/*[contains(@class,"cv-subtitle")]',     $hdr);
        $tag = xqFirst($xp, './/*[contains(@class,"cv-tagline")]',      $hdr);
        $cnt = xqFirst($xp, './/*[contains(@class,"cv-contact")]',      $hdr);
        $dsp = xqFirst($xp, './/*[contains(@class,"cv-disponibilite")]', $hdr);
        if ($h1)  $body .= wp(wr(trim($h1->textContent),  true,  false, '1C1C18', 20, 'Georgia'), '', 'center', 0, 60);
        if ($sub) $body .= wp(wr(trim($sub->textContent), true,  false, '6D155D', 12),            '', 'center', 0, 40);
        if ($tag) $body .= wp(wr(trim($tag->textContent), false, false, '555555',  9),            '', 'center', 0, 20);
        if ($cnt) $body .= wp(wr(trim($cnt->textContent), false, false, '666666',  9),            '', 'center', 0, 20);
        if ($dsp) $body .= wp(wr(trim($dsp->textContent), false, false, '6D155D',  9),            '', 'center', 0, 100);
    }

    // ── Sections ──
    $secs = $xp->query('//*[@id="cv"]//section[contains(@class,"cv-section")]');
    if (!$secs || $secs->length === 0) {
        $secs = $xp->query('//section[contains(@class,"cv-section")]');
    }
    if ($secs) {
        foreach ($secs as $sec) {
            // Titre de section
            $tn = xqFirst($xp, './/*[contains(@class,"cv-section-title")]', $sec);
            if ($tn) {
                $body .= wp(wr(trim($tn->textContent), true, false, '6D155D', 8.5), 'CVSectionTitle');
            }
            // Enfants de la section
            foreach ($sec->childNodes as $child) {
                if ($child->nodeType !== XML_ELEMENT_NODE) continue;
                $tag   = strtolower($child->nodeName);
                $class = ($child instanceof DOMElement) ? $child->getAttribute('class') : '';
                if (strpos($class, 'cv-section-title') !== false) continue;

                if ($tag === 'p') {
                    if (trim($child->textContent) !== '') {
                        $body .= wp(parseInline($child, false, false, '', 10), '', '', 0, 40);
                    }
                } elseif ($tag === 'ul') {
                    if (strpos($class, 'cv-list-2col') !== false) {
                        $liList = $xp->query('.//li', $child);
                        $items  = [];
                        if ($liList) foreach ($liList as $li) $items[] = trim($li->textContent);
                        $body .= build2ColTable($items);
                    } else {
                        foreach ($child->childNodes as $li) {
                            if ($li->nodeType !== XML_ELEMENT_NODE || strtolower($li->nodeName) !== 'li') continue;
                            $body .= wp(
                                wr('▸  ', false, false, '6D155D', 9.5) . parseInline($li, false, false, '', 9.5),
                                '', '', 0, 20, 200
                            );
                        }
                    }
                } elseif ($tag === 'div' && strpos($class, 'cv-job') !== false) {
                    $body .= parseJob($child, $xp);
                }
            }
        }
    }

    return $body ?: '<w:p><w:r><w:t>CV vide</w:t></w:r></w:p>';
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
      <w:sz w:val="20"/><w:szCs w:val="20"/>
      <w:color w:val="1C1C18"/>
      <w:lang w:val="fr-FR"/>
    </w:rPr></w:rPrDefault>
    <w:pPrDefault><w:pPr>
      <w:spacing w:after="0" w:line="240" w:lineRule="auto"/>
    </w:pPr></w:pPrDefault>
  </w:docDefaults>
  <w:style w:type="paragraph" w:styleId="CVSectionTitle">
    <w:name w:val="CV Section Title"/>
    <w:pPr>
      <w:spacing w:before="120" w:after="60"/>
      <w:pBdr>
        <w:bottom w:val="single" w:sz="6" w:space="4" w:color="6D155D"/>
      </w:pBdr>
    </w:pPr>
  </w:style>
</w:styles>';

// ─── Génération et envoi ──────────────────────────────────────────────────

if (!class_exists('ZipArchive')) {
    // Fallback : ancien comportement HTML si ZipArchive indisponible
    $oldFile = str_replace('.docx', '.doc', $filename);
    header('Content-Type: application/msword; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $oldFile . '"');
    header('Cache-Control: no-cache');
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $app['cv_content'] . '</body></html>';
    exit;
}

$bodyXml = htmlToWordBody($app['cv_content']);

$DOC = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
            xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <w:body>
' . $bodyXml . '
    <w:sectPr>
      <w:pgSz w:w="11906" w:h="16838"/>
      <w:pgMar w:top="1020" w:right="1134" w:bottom="1020" w:left="1134"
               w:header="708" w:footer="708" w:gutter="0"/>
    </w:sectPr>
  </w:body>
</w:document>';

$tmp = tempnam(sys_get_temp_dir(), 'cvdocx_');
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
