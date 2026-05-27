<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) jsonResponse(['error' => 'Données invalides.'], 400);

$content = trim($data['content'] ?? '');
if ($content === '') jsonResponse(['error' => 'Le contenu est obligatoire.'], 400);

$type  = in_array($data['type'] ?? '', ['import','experience','competence','formation','autre'])
         ? $data['type'] : 'autre';
$title = trim($data['title'] ?? '');

// Métadonnées structurées pour les expériences
$meta = null;
if ($type === 'experience') {
    $meta = json_encode([
        'company' => trim($data['company'] ?? ''),
        'role'    => trim($data['role'] ?? ''),
        'period'  => trim($data['period'] ?? ''),
    ], JSON_UNESCAPED_UNICODE);
}

$db = getDB();
$stmt = $db->prepare("INSERT INTO cv_knowledge (type, title, content, meta_json) VALUES (?, ?, ?, ?)");
$stmt->execute([$type, $title, $content, $meta]);

jsonResponse(['success' => true, 'id' => (int)$db->lastInsertId()]);
