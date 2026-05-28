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
if (str_contains($url, 'linkedin.com')) {
    if (preg_match('/^(.+?)\s*[-–]\s*(.+?)\s*\|/', $rawTitle, $m)) {
        $position = trim($m[1]);
        $company  = trim($m[2]);
    } elseif (preg_match('/^(.+?)\s+chez\s+(.+?)(?:\s*\||$)/i', $rawTitle, $m)) {
        $position = trim($m[1]);
        $company  = trim($m[2]);
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

// ── Extraction du texte de l'annonce ─────────────────
// Supprimer les balises inutiles
foreach (['script','style','nav','header','footer','noscript','aside','iframe'] as $tag) {
    $nodes = $dom->getElementsByTagName($tag);
    while ($nodes->length > 0) {
        $nodes->item(0)->parentNode->removeChild($nodes->item(0));
    }
}

// Chercher la section principale de l'annonce (sélecteurs courants)
$jobText   = '';
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
