<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data   = json_decode(file_get_contents('php://input'), true);
$app_id = (int)($data['app_id'] ?? 0);
if ($app_id <= 0) jsonResponse(['error' => 'Paramètres invalides.'], 400);

getDB()->prepare("UPDATE cv_applications SET step_current = 4, status = 'generating', updated_at = NOW() WHERE id = ?")
       ->execute([$app_id]);

jsonResponse(['success' => true]);
