<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$id   = (int)($data['id'] ?? 0);
$val  = $data['avancement'] ?? '';

$allowed = ['en_cours', 'a_relancer', 'cloture'];
if ($id <= 0 || !in_array($val, $allowed)) {
    jsonResponse(['error' => 'Paramètres invalides.'], 400);
}

$db = getDB();
$db->prepare("UPDATE cv_applications SET avancement = ? WHERE id = ?")
   ->execute([$val, $id]);

jsonResponse(['success' => true]);
