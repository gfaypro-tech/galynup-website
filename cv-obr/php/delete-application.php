<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$id   = (int)($data['id'] ?? 0);
if ($id <= 0) jsonResponse(['error' => 'ID invalide.'], 400);

$db = getDB();
// Les obr_dialogue sont supprimés en cascade (ON DELETE CASCADE)
$db->prepare("DELETE FROM obr_applications WHERE id = ?")->execute([$id]);
jsonResponse(['success' => true]);
