<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data    = json_decode(file_get_contents('php://input'), true);
$id      = (int)($data['id'] ?? 0);
$content = trim($data['content'] ?? '');

if ($id <= 0 || $content === '') jsonResponse(['error' => 'Paramètres invalides.'], 400);

// Extraire la section <section id="letter"> si Claude a retourné du texte autour
if (preg_match('/<section[^>]*id=["\']letter["\'][^>]*>([\s\S]*?)<\/section>/i', $content, $m)) {
    $content = '<section id="letter">' . $m[1] . '</section>';
}

$db = getDB();
$db->prepare("UPDATE cv_applications SET letter_content = ?, updated_at = NOW() WHERE id = ?")
   ->execute([$content, $id]);

jsonResponse(['success' => true]);
