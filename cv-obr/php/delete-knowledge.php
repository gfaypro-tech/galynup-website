<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$id   = (int)($data['id'] ?? 0);
if ($id <= 0) jsonResponse(['error' => 'ID invalide.'], 400);

$db = getDB();
$db->prepare("UPDATE obr_knowledge SET is_active = 0 WHERE id = ?")->execute([$id]);
jsonResponse(['success' => true]);
