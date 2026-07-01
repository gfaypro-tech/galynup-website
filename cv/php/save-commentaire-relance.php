<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data        = json_decode(file_get_contents('php://input'), true);
$id          = (int)($data['id'] ?? 0);
$commentaire = trim($data['commentaire'] ?? '');

if ($id <= 0) {
    jsonResponse(['error' => 'Paramètres invalides.'], 400);
}

$db = getDB();
$db->prepare("UPDATE cv_applications SET commentaire_relance = ? WHERE id = ?")
   ->execute([$commentaire ?: null, $id]);

jsonResponse(['success' => true]);
