<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) jsonResponse(['error' => 'Données invalides.'], 400);

$id = (int)($data['id'] ?? 0);
if ($id <= 0) jsonResponse(['error' => 'ID invalide.'], 400);

$content = trim($data['content'] ?? '');
if ($content === '') jsonResponse(['error' => 'Le contenu est obligatoire.'], 400);

$type  = in_array($data['type'] ?? '', ['import','experience','competence','formation','autre'])
         ? $data['type'] : 'autre';
$title = trim($data['title'] ?? '');

$meta         = null;
$period_start = null;
if ($type === 'experience') {
    $period = trim($data['period'] ?? '');
    $meta   = json_encode([
        'company' => trim($data['company'] ?? ''),
        'role'    => trim($data['role'] ?? ''),
        'period'  => $period,
    ], JSON_UNESCAPED_UNICODE);
    if (preg_match('/(\d{4})/', $period, $m)) {
        $period_start = (int)$m[1];
    }
}

$db = getDB();
$db->prepare("UPDATE obr_knowledge SET type = ?, title = ?, content = ?, meta_json = ?, period_start = ? WHERE id = ? AND is_active = 1")
   ->execute([$type, $title, $content, $meta, $period_start, $id]);

jsonResponse(['success' => true]);
