<?php
// Configuration
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Email de destination
$to_email = 'gaelle.fay@galynup.fr';

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

// Récupérer et nettoyer les données du formulaire
$fullName = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$company = isset($_POST['company']) ? trim($_POST['company']) : '';
$serviceType = isset($_POST['serviceType']) ? trim($_POST['serviceType']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validation des champs obligatoires
$errors = [];

if (empty($fullName) || strlen($fullName) < 2) {
    $errors[] = 'Le nom doit contenir au moins 2 caractères';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email invalide';
}

if (empty($serviceType)) {
    $errors[] = 'Veuillez sélectionner un type de prestation';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Le message doit contenir au moins 10 caractères';
}

// Si des erreurs, retourner
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ]);
    exit;
}

// Protection contre les injections d'en-têtes
$fullName = str_replace(["\r", "\n", "%0a", "%0d"], '', $fullName);
$email = str_replace(["\r", "\n", "%0a", "%0d"], '', $email);

// Préparer le sujet de l'email
$subject = "Nouvelle demande de devis - $serviceType";

// Préparer le corps de l'email
$email_body = "Nouvelle demande de devis reçue depuis le site Galynup\n\n";
$email_body .= "Informations du contact:\n";
$email_body .= "------------------------\n";
$email_body .= "Nom complet: $fullName\n";
$email_body .= "Email: $email\n";

if (!empty($phone)) {
    $email_body .= "Téléphone: $phone\n";
}

if (!empty($company)) {
    $email_body .= "Entreprise: $company\n";
}

$email_body .= "\nType de prestation:\n";
$email_body .= "------------------------\n";
$email_body .= "$serviceType\n";

$email_body .= "\nMessage:\n";
$email_body .= "------------------------\n";
$email_body .= "$message\n";

$email_body .= "\n------------------------\n";
$email_body .= "Date: " . date('d/m/Y H:i:s') . "\n";
$email_body .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Lien pour donner l'accès au Simulateur LLM (inline, sans dépendance)
$configFile = __DIR__ . '/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
    if (defined('ADMIN_SECRET') && defined('SITE_URL')) {
        $name_b64  = base64_encode($fullName);
        $email_b64 = base64_encode($email);
        $sig       = hash_hmac('sha256', $name_b64 . '|' . $email_b64, ADMIN_SECRET);
        $grantLink = SITE_URL . '/php/grant-access.php'
            . '?name='  . urlencode($name_b64)
            . '&email=' . urlencode($email_b64)
            . '&sig='   . $sig;
        $email_body .= "\n========================================\n";
        $email_body .= "DONNER L'ACCES AU SIMULATEUR LLM\n";
        $email_body .= "========================================\n";
        $email_body .= "Cliquez ce lien pour envoyer un acces a $fullName :\n";
        $email_body .= "$grantLink\n";
        $email_body .= "========================================\n";
    }
}

// Préparer les en-têtes
$from_email = defined('NOTIFICATION_EMAIL') ? NOTIFICATION_EMAIL : $to_email;
$headers = [];
$headers[] = "From: Galyn'Up <$from_email>";
$headers[] = "Reply-To: $fullName <$email>";
$headers[] = "X-Mailer: PHP/" . phpversion();
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";

// Envoyer l'email
$mail_sent = mail($to_email, $subject, $email_body, implode("\r\n", $headers));

// Réponse
if ($mail_sent) {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Votre demande a été envoyée avec succès ! Je vous recontacterai dans les plus brefs délais.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer ou me contacter directement par email.'
    ]);
}
?>
