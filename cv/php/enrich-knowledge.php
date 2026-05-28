<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data         = json_decode(file_get_contents('php://input'), true);
$app_id       = (int)($data['app_id'] ?? 0);
$comp_index   = (int)($data['comp_index'] ?? -1);
$knowledge_id = (int)($data['knowledge_id'] ?? 0);
$competency   = trim($data['competency'] ?? '');
$description  = trim($data['description'] ?? '');
$new_role     = trim($data['new_role'] ?? '');
$new_company  = trim($data['new_company'] ?? '');
$new_period   = trim($data['new_period'] ?? '');

if ($app_id <= 0 || $comp_index < 0 || $competency === '' || $description === '') {
    jsonResponse(['error' => 'Paramètres invalides.'], 400);
}

$db   = getDB();
$stmt = $db->prepare("SELECT enrichment_data FROM cv_applications WHERE id = ?");
$stmt->execute([$app_id]);
$app  = $stmt->fetch();
if (!$app) jsonResponse(['error' => 'Candidature introuvable.'], 404);

$enrichmentData = json_decode($app['enrichment_data'], true) ?? [];
$newLine        = $competency . ' : ' . $description;

if ($knowledge_id > 0) {
    // Enrichir une entrée existante
    $stmt = $db->prepare("SELECT id, content, keywords FROM cv_knowledge WHERE id = ? AND is_active = 1");
    $stmt->execute([$knowledge_id]);
    $entry = $stmt->fetch();
    if (!$entry) jsonResponse(['error' => 'Entrée introuvable.'], 404);

    $newContent  = rtrim($entry['content']) . "\n" . $newLine;
    $existing    = array_filter(array_map('trim', explode(',', $entry['keywords'] ?? '')));
    $existing[]  = mb_strtolower($competency);
    $newKeywords = implode(',', array_unique(array_filter($existing)));

    $db->prepare("UPDATE cv_knowledge SET content = ?, keywords = ?, updated_at = NOW() WHERE id = ?")
       ->execute([$newContent, $newKeywords, $knowledge_id]);
    $resultId = $knowledge_id;
} else {
    // Créer une nouvelle entrée
    $title    = $new_role ?: $competency;
    $meta     = json_encode(['company' => $new_company, 'role' => $new_role, 'period' => $new_period], JSON_UNESCAPED_UNICODE);
    $keywords = mb_strtolower($competency);

    $db->prepare("INSERT INTO cv_knowledge (type, title, content, meta_json, keywords) VALUES ('experience', ?, ?, ?, ?)")
       ->execute([$title, $newLine, $meta, $keywords]);
    $resultId = (int)$db->lastInsertId();
}

// Marquer la compétence comme enrichie
if (isset($enrichmentData[$comp_index])) {
    $enrichmentData[$comp_index]['status']       = 'enriched';
    $enrichmentData[$comp_index]['knowledge_id'] = $resultId;
    $db->prepare("UPDATE cv_applications SET enrichment_data = ? WHERE id = ?")
       ->execute([json_encode($enrichmentData, JSON_UNESCAPED_UNICODE), $app_id]);
}

jsonResponse(['success' => true, 'knowledge_id' => $resultId]);
