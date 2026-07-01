<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) jsonResponse(['error' => 'Données invalides.'], 400);

$company          = trim($data['company'] ?? '');
$position         = trim($data['position'] ?? '');
$source_url       = trim($data['source_url'] ?? '');
$date_candidature = trim($data['date_candidature'] ?? '');
$hiring_status    = trim($data['hiring_status'] ?? 'non_envoye');

if ($company === '' || $position === '') {
    jsonResponse(['error' => 'L\'entreprise et le poste sont obligatoires.'], 400);
}

$allowed_hiring = ['non_envoye','envoye','repondu','relance','entretien','offre','refuse','abandon'];
if (!in_array($hiring_status, $allowed_hiring)) {
    $hiring_status = 'non_envoye';
}

if ($date_candidature !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_candidature)) {
    $date_candidature = '';
}

$db = getDB();
$stmt = $db->prepare(
    "INSERT INTO cv_applications (company, position, source_url, date_candidature, step_current, status, hiring_status)
     VALUES (?, ?, ?, ?, 1, 'direct', ?)"
);
$stmt->execute([$company, $position, $source_url ?: null, $date_candidature ?: null, $hiring_status]);

jsonResponse(['success' => true, 'id' => (int)$db->lastInsertId()]);
