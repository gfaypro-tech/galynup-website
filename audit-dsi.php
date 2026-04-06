<?php
session_start();
require_once 'php/simulateur-db.php';

$hasAccess = false;
$accessLevel = 'free';

if (isset($_SESSION['simulateur_access']) && $_SESSION['simulateur_access'] === true) {
    $hasAccess = true;
    $accessLevel = $_SESSION['simulateur_level'] ?? 'free';
} elseif (isset($_GET['token']) && !empty($_GET['token'])) {
    $level = validateToken($_GET['token']);
    if ($level !== false) {
        $hasAccess = true;
        $accessLevel = $level;
        $_SESSION['simulateur_access'] = true;
        $_SESSION['simulateur_level'] = $level;
    }
}
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
    /* GATE */
    .gate-wrap { position: relative; min-height: 400px; }
    .gate-overlay { position: absolute; inset: 0; backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); background: rgba(250,250,248,0.82); border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10; gap: 14px; padding: 32px 24px; text-align: center; }
    .gate-lock { font-size: 40px; line-height: 1; }
    .gate-title { font-family: 'Cormorant Garamond', serif; font-size: 22px; font-weight: 600; color: #6D155D; }
    .gate-sub { font-size: 13px; color: #5c5b55; line-height: 1.65; max-width: 400px; }
    .gate-btn { display: inline-block; padding: 12px 26px; background: #6D155D; color: #fff; border-radius: 7px; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; text-decoration: none; }
    .gate-btn:hover { background: #8a1c77; }
    /* App container */
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

    <?php if (!$hasAccess): ?>
    <!-- APERÇU VERROUILLÉ -->
    <h1 style="font-family:'Cormorant Garamond',serif;font-size:32px;color:#6D155D;margin-bottom:8px;">Audit de Maturité DSI</h1>
    <p style="font-size:13px;color:#5c5b55;line-height:1.65;max-width:620px;margin-bottom:30px;">
      Évaluez la maturité de votre DSI sur 6 axes stratégiques (24 critères pondérés). Scoring formalisé et recommandations C-Level.
    </p>
    <div class="gate-wrap">
      <div style="background:#fff;border:1px solid #dddcd7;border-radius:12px;padding:28px;margin-bottom:16px;opacity:0.4;pointer-events:none;">
        <div style="font-size:10px;font-weight:500;color:#9e9d96;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:18px;">Aperçu — 6 axes d'évaluation</div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
          <?php
          $axes = ['◈ Alignement Stratégique','⬡ Gouvernance & Conformité','◉ Architecture & Urbanisation','◎ Delivery & Agilité','◆ Opérations & Services','✦ Valeur & Innovation'];
          foreach ($axes as $ax) echo "<div style='background:#f6f6f4;border-radius:8px;padding:14px;font-size:12px;font-weight:500;'>$ax</div>";
          ?>
        </div>
      </div>
      <div class="gate-overlay">
        <div class="gate-lock">🔒</div>
        <div class="gate-title">Accès gratuit sur demande</div>
        <div class="gate-sub">Évaluez la maturité de votre DSI sur 6 axes stratégiques et 24 critères pondérés. Accès immédiat et gratuit après votre demande.</div>
        <a class="gate-btn" href="index.php#contact" target="_blank" rel="noopener">Demander l'accès gratuit →</a>
      </div>
    </div>
    <?php else: ?>
    <!-- APP REACT -->
    <script>window.AUDIT_LEVEL = '<?= htmlspecialchars($accessLevel) ?>';</script>
    <div id="audit-root"></div>
    <script type="module" src="/audit-dsi/assets/<?= basename(glob('/Users/studio/SITE GALYNUP/galynup-website/audit-dsi/assets/*.js')[0]) ?>"></script>
    <?php endif; ?>

  </div>

  <footer style="text-align:center;padding:22px;font-size:11px;color:#9e9d96;border-top:1px solid #dddcd7;">
    Outil réalisé par <a href="https://galynup.fr" target="_blank" style="color:#6D155D;text-decoration:none;">Galyn'Up</a> — CIO Advisory &nbsp;·&nbsp;
    © 2026 Galyn'Up — Tous droits réservés
  </footer>

<script src="js/script.js"></script>
</body>
</html>
