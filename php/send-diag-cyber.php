<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$name    = isset($_POST['name'])     ? trim($_POST['name'])     : '';
$email   = isset($_POST['email'])    ? trim($_POST['email'])    : '';
$message = isset($_POST['message'])  ? trim($_POST['message'])  : '';
$diagRaw = isset($_POST['diag_data']) ? $_POST['diag_data']     : '';
$pdfB64  = isset($_POST['pdf_data']) ? preg_replace('/\s+/', '', $_POST['pdf_data']) : '';
$csvB64  = isset($_POST['csv_data']) ? preg_replace('/\s+/', '', $_POST['csv_data']) : '';

if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nom et email requis']);
    exit;
}

$name  = str_replace(["\r", "\n"], '', $name);
$email = str_replace(["\r", "\n"], '', $email);

$diag = $diagRaw ? @json_decode($diagRaw, true) : null;

// ── Corps texte ──────────────────────────────────────────────────────────────
$text  = "Demande d'analyse approfondie — Diagnostic Maturité Cybersécurité\n\n";
$text .= "Nom    : $name\n";
$text .= "Email  : $email\n";
if ($message) $text .= "Message: $message\n";
$text .= "\n";

if ($diag) {
    $client = $diag['client'] ?? [];
    $text .= "========================================\n";
    $text .= "CONTEXTE DU DIAGNOSTIC\n";
    $text .= "========================================\n";
    if (!empty($client['name']))    $text .= "Client        : {$client['name']}\n";
    if (!empty($client['sector']))  $text .= "Secteur       : {$client['sector']}\n";
    if (!empty($client['contact'])) $text .= "Interlocuteur : {$client['contact']}\n";
    if (!empty($client['date']))    $text .= "Date          : {$client['date']}\n";

    $text .= "\n========================================\n";
    $text .= "RÉSULTATS DU DIAGNOSTIC CYBERSÉCURITÉ\n";
    $text .= "========================================\n";
    $text .= "Score global : " . ($diag['globalScore'] ?? 'N/A') . " / 100\n\n";

    if (!empty($diag['domainScores'])) {
        foreach ($diag['domainScores'] as $item) {
            $dom   = $item['domain'] ?? '';
            $sc    = (int)($item['score'] ?? 0);
            $lbl   = $sc < 25 ? 'Initial' : ($sc < 50 ? 'Basique' : ($sc < 75 ? 'Défini' : 'Optimisé'));
            $text .= sprintf("  %-42s %3d%%  (%s)\n", $dom, $sc, $lbl);
        }
    }

    if (!empty($diag['recos'])) {
        $text .= "\n========================================\n";
        $text .= "PLAN DE RECOMMANDATIONS (top 12)\n";
        $text .= "========================================\n";
        foreach ($diag['recos'] as $i => $r) {
            $text .= ($i + 1) . ". [{$r['prio']}] {$r['domain']}\n";
            $text .= "   Situation     : {$r['action']}\n";
            $text .= "   Étape suivante: {$r['next']}\n\n";
        }
    }
    $text .= "========================================\n";
}
$text .= "\nDate envoi : " . date('d/m/Y H:i:s') . "\n";

// ── Config ───────────────────────────────────────────────────────────────────
$configFile = __DIR__ . '/config.php';
if (file_exists($configFile)) require_once $configFile;
$from = defined('NOTIFICATION_EMAIL') ? NOTIFICATION_EMAIL : 'gaelle.fay@galynup.fr';
$to   = defined('NOTIFICATION_EMAIL') ? NOTIFICATION_EMAIL : 'gaelle.fay@galynup.fr';

$clientSlug = preg_replace('/[^a-z0-9_-]/i', '_', $name);

// ── MIME multipart ────────────────────────────────────────────────────────────
$boundary = 'galynup_' . md5(uniqid(mt_rand(), true));

$mime  = "--{$boundary}\r\n";
$mime .= "Content-Type: text/plain; charset=UTF-8\r\n";
$mime .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
$mime .= quoted_printable_encode($text) . "\r\n";

if ($pdfB64) {
    $mime .= "--{$boundary}\r\n";
    $mime .= "Content-Type: application/pdf\r\n";
    $mime .= "Content-Disposition: attachment; filename=\"diag-cyber-{$clientSlug}.pdf\"\r\n";
    $mime .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $mime .= chunk_split($pdfB64) . "\r\n";
}

if ($csvB64) {
    $mime .= "--{$boundary}\r\n";
    $mime .= "Content-Type: text/csv; charset=UTF-8\r\n";
    $mime .= "Content-Disposition: attachment; filename=\"diag-cyber-{$clientSlug}.csv\"\r\n";
    $mime .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $mime .= chunk_split($csvB64) . "\r\n";
}

$mime .= "--{$boundary}--\r\n";

$headers  = "From: Galyn'Up <{$from}>\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

$sent = mail($to, "Diag Cybersécurité — {$name}", $mime, $headers);

echo json_encode([
    'success' => $sent,
    'message' => $sent ? 'Demande envoyée.' : 'Erreur envoi email.',
]);
?>
