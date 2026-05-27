<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); exit('ID invalide.'); }

$db   = getDB();
$stmt = $db->prepare("SELECT * FROM cv_applications WHERE id = ?");
$stmt->execute([$id]);
$app  = $stmt->fetch();
if (!$app || empty($app['cv_content'])) { http_response_code(404); exit('CV introuvable.'); }

$company  = preg_replace('/[^a-zA-Z0-9_-]/', '_', $app['company']);
$position = preg_replace('/[^a-zA-Z0-9_-]/', '_', $app['position']);
$filename = "CV_{$company}_{$position}_" . date('Y-m-d') . ".doc";

// Génération Word (format HTML ouvert par Word)
$wordStyles = '
body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; margin: 2cm; line-height: 1.4; color: #1c1c18; }
h1 { font-size: 20pt; color: #6D155D; margin-bottom: 4px; font-family: Georgia, serif; }
h2 { font-size: 12pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #4a0d3e; border-bottom: 1.5pt solid #D3A625; padding-bottom: 2px; margin-top: 16pt; margin-bottom: 6pt; }
h3 { font-size: 11pt; font-weight: bold; margin-bottom: 2px; }
p { margin: 4pt 0; }
ul { margin: 4pt 0 8pt 14pt; }
li { margin-bottom: 2pt; }
strong { font-weight: bold; }
em { font-style: italic; }
section { max-width: 18cm; }
';

header('Content-Type: application/msword; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache');

echo '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="UTF-8">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Microsoft Word 15">
<!--[if gte mso 9]>
<xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom></w:WordDocument></xml>
<![endif]-->
<style>' . $wordStyles . '</style>
</head>
<body>
' . $app['cv_content'] . '
</body>
</html>';
exit;
