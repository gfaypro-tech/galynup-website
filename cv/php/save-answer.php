<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data             = json_decode(file_get_contents('php://input'), true);
$appId            = (int)($data['id'] ?? 0);
$questionId       = (int)($data['question_id'] ?? 0);
$answer           = trim($data['answer'] ?? '');
$saveToKnowledge  = !empty($data['save_to_knowledge']);

if ($appId <= 0 || $questionId <= 0) jsonResponse(['error' => 'Paramètres invalides.'], 400);

$db = getDB();

// Vérifier que la question appartient à cette candidature et récupérer son texte
$stmt = $db->prepare("SELECT id, question FROM cv_dialogue WHERE id = ? AND application_id = ?");
$stmt->execute([$questionId, $appId]);
$qRow = $stmt->fetch();
if (!$qRow) jsonResponse(['error' => 'Question introuvable.'], 404);

$db->prepare("UPDATE cv_dialogue SET answer = ? WHERE id = ?")->execute([$answer, $questionId]);

// Enregistrer dans la base de connaissance si demandé (et réponse non vide)
if ($saveToKnowledge && $answer !== '') {
    $title = mb_substr($qRow['question'], 0, 120);
    if (mb_strlen($qRow['question']) > 120) $title .= '…';
    $db->prepare("INSERT INTO cv_knowledge (type, title, content) VALUES ('experience', ?, ?)")
       ->execute([$title, $answer]);
}

// Vérifier si toutes les questions ont été répondues
$remaining = $db->prepare("SELECT COUNT(*) FROM cv_dialogue WHERE application_id = ? AND answer IS NULL");
$remaining->execute([$appId]);
$countRemaining = (int)$remaining->fetchColumn();

// Si plus aucune question sans réponse → mettre à jour le statut
if ($countRemaining === 0) {
    $db->prepare("UPDATE cv_applications SET updated_at = NOW() WHERE id = ?")->execute([$appId]);
}

jsonResponse(['success' => true, 'remaining' => $countRemaining]);
