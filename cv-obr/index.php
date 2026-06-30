<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

// Déjà connecté → dashboard
if (isLoggedIn()) {
    header('Location: ' . CV_BASE_URL . '/dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (login($user, $pass)) {
        header('Location: ' . CV_BASE_URL . '/dashboard.php');
        exit;
    }
    $error = 'Identifiants incorrects.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CV Builder — Connexion</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-box">
    <div class="login-logo">
      <span class="brand-icon">◈</span>
      <span class="brand-name">CV Builder</span>
    </div>

    <?php if ($error): ?>
      <div class="login-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="username">Identifiant</label>
        <input type="text" id="username" name="username" class="form-control"
               autocomplete="username" autofocus
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" class="form-control"
               autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:8px;">
        Se connecter
      </button>
    </form>
  </div>
</div>
</body>
</html>
