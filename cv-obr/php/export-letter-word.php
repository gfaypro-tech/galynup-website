<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit('ID invalide.'); }

$db   = getDB();
$stmt = $db->prepare("SELECT company, position, letter_content FROM obr_applications WHERE id = ?");
$stmt->execute([$id]);
$app  = $stmt->fetch();
if (!$app || empty($app['letter_content'])) { http_response_code(404); exit('Lettre introuvable.'); }

$ascii    = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $app['position'] ?? '');
$slug     = strtoupper(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $ascii)));
$slug     = preg_replace('/\s+/', '_', $slug);
$filename = 'LM_GFAY_' . ($slug ?: 'LM') . '_' . date('Y-m-d') . '.doc';

$styles = '
body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; margin: 2.5cm 2.5cm; line-height: 1.6; color: #1c1c18; }
.letter-meta { margin-bottom: 24pt; }
.letter-sender { font-size: 10pt; color: #555; }
.letter-date { font-size: 10pt; color: #555; margin-top: 4pt; }
.letter-recipient { margin-bottom: 16pt; font-size: 11pt; }
.letter-object { font-weight: bold; margin-bottom: 20pt; font-size: 11pt; }
.letter-body p { margin: 0 0 12pt; font-size: 11pt; text-align: justify; }
.letter-signature { margin-top: 24pt; font-size: 11pt; }
strong { font-weight: bold; }
em { font-style: italic; }
';

header('Content-Type: application/msword; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache');

echo '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="UTF-8">
<meta name=ProgId content=Word.Document>
<!--[if gte mso 9]>
<xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom></w:WordDocument></xml>
<![endif]-->
<style>' . $styles . '</style>
</head>
<body>' . $app['letter_content'] . '</body>
</html>';
exit;
