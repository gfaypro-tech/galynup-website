<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data          = json_decode(file_get_contents('php://input'), true);
$app_id        = (int)($data['app_id'] ?? 0);
$comp_index    = (int)($data['comp_index'] ?? -1);
$knowledge_ids = array_map('intval', $data['knowledge_ids'] ?? []);
$competency    = trim($data['competency'] ?? '');
$description   = trim($data['description'] ?? '');
$new_role      = trim($data['new_role'] ?? '');
$new_company   = trim($data['new_company'] ?? '');
$new_period    = trim($data['new_period'] ?? '');
$transversal   = !empty($data['transversal']);

$has_new = ($new_role !== '' || $new_company !== '');

if ($app_id <= 0 || $comp_index < 0 || $competency === '') {
    jsonResponse(['error' => 'Paramètres invalides.'], 400);
}
if (!$transversal && $description === '') {
    jsonResponse(['error' => 'Paramètres invalides.'], 400);
}
if (!$transversal && empty($knowledge_ids) && !$has_new) {
    jsonResponse(['error' => 'Aucune expérience sélectionnée.'], 400);
}

$db   = getDB();
$stmt = $db->prepare("SELECT enrichment_data FROM cv_applications WHERE id = ?");
$stmt->execute([$app_id]);
$app  = $stmt->fetch();
if (!$app) jsonResponse(['error' => 'Candidature introuvable.'], 404);

$enrichmentData = json_decode($app['enrichment_data'], true) ?? [];
$newLine        = $competency . ' : ' . $description;
$resultIds      = [];

// ── Enrichir les entrées existantes sélectionnées ─────────────────────────
foreach ($knowledge_ids as $kid) {
    if ($kid <= 0) continue;
    $stmt = $db->prepare("SELECT id, content, keywords FROM cv_knowledge WHERE id = ? AND is_active = 1");
    $stmt->execute([$kid]);
    $entry = $stmt->fetch();
    if (!$entry) continue;

    $newContent  = rtrim($entry['content']) . "\n" . $newLine;
    $existing    = array_filter(array_map('trim', explode(',', $entry['keywords'] ?? '')));
    $existing[]  = mb_strtolower($competency);
    $newKeywords = implode(',', array_unique(array_filter($existing)));

    $db->prepare("UPDATE cv_knowledge SET content = ?, keywords = ?, updated_at = NOW() WHERE id = ?")
       ->execute([$newContent, $newKeywords, $kid]);
    $resultIds[] = $kid;
}

// ── Créer une nouvelle entrée si demandé ──────────────────────────────────
if ($has_new) {
    $title        = $new_role ?: $competency;
    $meta         = json_encode(['company' => $new_company, 'role' => $new_role, 'period' => $new_period], JSON_UNESCAPED_UNICODE);
    $keywords     = mb_strtolower($competency);
    $period_start = null;
    if (preg_match('/(\d{4})/', $new_period, $pm)) {
        $period_start = (int)$pm[1];
    }

    $db->prepare("INSERT INTO cv_knowledge (type, title, content, meta_json, keywords, period_start) VALUES ('experience', ?, ?, ?, ?, ?)")
       ->execute([$title, $newLine, $meta, $keywords, $period_start]);
    $resultIds[] = (int)$db->lastInsertId();
}

// ── Créer une entrée transversale (non liée à une expérience) ────────────
if ($transversal) {
    $content  = $competency . ' : ' . ($description !== ''
        ? $description
        : 'Compétence acquise progressivement tout au long du parcours professionnel.');
    $meta     = json_encode(['transversal' => true], JSON_UNESCAPED_UNICODE);
    $keywords = mb_strtolower($competency);
    $db->prepare("INSERT INTO cv_knowledge (type, title, content, meta_json, keywords) VALUES ('competence', ?, ?, ?, ?)")
       ->execute([$competency, $content, $meta, $keywords]);
    $resultIds[] = (int)$db->lastInsertId();
}

// ── Marquer la compétence comme enrichie ──────────────────────────────────
if (isset($enrichmentData[$comp_index])) {
    $enrichmentData[$comp_index]['status']        = 'enriched';
    $enrichmentData[$comp_index]['knowledge_ids'] = $resultIds;
    $db->prepare("UPDATE cv_applications SET enrichment_data = ? WHERE id = ?")
       ->execute([json_encode($enrichmentData, JSON_UNESCAPED_UNICODE), $app_id]);
}

jsonResponse(['success' => true, 'knowledge_ids' => $resultIds]);
