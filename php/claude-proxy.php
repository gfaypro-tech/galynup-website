<?php
/**
 * Proxy Claude API — réservé aux utilisateurs premium.
 * La clé API Anthropic est stockée côté serveur dans config.php.
 */

session_start();
header('Content-Type: application/json');

// Vérification accès premium
if (
    !isset($_SESSION['simulateur_access']) ||
    $_SESSION['simulateur_access'] !== true ||
    ($_SESSION['simulateur_level'] ?? '') !== 'premium'
) {
    http_response_code(403);
    echo json_encode(['error' => 'Accès premium requis.']);
    exit;
}

// Lecture du body JSON
$body = json_decode(file_get_contents('php://input'), true);
$prompt = $body['prompt'] ?? '';

if (empty($prompt)) {
    http_response_code(400);
    echo json_encode(['error' => 'Prompt manquant.']);
    exit;
}

require_once __DIR__ . '/config.php';

if (!defined('ANTHROPIC_API_KEY')) {
    http_response_code(500);
    echo json_encode(['error' => 'Clé API non configurée.']);
    exit;
}

// Appel Anthropic
$payload = json_encode([
    'model'      => 'claude-sonnet-4-6',
    'max_tokens' => 1500,
    'messages'   => [['role' => 'user', 'content' => $prompt]],
]);

$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'x-api-key: ' . ANTHROPIC_API_KEY,
        'anthropic-version: 2023-06-01',
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(502);
    echo json_encode(['error' => 'Erreur API Anthropic (' . $httpCode . ').']);
    exit;
}

$data = json_decode($response, true);
$text = implode('', array_map(fn($b) => $b['text'] ?? '', $data['content'] ?? []));

echo json_encode(['content' => $text]);
