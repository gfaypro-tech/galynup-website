<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); die('ID invalide.'); }

if (!defined('PDFSHIFT_API_KEY') || PDFSHIFT_API_KEY === '') {
    die('Clé API PDFShift manquante.');
}

$db   = getDB();
$stmt = $db->prepare("SELECT company, position, letter_content FROM cv_applications WHERE id = ?");
$stmt->execute([$id]);
$app  = $stmt->fetch();
if (!$app || empty($app['letter_content'])) { http_response_code(404); die('Lettre introuvable.'); }

$ascii    = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $app['position'] ?? '');
$slug     = strtoupper(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $ascii)));
$slug     = preg_replace('/\s+/', '_', $slug);
$filename = 'LM_GFAY_' . ($slug ?: 'LM') . '.pdf';

$html = '<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: "DM Sans", system-ui, sans-serif;
  font-size: 11pt; color: #1c1c18; background: #fff;
  padding: 3mm 20mm; line-height: 1.6;
}
.letter-meta { margin-bottom: 12mm; }
.letter-sender { font-size: 10pt; color: #555; }
.letter-date { font-size: 10pt; color: #555; margin-top: 4pt; }
.letter-recipient { margin-bottom: 8mm; font-size: 11pt; }
.letter-object { font-weight: 600; margin-bottom: 8mm; font-size: 11pt; }
.letter-body p { margin: 0 0 8pt; font-size: 11pt; text-align: justify; }
.letter-signature { margin-top: 10mm; font-size: 11pt; }
strong { font-weight: 600; }
em { font-style: italic; }
section#letter { display: block; }
</style>
</head>
<body>' . $app['letter_content'] . '</body>
</html>';

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL            => 'https://api.pdfshift.io/v3/convert/pdf',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode([
        'source'    => $html,
        'format'    => 'A4',
        'margin'    => ['top' => '3mm', 'right' => '0mm', 'bottom' => '3mm', 'left' => '0mm'],
        'use_print' => false,
        'landscape' => false,
    ]),
    CURLOPT_HTTPHEADER     => [
        'X-API-Key: ' . PDFSHIFT_API_KEY,
        'Content-Type: application/json',
    ],
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($curl);
curl_close($curl);

if ($curlErr) die('Erreur réseau : ' . htmlspecialchars($curlErr));
if ($httpCode !== 200) {
    $detail = json_decode($response, true)['message'] ?? substr($response, 0, 300);
    die('Erreur PDFShift (' . $httpCode . ') : ' . htmlspecialchars($detail));
}

$tmpDir = __DIR__ . '/../tmp/';
if (!is_dir($tmpDir)) mkdir($tmpDir, 0755, true);
foreach (glob($tmpDir . '*.pdf') as $f) {
    if (time() - filemtime($f) > 600) @unlink($f);
}
file_put_contents($tmpDir . $filename, $response);

header('Location: ' . CV_BASE_URL . '/tmp/' . rawurlencode($filename));
exit;
