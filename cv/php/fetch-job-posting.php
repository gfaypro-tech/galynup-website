<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$url  = trim($data['url'] ?? '');

if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
    jsonResponse(['error' => 'URL invalide.'], 400);
}

// ── Fetch de la page ─────────────────────────────────
$html = '';
if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_TIMEOUT        => 12,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER     => [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: fr-FR,fr;q=0.9,en;q=0.8',
        ],
        CURLOPT_ENCODING       => 'gzip, deflate',
    ]);
    $html     = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if (!$html || $httpCode >= 400) {
        jsonResponse(['error' => 'Page inaccessible (code ' . $httpCode . '). Colle l\'annonce manuellement.'], 400);
    }
} else {
    $ctx  = stream_context_create(['http' => [
        'header'     => "User-Agent: Mozilla/5.0 Chrome/124\r\nAccept-Language: fr-FR,fr;q=0.9\r\n",
        'timeout'    => 12,
    ]]);
    $html = @file_get_contents($url, false, $ctx);
    if (!$html) jsonResponse(['error' => 'Impossible de récupérer la page. Colle l\'annonce manuellement.'], 400);
}

// ── Parsing HTML ─────────────────────────────────────
libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
libxml_clear_errors();
$xpath = new DOMXPath($dom);

// Titre du poste + entreprise depuis les meta tags
$position = '';
$company  = '';

$ogTitle = $xpath->query('//meta[@property="og:title"]/@content');
$rawTitle = $ogTitle->length > 0
    ? trim($ogTitle->item(0)->value)
    : trim($dom->getElementsByTagName('title')->item(0)?->textContent ?? '');

// LinkedIn : "Position - Entreprise | LinkedIn" ou "Position chez Entreprise"
// ou "Entreprise recrute [pour des postes de] Position"
if (str_contains($url, 'linkedin.com')) {
    if (preg_match('/^(.+?)\s+[-–]\s+(.+?)\s*\|/', $rawTitle, $m)) {
        $position = trim($m[1]);
        $company  = trim($m[2]);
    } elseif (preg_match('/^(.+?)\s+chez\s+(.+?)(?:\s*\||$)/i', $rawTitle, $m)) {
        $position = trim($m[1]);
        $company  = trim($m[2]);
    } elseif (preg_match(
        '/^(.+?)\s+recrute(?:\s+pour\s+(?:des?\s+)?postes?\s+de)?\s+(?:un(?:e)?\s+)?(.+?)(?:\s*[(\[]|\s*[|–\-]|$)/iu',
        $rawTitle, $m
    )) {
        $company  = trim($m[1]);
        // Nettoyer : "(Ville)", "[F/H]", "H/F", "F/H" en fin de chaîne
        $position = trim(preg_replace('/\s*[\(\[].*?[\)\]]|\s+[HFhf]\/[HFhf]\s*$/u', '', trim($m[2])));
    } else {
        $position = $rawTitle;
    }
} else {
    // Autres plateformes : séparateur " - " ou ":"
    if (preg_match('/^(.+?)\s*[-–:]\s*(.+?)(?:\s*\||$)/', $rawTitle, $m)) {
        $position = trim($m[1]);
        $company  = trim($m[2]);
    } else {
        $position = $rawTitle;
    }
    // og:site_name si disponible
    $ogSite = $xpath->query('//meta[@property="og:site_name"]/@content');
    if ($ogSite->length > 0) {
        $site = trim($ogSite->item(0)->value);
        if (!in_array(strtolower($site), ['linkedin', 'indeed', 'apec', 'hellowork']) && !empty($site)) {
            $company = $site;
        }
    }
}

// ── Fallback "TOTO recrute MOMO" sur le titre brut ───
if (empty($company) || empty($position)) {
    if (preg_match(
        '/^(.+?)\s+recrute(?:\s+pour\s+(?:des?\s+)?postes?\s+de|\s*:)?\s+(?:un(?:e)?\s+)?(.{5,120}?)(?:\s*[(\[]|\s*[|–\-]|$)/iu',
        $rawTitle,
        $m
    )) {
        if (empty($company))  $company  = trim($m[1]);
        if (empty($position)) $position = trim(preg_replace('/\s*[\(\[].*?[\)\]]|\s+[HFhf]\/[HFhf]\s*$/u', '', trim($m[2])));
    }
}

