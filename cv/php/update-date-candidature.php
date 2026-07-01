<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$id   = (int)($data['id'] ?? 0);
$date = trim($data['date_candidature'] ?? '');

if ($id <= 0) {
    jsonResponse(['error' => 'Paramètres invalides.'], 400);
}

if ($date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    jsonResponse(['error' => 'Format de date invalide.'], 400);
}

$db = getDB();
$db->prepare("UPDATE cv_applications SET date_candidature = ? WHERE id = ?")
   ->execute([$date ?: null, $id]);

jsonResponse(['success' => true]);
