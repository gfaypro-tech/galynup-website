<?php
/**
 * Endpoint admin — Génère un token d'accès et l'envoie au prospect.
 * Ce lien est reçu dans l'email de notification de devis.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/simulateur-db.php';

// Vérification des paramètres
if (empty($_GET['name']) || empty($_GET['email']) || empty($_GET['sig'])) {
    http_response_code(400);
    die('Paramètres manquants.');
}

$name_b64  = $_GET['name'];
$email_b64 = $_GET['email'];
$sig       = $_GET['sig'];

// Vérification signature HMAC
if (!verifyGrantSig($name_b64, $email_b64, $sig)) {
    http_response_code(403);
    die('Signature invalide — lien corrompu ou expiré.');
}

$name  = base64_decode($name_b64);
$email = base64_decode($email_b64);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die('Email invalide.');
}

// Génération du token
$token = createToken($email, $name);
if (!$token) {
    http_response_code(500);
    die('Erreur lors de la création du token. Vérifiez la connexion à la base de données.');
}

// Lien d'accès pour le prospect
$accessUrl = SITE_URL . '/simulateur-llm.php?token=' . $token;

// Email au prospect
$subject = "Votre accès au Simulateur LLM — Galyn'Up";

$body  = "Bonjour $name,\n\n";
$body .= "Suite à votre demande, voici votre accès personnel au Simulateur LLM de Galyn'Up.\n\n";
$body .= "Ce simulateur vous permet d'estimer le coût mensuel de votre projet IA en comparant\n";
$body .= "26 modèles LLM selon vos volumes réels et votre contexte (langue, hallucination, région).\n\n";
$body .= "→ Accéder au simulateur :\n";
$body .= "$accessUrl\n\n";
$body .= "Cet accès est personnel et nominatif.\n\n";
$body .= "N'hésitez pas à me contacter si vous souhaitez échanger sur vos résultats.\n\n";
$body .= "Cordialement,\n";
$body .= "Gaëlle FAY\n";
$body .= "Présidente — Galyn'Up | CIO Advisory\n";
$body .= "https://galynup.fr\n";

$headers   = [];
$headers[] = "From: Gaëlle FAY — Galyn'Up <gaelle.fay@galynup.fr>";
$headers[] = "Reply-To: gaelle.fay@galynup.fr";
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";

$sent = mail($email, $subject, $body, implode("\r\n", $headers));

// Réponse à Gaëlle
if ($sent) {
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">
    <title>Accès accordé</title>
    <style>body{font-family:sans-serif;max-width:520px;margin:60px auto;padding:0 20px;color:#1c1c18;}
    .ok{background:#eef6eb;border:1px solid #b2d9a8;border-radius:8px;padding:20px 24px;}
    a{color:#6D155D;}code{background:#f3f3f0;padding:2px 6px;border-radius:4px;font-size:13px;}</style>
    </head><body>
    <div class="ok">
      <strong>✅ Accès accordé à ' . htmlspecialchars($name) . '</strong><br><br>
      Un email a été envoyé à <a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a>
      avec son lien d\'accès personnel.<br><br>
      <small>Lien généré :<br><code>' . htmlspecialchars($accessUrl) . '</code></small>
    </div>
    </body></html>';
} else {
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Erreur</title></head><body>
    <p>❌ Token créé mais l\'email n\'a pas pu être envoyé.<br>
    Envoyez manuellement ce lien à <strong>' . htmlspecialchars($email) . '</strong> :<br>
    <a href="' . htmlspecialchars($accessUrl) . '">' . htmlspecialchars($accessUrl) . '</a></p>
    </body></html>';
}
