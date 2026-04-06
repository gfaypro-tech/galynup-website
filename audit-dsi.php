<?php
session_start();
require_once 'php/simulateur-db.php';

$accessLevel = 'free';

if (isset($_SESSION['simulateur_access']) && $_SESSION['simulateur_access'] === true) {
    $accessLevel = $_SESSION['simulateur_level'] ?? 'free';
} elseif (isset($_GET['token']) && !empty($_GET['token'])) {
    $level = validateToken($_GET['token']);
    if ($level !== false) {
        $accessLevel = $level;
        $_SESSION['simulateur_access'] = true;
        $_SESSION['simulateur_level'] = $level;
    }
}

$jsFiles = glob(__DIR__ . '/audit-dsi/assets/*.js');
$jsFile  = $jsFiles ? basename($jsFiles[0]) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Audit Maturité DSI — Galyn'Up</title>
  <meta name="description" content="Évaluez la maturité de votre DSI sur 6 axes stratégiques. Outil propriétaire Galyn'Up — CIO Advisory.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    #audit-root { min-height: 600px; }
  </style>
</head>
<body>

  <nav class="navbar">
    <div class="container">
      <div class="nav-content">
        <div class="nav-brand">
          <a href="index.php"><img src="images/galynup-logo-180.png" alt="GALYN'UP Logo" class="nav-logo"></a>
        </div>
        <div class="nav-menu" id="navMenu">
          <a href="index.php#about" class="nav-link">À propos</a>
          <a href="index.php#competences" class="nav-link">Compétences</a>
          <a href="index.php#prestations" class="nav-link">Prestations</a>
          <a href="index.php#realisations" class="nav-link">Réalisations</a>
          <a href="index.php#recommandations" class="nav-link">Recommandations</a>
          <a href="index.php#contact" class="nav-link">Contact</a>
          <div class="nav-dropdown">
            <a href="#" class="nav-link nav-dropdown-toggle" style="color:#D3A625;font-weight:500;">🛠 Outils ▾</a>
            <div class="nav-dropdown-menu">
              <a href="simulateur-llm.php" class="nav-dropdown-item">🧮 Simulateur LLM</a>
              <a href="audit-dsi.php" class="nav-dropdown-item active">◈ Audit Maturité DSI</a>
            </div>
          </div>
        </div>
        <div class="nav-cta-group">
          <a href="index.php#contact" class="btn btn-primary">Demander un devis</a>
        </div>
        <button class="mobile-menu-btn" id="mobileMenuBtn">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </nav>

  <div style="max-width:1000px;margin:0 auto;padding:170px 20px 64px;">
    <script>window.AUDIT_LEVEL = '<?= htmlspecialchars($accessLevel) ?>';</script>
    <div id="audit-root"></div>
    <?php if ($jsFile): ?>
    <script type="module" src="/audit-dsi/assets/<?= $jsFile ?>"></script>
    <?php endif; ?>
  </div>

  <footer style="text-align:center;padding:22px;font-size:11px;color:#9e9d96;border-top:1px solid #dddcd7;">
    Outil réalisé par <a href="https://galynup.fr" target="_blank" style="color:#6D155D;text-decoration:none;">Galyn'Up</a> — CIO Advisory &nbsp;·&nbsp;
    © 2026 Galyn'Up — Tous droits réservés
  </footer>

<script src="js/script.js"></script>
</body>
</html>
