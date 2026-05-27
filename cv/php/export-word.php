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
body        { font-family: Calibri, Arial, sans-serif; font-size: 10pt; margin: 1.8cm 2cm; line-height: 1.45; color: #1c1c18; }

/* Header */
.cv-header  { text-align: center; margin-bottom: 14pt; }
h1          { font-family: Georgia, "Times New Roman", serif; font-size: 20pt; font-weight: bold; letter-spacing: 2pt; color: #1c1c18; text-transform: uppercase; margin: 0 0 5pt; }
.cv-subtitle{ font-size: 12pt; font-weight: bold; color: #6D155D; margin: 0 0 4pt; }
.cv-tagline { font-size: 9pt; color: #555; margin: 0 0 3pt; }
.cv-contact { font-size: 9pt; color: #666; margin: 0; }

/* Sections */
.cv-section      { margin-bottom: 12pt; }
.cv-section-title{
  font-size: 8.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1pt;
  color: #6D155D; border-bottom: 0.75pt solid #6D155D; padding-bottom: 2pt; margin: 0 0 7pt;
}
p { margin: 0 0 4pt; font-size: 10pt; }

/* Listes 2 colonnes (Certifications / Compétences) — colonnes CSS supportées par Word */
.cv-list-2col { list-style: none; padding: 0; margin: 0; -mso-columns: 2; columns: 2; column-gap: 20pt; font-size: 9.5pt; }
.cv-list-2col li { margin-bottom: 2pt; }
.cv-list-2col li::before { content: "\25B8  "; color: #6D155D; }

/* Expériences */
.cv-job        { margin-bottom: 10pt; }
.cv-job-header { display: flex; justify-content: space-between; }
.cv-job-title  { font-size: 10pt; font-weight: bold; }
.cv-job-date   { font-size: 9pt; color: #555; }
.cv-job-context{ font-size: 9pt; color: #555; font-style: italic; margin: 1pt 0 4pt; }
.cv-job-bullets{ list-style: none; padding: 0; margin: 0; }
.cv-job-bullets li { font-size: 9.5pt; margin-bottom: 2pt; padding-left: 12pt; }
.cv-job-bullets li::before { content: "\25B8  "; color: #6D155D; }
.cv-job-note   { font-size: 9pt; color: #777; font-style: italic; margin-top: 2pt; }
strong { font-weight: bold; }
em     { font-style: italic; }
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
