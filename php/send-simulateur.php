<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false]);
    exit;
}

$name  = isset($_POST['name'])  ? trim($_POST['name'])  : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nom et email requis']);
    exit;
}

$name  = str_replace(["\r", "\n"], '', $name);
$email = str_replace(["\r", "\n"], '', $email);

require_once __DIR__ . '/config.php';

// Génération du lien d'accès
$name_b64  = base64_encode($name);
$email_b64 = base64_encode($email);
$sig       = hash_hmac('sha256', $name_b64 . '|' . $email_b64, ADMIN_SECRET);
$grantLink = SITE_URL . '/php/grant-access.php'
    . '?name='  . urlencode($name_b64)
    . '&email=' . urlencode($email_b64)
    . '&sig='   . $sig;

$from = defined('NOTIFICATION_EMAIL') ? NOTIFICATION_EMAIL : 'gaelle.fay@galynup.fr';
$to   = $from;

$body  = "Demande d'accès au Simulateur LLM\n\n";
$body .= "Nom   : $name\n";
$body .= "Email : $email\n";
$body .= "Date  : " . date('d/m/Y H:i:s') . "\n\n";
$body .= "========================================\n";
$body .= "DONNER L'ACCÈS AU SIMULATEUR\n";
$body .= "========================================\n";
$body .= "$grantLink\n";
$body .= "========================================\n";

$headers  = "From: Galyn'Up <$from>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail($to, "Accès Simulateur LLM — $name", $body, $headers);

echo json_encode(['success' => $sent]);
?>
