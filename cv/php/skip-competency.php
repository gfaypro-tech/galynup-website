<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data       = json_decode(file_get_contents('php://input'), true);
$app_id     = (int)($data['app_id'] ?? 0);
$comp_index = (int)($data['comp_index'] ?? -1);

if ($app_id <= 0 || $comp_index < 0) jsonResponse(['error' => 'Paramètres invalides.'], 400);

$db   = getDB();
$stmt = $db->prepare("SELECT enrichment_data FROM cv_applications WHERE id = ?");
$stmt->execute([$app_id]);
$app  = $stmt->fetch();
if (!$app) jsonResponse(['error' => 'Candidature introuvable.'], 404);

$enrichmentData = json_decode($app['enrichment_data'], true) ?? [];
if (isset($enrichmentData[$comp_index])) {
    $enrichmentData[$comp_index]['status'] = 'skipped';
    $db->prepare("UPDATE cv_applications SET enrichment_data = ? WHERE id = ?")
       ->execute([json_encode($enrichmentData, JSON_UNESCAPED_UNICODE), $app_id]);
}

jsonResponse(['success' => true]);
