<?php
// =============================================================
// setup.php — Génération du hash de mot de passe
// SUPPRIMER CE FICHIER après utilisation
// =============================================================
$hash = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if (strlen($pass) < 8) {
        $error = 'Le mot de passe doit faire au moins 8 caractères.';
    } elseif ($pass !== $pass2) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Setup — CV Builder</title>
  <style>
    body { font-family: system-ui, sans-serif; max-width: 500px; margin: 60px auto; padding: 20px; }
    h1 { color: #6D155D; margin-bottom: 24px; }
    label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; margin-top: 14px; }
    input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
    button { margin-top: 16px; background: #6D155D; color: white; border: none; padding: 11px 24px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
    .hash-box { background: #f4f4f4; border: 1px solid #ddd; padding: 14px; border-radius: 6px; font-family: monospace; font-size: 13px; word-break: break-all; margin-top: 20px; }
    .instructions { background: #fff4e0; border: 1px solid #f5d799; padding: 14px; border-radius: 6px; font-size: 13px; margin-top: 16px; }
    .error { color: #c0392b; margin-top: 10px; font-size: 13px; }
    .warning { background: #fdf0ee; border: 1px solid #f5c6c0; padding: 12px; border-radius: 6px; font-size: 13px; color: #c0392b; margin-bottom: 20px; }
  </style>
</head>
<body>
  <h1>◈ CV Builder — Setup</h1>

  <div class="warning">
    ⚠ Supprime ce fichier (setup.php) après avoir configuré ton mot de passe.
  </div>

  <form method="POST">
    <label>Nouveau mot de passe</label>
    <input type="password" name="password" minlength="8" required>
    <label>Confirmer le mot de passe</label>
    <input type="password" name="password2" minlength="8" required>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <button type="submit">Générer le hash</button>
  </form>

  <?php if ($hash): ?>
    <div class="hash-box"><?= htmlspecialchars($hash) ?></div>
    <div class="instructions">
      <strong>Instructions :</strong><br>
      1. Copie le hash ci-dessus<br>
      2. Ouvre <code>cv/config.php</code> via FTP<br>
      3. Remplace la valeur de <code>CV_PASSWORD_HASH</code> par ce hash<br>
      4. <strong>Supprime ce fichier setup.php du serveur</strong>
    </div>
  <?php endif; ?>
</body>
</html>
