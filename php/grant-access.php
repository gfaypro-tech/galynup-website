<?php
/**
 * Endpoint admin — Génère un token d'accès et l'envoie au prospect.
 * Utilise un formulaire POST pour éviter le pré-scan des liens par les clients email.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/simulateur-db.php';

// Vérification des paramètres GET
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

// Si c'est un POST → on génère le token et on envoie l'email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {

    $token = createToken($email, $name, 'free');
    if (!$token) {
        http_response_code(500);
        die('Erreur lors de la création du token.');
    }

    $accessUrl = SITE_URL . '/simulateur-llm.php?token=' . $token;

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

    $headers   = [];
    $headers[] = "From: Gaëlle FAY — Galyn'Up <gaelle.fay@galynup.fr>";
    $headers[] = "Reply-To: gaelle.fay@galynup.fr";
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/plain; charset=UTF-8";

    $sent = mail($email, $subject, $body, implode("\r\n", $headers));

    if ($sent) {
        echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">
        <title>Accès accordé</title>
        <style>body{font-family:sans-serif;max-width:520px;margin:60px auto;padding:0 20px;color:#1c1c18;}
        .ok{background:#eef6eb;border:1px solid #b2d9a8;border-radius:8px;padding:20px 24px;}
        a{color:#6D155D;}code{background:#f3f3f0;padding:2px 6px;border-radius:4px;font-size:13px;word-break:break-all;}</style>
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
        <p>❌ Token créé mais email non envoyé. Envoyez manuellement ce lien à <strong>' . htmlspecialchars($name) . '</strong> :<br>
        <a href="' . htmlspecialchars($accessUrl) . '">' . htmlspecialchars($accessUrl) . '</a></p>
        </body></html>';
    }
    exit;
}

// Sinon → page de confirmation (GET simple = pré-scan email, on n'agit pas encore)
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Donner l'accès — Galyn'Up</title>
  <style>
    body { font-family: sans-serif; max-width: 520px; margin: 60px auto; padding: 0 20px; color: #1c1c18; }
    .card { background: #f7eef5; border: 1px solid rgba(109,21,93,0.2); border-radius: 10px; padding: 28px 28px; }
    h2 { font-size: 20px; color: #6D155D; margin: 0 0 16px; }
    p { font-size: 14px; line-height: 1.6; margin: 0 0 20px; }
    .btn { display: inline-block; padding: 12px 28px; background: #6D155D; color: #fff; border: none;
           border-radius: 7px; font-size: 14px; font-weight: 500; cursor: pointer; font-family: sans-serif; }
    .btn:hover { background: #8a1c77; }
    .btn-premium { background: #D3A625; color: #1c1c18; }
    .btn-premium:hover { background: #e8b82e; }
    .info { font-size: 12px; color: #5c5b55; margin-top: 14px; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Donner l'accès au Simulateur LLM</h2>
    <p>
      Vous allez accorder un accès personnel au simulateur à :<br>
      <strong><?= htmlspecialchars($name) ?></strong> — <?= htmlspecialchars($email) ?>
    </p>
    <p>Un email avec son lien unique lui sera envoyé automatiquement.</p>
    <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
      <input type="hidden" name="confirm" value="1">
      <button type="submit" class="btn">✅ Donner l'accès</button>
    </form>
    <p class="info">⚠️ Ne cliquez qu'une seule fois. Chaque clic génère un nouveau lien et désactive le précédent.</p>
  </div>
</body>
</html>
