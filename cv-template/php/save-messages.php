<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data    = json_decode(file_get_contents('php://input'), true);
$id      = (int)($data['id']      ?? 0);
$lettre  = trim($data['lettre']   ?? '');
$linkedin = trim($data['linkedin'] ?? '');
$malt    = trim($data['malt']     ?? '');

if ($id <= 0 || ($lettre === '' && $linkedin === '' && $malt === '')) {
    jsonResponse(['error' => 'Paramètres invalides.'], 400);
}

// Extraire uniquement la balise <section id="letter"> si Claude a ajouté du texte autour
if ($lettre !== '' && preg_match('/<section[^>]*id=["\']letter["\'][^>]*>([\s\S]*?)<\/section>/i', $lettre, $m)) {
    $lettre = '<section id="letter">' . $m[1] . '</section>';
}

$lettersJson = json_encode(
    ['linkedin' => $linkedin, 'malt' => $malt],
    JSON_UNESCAPED_UNICODE
);

$db = getDB();
$db->prepare("UPDATE cv_applications SET letter_content = ?, letters_json = ?, updated_at = NOW() WHERE id = ?")
   ->execute([$lettre ?: null, $lettersJson, $id]);

jsonResponse(['success' => true]);
