<?php
// Réextrait les questions depuis le matching_result en cas d'erreur de parsing initial
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ../dashboard.php'); exit; }

$db   = getDB();
$stmt = $db->prepare("SELECT * FROM cv_applications WHERE id = ?");
$stmt->execute([$id]);
$app  = $stmt->fetch();

if (!$app || empty($app['matching_result'])) {
    header('Location: ../new-application.php?id=' . $id);
    exit;
}

$parsed = json_decode($app['matching_result'], true);
if (!$parsed || empty($parsed['questions'])) {
    // Pas de questions extraites → retour à l'étape matching
    $db->prepare("UPDATE cv_applications SET step_current = 3, status = 'matching' WHERE id = ?")->execute([$id]);
    header('Location: ../new-application.php?id=' . $id);
    exit;
}

// Supprimer les anciennes questions
$db->prepare("DELETE FROM cv_dialogue WHERE application_id = ?")->execute([$id]);

// Réinsérer
$qStmt = $db->prepare("INSERT INTO cv_dialogue (application_id, question_order, question) VALUES (?, ?, ?)");
foreach ($parsed['questions'] as $q) {
    $qText = trim($q['question'] ?? '');
    $qId   = (int)($q['id'] ?? 0);
    if ($qText) $qStmt->execute([$id, $qId, $qText]);
}

header('Location: ../new-application.php?id=' . $id);
exit;
