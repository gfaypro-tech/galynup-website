<?php
ini_set('zlib.output_compression', 'Off'); // désactiver la compression gzip sur le binaire PDF
ob_start(); // capturer tout output parasite (warnings, notices)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); die('ID invalide.'); }

if (!defined('PDFSHIFT_API_KEY') || PDFSHIFT_API_KEY === '') {
    http_response_code(500);
    die('Clé API PDFShift manquante — remplis PDFSHIFT_API_KEY dans config.php.');
}

$db   = getDB();
$stmt = $db->prepare("SELECT cv_content, position FROM cv_applications WHERE id = ? AND status = 'completed'");
$stmt->execute([$id]);
$app  = $stmt->fetch();
if (!$app) { http_response_code(404); die('CV introuvable ou non finalisé.'); }

// ── Nom du fichier ──────────────────────────────────────────────────
$pos      = $app['position'] ?? '';
$ascii    = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $pos);
$clean    = strtoupper(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $ascii)));
$slug     = preg_replace('/\s+/', '_', $clean);
$filename = 'CV_GFAY_' . ($slug ?: 'CV') . '.pdf';

// ── HTML complet pour PDFShift ──────────────────────────────────────
$html = '<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: "DM Sans", system-ui, sans-serif;
  font-size: 11.5pt;
  color: #1c1c18;
  background: #fff;
  padding: 12mm 14mm;
  line-height: 1.55;
}
.cv-header { text-align: center; margin-bottom: 18px; }
h1 {
  font-family: "Cormorant Garamond", Georgia, serif;
  font-size: 26px; font-weight: 700;
  letter-spacing: 2px; color: #1c1c18;
  margin: 0 0 6px; text-transform: uppercase;
}
.cv-subtitle { font-size: 13.5px; font-weight: 600; color: #6D155D; margin: 0 0 5px; }
.cv-tagline  { font-size: 11px; color: #555; margin: 0 0 4px; }
.cv-contact  { font-size: 11px; color: #666; margin: 0; }
.cv-section  { margin-bottom: 15px; }
.cv-section-title {
  font-size: 9.5px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.7px;
  color: #6D155D; border-bottom: 1px solid #6D155D;
  padding-bottom: 3px; margin: 0 0 9px;
}
p { margin: 0 0 6px; font-size: 12px; line-height: 1.55; }
.cv-list-2col {
  list-style: none; padding: 0; margin: 0;
  font-size: 11.5px; overflow: hidden;
}
.cv-list-2col li {
  position: relative; padding-left: 13px;
  float: left; width: 50%; margin-bottom: 3px;
}
.cv-list-2col li::before { content: "\25B8"; color: #6D155D; position: absolute; left: 0; }
.cv-list-2col::after { content: ""; display: table; clear: both; }
.cv-job { margin-bottom: 12px; }
.cv-job-header { overflow: hidden; }
.cv-job-title  { float: left; font-size: 12px; }
.cv-job-date   { float: right; font-size: 11px; color: #555; }
.cv-job-header::after { content: ""; display: table; clear: both; }
.cv-job-context { font-size: 11px; color: #555; margin: 2px 0 5px; font-style: italic; }
.cv-job-bullets { list-style: none; padding: 0; margin: 0; }
.cv-job-bullets li {
  font-size: 11.5px; padding-left: 14px;
  margin-bottom: 3px; position: relative; line-height: 1.5;
}
.cv-job-bullets li::before { content: "\25B8"; color: #6D155D; position: absolute; left: 0; }
.cv-job-note { font-size: 11px; color: #777; font-style: italic; margin-top: 3px; }
strong { font-weight: 600; }
em { font-style: italic; }
section#cv { display: block; }
</style>
</head>
<body>' . $app['cv_content'] . '</body>
</html>';

// ── Appel API PDFShift ──────────────────────────────────────────────
$payload = json_encode([
    'source'   => $html,
    'format'   => 'A4',
    'margin'   => ['top' => '12mm', 'right' => '14mm', 'bottom' => '12mm', 'left' => '14mm'],
    'filename' => $filename,
    'sandbox'  => false,
]);

$ch = curl_init('https://api.pdfshift.io/v3/convert/pdf');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode('api:' . PDFSHIFT_API_KEY),
    ],
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    ob_end_clean();
    http_response_code(500);
    die('Erreur réseau : ' . htmlspecialchars($curlErr));
}
if ($httpCode !== 200) {
    ob_end_clean();
    http_response_code(502);
    $decoded = json_decode($response, true);
    $detail  = $decoded['error'] ?? $decoded['message'] ?? substr($response, 0, 300);
    die('Erreur PDFShift (' . $httpCode . ') : ' . htmlspecialchars($detail));
}

// ── Envoyer le PDF au navigateur ────────────────────────────────────
ob_end_clean(); // vider tout output parasite accumulé
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Cache-Control: no-store, no-cache');
header('Pragma: no-cache');
// Pas de Content-Length : évite les conflits avec la compression OVH
echo $response;
