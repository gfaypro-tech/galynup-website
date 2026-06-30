<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data           = json_decode(file_get_contents('php://input'), true);
$app_id         = (int)($data['app_id'] ?? 0);
$validationJson = trim($data['validation_result'] ?? '');

if ($app_id <= 0) jsonResponse(['error' => 'Paramètres invalides.'], 400);

$db = getDB();
$db->prepare("UPDATE obr_applications SET validation_result = ?, step_current = 5, status = 'completed', updated_at = NOW() WHERE id = ?")
   ->execute([$validationJson ?: null, $app_id]);

jsonResponse(['success' => true]);
