<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$name    = isset($_POST['name'])    ? trim($_POST['name'])    : '';
$email   = isset($_POST['email'])   ? trim($_POST['email'])   : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$auditRaw = isset($_POST['audit_data']) ? $_POST['audit_data'] : '';

if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nom et email requis']);
    exit;
}

$name  = str_replace(["\r", "\n"], '', $name);
$email = str_replace(["\r", "\n"], '', $email);

// Décoder les résultats du diagnostic
$audit = $auditRaw ? @json_decode($auditRaw, true) : null;

// Corps de l'email
$body  = "Demande d'analyse approfondie — Audit Maturité DSI\n\n";
$body .= "Nom    : $name\n";
$body .= "Email  : $email\n";
if ($message) $body .= "Message: $message\n";
$body .= "\n";

if ($audit) {
    $ctx = $audit['context'] ?? [];
    $body .= "========================================\n";
    $body .= "CONTEXTE DU DIAGNOSTIC\n";
    $body .= "========================================\n";
    if (!empty($ctx['client']))   $body .= "Organisation : {$ctx['client']}\n";
    if (!empty($ctx['secteur']))  $body .= "Secteur      : {$ctx['secteur']}\n";
    if (!empty($ctx['effectif'])) $body .= "Effectif DSI : {$ctx['effectif']}\n";
    if (!empty($ctx['enjeux']))   $body .= "Enjeux       : {$ctx['enjeux']}\n";

    $body .= "\n========================================\n";
    $body .= "RÉSULTATS DU DIAGNOSTIC\n";
    $body .= "========================================\n";
    $body .= "Score global : " . ($audit['globalScore'] ?? 'N/A') . " / 5\n\n";

    $scoreLabels = [0=>'N/A',1=>'Initial',2=>'Défini',3=>'Géré',4=>'Optimisé',5=>'Excellence'];
    if (!empty($audit['scores'])) {
        foreach ($audit['scores'] as $axe => $score) {
            $rounded = (int) round((float) $score);
            $label   = $scoreLabels[$rounded] ?? '';
            $body   .= sprintf("  %-35s %s / 5  (%s)\n", $axe, $score, $label);
        }
    }
    $body .= "========================================\n";
}

$body .= "\nDate : " . date('d/m/Y H:i:s') . "\n";

// En-têtes
$configFile = __DIR__ . '/config.php';
if (file_exists($configFile)) require_once $configFile;
$from = defined('NOTIFICATION_EMAIL') ? NOTIFICATION_EMAIL : 'gaelle.fay@galynup.fr';
$to   = defined('NOTIFICATION_EMAIL') ? NOTIFICATION_EMAIL : 'gaelle.fay@galynup.fr';

$headers  = "From: Galyn'Up <$from>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail($to, "Demande d'analyse DSI — $name", $body, $headers);

echo json_encode([
    'success' => $sent,
    'message' => $sent ? 'Demande envoyée.' : 'Erreur envoi email.',
]);
?>