// ── Extraction via JSON-LD (contenu complet, pas tronqué par "voir plus") ──
$jobText = '';
$jsonldNodes = $xpath->query('//script[@type="application/ld+json"]');
for ($i = 0; $i < $jsonldNodes->length; $i++) {
    $raw = trim($jsonldNodes->item($i)->textContent);
    $decoded = json_decode($raw, true);
    if (!$decoded) continue;

    // Peut être un tableau ou un objet direct
    $items = isset($decoded[0]) ? $decoded : [$decoded];
    foreach ($items as $item) {
        $type = $item['@type'] ?? '';
        if (is_array($type)) $type = implode(',', $type);
        if (stripos($type, 'JobPosting') !== false) {
            $desc = $item['description'] ?? '';
            // Nettoyer le HTML éventuel dans la description
            $desc = strip_tags(html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if (mb_strlen($desc) > 200) {
                $jobText = $desc;

                // Récupérer position/entreprise depuis JSON-LD si pas encore trouvés
                if (empty($position)) $position = trim($item['title'] ?? '');
                if (empty($company))  $company  = trim($item['hiringOrganization']['name'] ?? ($item['hiringOrganization'] ?? ''));
                if (is_array($company)) $company = '';
                break 2;
            }
        }
    }
}

// ── Extraction du texte de l'annonce (fallback si pas de JSON-LD) ─────────
// Supprimer les balises inutiles
foreach (['script','style','nav','header','footer','noscript','aside','iframe'] as $tag) {
    $nodes = $dom->getElementsByTagName($tag);
    while ($nodes->length > 0) {
        $nodes->item(0)->parentNode->removeChild($nodes->item(0));
    }
}

// Chercher la section principale de l'annonce (sélecteurs courants)
$selectors = [
    '//div[contains(@class,"description__text")]',        // LinkedIn
    '//div[contains(@class,"show-more-less-html")]',      // LinkedIn
    '//section[contains(@class,"description")]',          // LinkedIn
    '//div[@id="job-details"]',                           // LinkedIn
    '//div[contains(@class,"job-description")]',          // générique
    '//div[contains(@class,"jobDescription")]',           // Indeed
    '//div[@id="jobDescriptionText"]',                    // Indeed
    '//div[contains(@class,"job_description")]',          // HelloWork
    '//article[contains(@class,"job")]',                  // générique
    '//main',                                             // fallback
];

foreach ($selectors as $sel) {
    if (!empty($jobText)) break;
    $nodes = $xpath->query($sel);
    if ($nodes->length > 0) {
        $raw = trim($nodes->item(0)->textContent);
        if (mb_strlen($raw) > 300) {
            $jobText = $raw;
            break;
        }
    }
}

// Fallback : tout le body
if (empty($jobText)) {
    $body = $dom->getElementsByTagName('body')->item(0);
    if ($body) $jobText = trim($body->textContent);
}

// Nettoyage du texte
$jobText = preg_replace('/[ \t]{2,}/', ' ', $jobText);
$jobText = preg_replace('/\n{3,}/', "\n\n", $jobText);
$jobText = preg_replace('/^\s+|\s+$/m', '', $jobText);
$jobText = trim($jobText);

// ── Fallback "TOTO recrute MOMO" sur les premières lignes du texte ───
if ((empty($company) || empty($position)) && !empty($jobText)) {
    $firstLines = implode("\n", array_slice(explode("\n", $jobText), 0, 15));
    if (preg_match(
        '/([\w\s\-&\'\.À-ÿ]{2,60}?)\s+recrute(?:\s*:)?\s+(?:un(?:e)?\s+)?(.{5,120}?)(?:\n|[|\-–]|$)/iu',
        $firstLines,
        $m
    )) {
        if (empty($company))  $company  = trim($m[1]);
        if (empty($position)) $position = trim(preg_replace('/^un(?:e)?\s+/iu', '', trim($m[2])));
    }
}

if (empty($position) && empty($jobText)) {
    jsonResponse(['error' => 'Contenu introuvable. La page nécessite peut-être une connexion.'], 400);
}

// Limiter la taille (sécurité)
if (mb_strlen($jobText) > 20000) {
    $jobText = mb_substr($jobText, 0, 20000) . "\n[…]";
}

jsonResponse([
    'success'     => true,
    'position'    => $position,
    'company'     => $company,
    'job_posting' => $jobText,
]);
