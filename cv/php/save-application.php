<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) jsonResponse(['error' => 'Données invalides.'], 400);

$company     = trim($data['company'] ?? '');
$position    = trim($data['position'] ?? '');
$job_posting = trim($data['job_posting'] ?? '');

if ($company === '' || $position === '' || $job_posting === '') {
    jsonResponse(['error' => 'Tous les champs sont obligatoires.'], 400);
}

$db  = getDB();
$id  = (int)($data['id'] ?? 0);

if ($id > 0) {
    // Mise à jour (retour en étape 1 pour corriger)
    $stmt = $db->prepare("UPDATE cv_applications SET company = ?, position = ?, job_posting = ?, step_current = 2, status = 'analysis', updated_at = NOW() WHERE id = ?");
    $stmt->execute([$company, $position, $job_posting, $id]);
    jsonResponse(['success' => true, 'id' => $id]);
} else {
    // Nouvelle candidature
    $stmt = $db->prepare("INSERT INTO cv_applications (company, position, job_posting, step_current, status) VALUES (?, ?, ?, 2, 'analysis')");
    $stmt->execute([$company, $position, $job_posting]);
    jsonResponse(['success' => true, 'id' => (int)$db->lastInsertId()]);
}
