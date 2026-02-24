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

// Préparer les en-têtes
$headers = [];
$headers[] = "From: $fullName <$email>";
$headers[] = "Reply-To: $email";
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
