<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data   = json_decode(file_get_contents('php://input'), true);
$id     = (int)($data['id'] ?? 0);
$status = $data['hiring_status'] ?? '';

$allowed = ['non_envoye', 'envoye', 'repondu', 'entretien', 'offre', 'refuse', 'abandon'];
if ($id <= 0 || !in_array($status, $allowed)) {
    jsonResponse(['error' => 'Paramètres invalides.'], 400);
}

$db = getDB();
$db->prepare("UPDATE cv_applications SET hiring_status = ? WHERE id = ?")
   ->execute([$status, $id]);

jsonResponse(['success' => true]);
