<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data       = json_decode(file_get_contents('php://input'), true);
$appId      = (int)($data['id'] ?? 0);
$questionId = (int)($data['question_id'] ?? 0);
$answer     = trim($data['answer'] ?? '');

if ($appId <= 0 || $questionId <= 0) jsonResponse(['error' => 'Paramètres invalides.'], 400);

$db = getDB();

// Vérifier que la question appartient à cette candidature
$stmt = $db->prepare("SELECT id FROM obr_dialogue WHERE id = ? AND application_id = ?");
$stmt->execute([$questionId, $appId]);
if (!$stmt->fetch()) jsonResponse(['error' => 'Question introuvable.'], 404);

$db->prepare("UPDATE obr_dialogue SET answer = ? WHERE id = ?")->execute([$answer, $questionId]);

// Vérifier si toutes les questions ont été répondues
$remaining = $db->prepare("SELECT COUNT(*) FROM obr_dialogue WHERE application_id = ? AND answer IS NULL");
$remaining->execute([$appId]);
$countRemaining = (int)$remaining->fetchColumn();

// Si plus aucune question sans réponse → mettre à jour le statut
if ($countRemaining === 0) {
    $db->prepare("UPDATE obr_applications SET updated_at = NOW() WHERE id = ?")->execute([$appId]);
}

jsonResponse(['success' => true, 'remaining' => $countRemaining]);
