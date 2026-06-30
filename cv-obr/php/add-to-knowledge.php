<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
$id   = (int)($data['id'] ?? 0);
if ($id <= 0) jsonResponse(['error' => 'ID invalide.'], 400);

$db = getDB();

// Récupérer la candidature
$stmt = $db->prepare("SELECT * FROM obr_applications WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch();
if (!$app) jsonResponse(['error' => 'Candidature introuvable.'], 404);

// Récupérer les réponses non vides du dialogue
$stmt = $db->prepare("SELECT question, answer FROM obr_dialogue WHERE application_id = ? AND answer != '' AND answer IS NOT NULL ORDER BY question_order");
$stmt->execute([$id]);
$answers = $stmt->fetchAll();
if (empty($answers)) jsonResponse(['error' => 'Aucune réponse à enregistrer.'], 400);

// Construire le contenu à sauvegarder
$content = "Candidature : {$app['position']} chez {$app['company']}\n";
$content .= "Date : " . date('d/m/Y', strtotime($app['created_at'])) . "\n\n";
foreach ($answers as $a) {
    $content .= "Q : {$a['question']}\nR : {$a['answer']}\n\n";
}

$title = "Dialogue — {$app['position']} ({$app['company']})";
$meta  = json_encode(['company' => $app['company'], 'role' => $app['position']], JSON_UNESCAPED_UNICODE);

$ins = $db->prepare("INSERT INTO obr_knowledge (type, title, content, meta_json) VALUES ('experience', ?, ?, ?)");
$ins->execute([$title, trim($content), $meta]);

jsonResponse(['success' => true]);
