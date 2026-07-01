<?php
// header.php — reçoit $pageTitle (string) et $activePage (string)
$activePage = $activePage ?? '';
$pageTitle  = $pageTitle ?? CV_TITLE;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> — CV Builder</title>
  <link rel="icon" type="image/svg+xml" href="<?= cvUrl('img/icon.svg') ?>">
  <link rel="apple-touch-icon" href="<?= cvUrl('img/icon.php?size=180') ?>">
  <link rel="manifest" href="<?= cvUrl('manifest.json') ?>">
  <meta name="theme-color" content="#0d0d14">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="CV Builder">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php if ((CV_THEME ?? 'default') === 'glass'): ?>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<?php else: ?>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<?php endif; ?>
  <link rel="stylesheet" href="<?= cvUrl('css/style.css') ?>">
<?php if ((CV_THEME ?? 'default') === 'glass'): ?>
  <link rel="stylesheet" href="<?= cvUrl('css/style-glass.css') ?>">
<?php endif; ?>
</head>
<body>

<div class="mobile-topbar">
  <button class="hamburger" id="hamburger-btn" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="sidebar">
    <span></span>
    <span></span>
    <span></span>
  </button>
  <span class="mobile-brand-name">CV Builder</span>
</div>

<div class="layout">
  <!-- Sidebar -->
  <div class="sidebar-overlay" id="sidebar-overlay"></div>
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <span class="brand-icon">◈</span>
      <span class="brand-name">CV Builder</span>
    </div>

    <nav class="sidebar-nav">
      <a href="<?= cvUrl('dashboard.php') ?>" class="nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
        <span class="nav-icon">⊞</span> Dashboard
      </a>
      <a href="<?= cvUrl('new-application.php') ?>" class="nav-item nav-item--cta <?= $activePage === 'new' ? 'active' : '' ?>">
        <span class="nav-icon">+</span> Nouvelle candidature
      </a>
      <a href="<?= cvUrl('knowledge-base.php') ?>" class="nav-item <?= $activePage === 'knowledge' ? 'active' : '' ?>">
        <span class="nav-icon">◎</span> Base de connaissance
      </a>
      <a href="<?= cvUrl('history.php') ?>" class="nav-item <?= $activePage === 'history' ? 'active' : '' ?>">
        <span class="nav-icon">◷</span> Historique
      </a>
    </nav>

    <div class="sidebar-footer">
      <a href="<?= cvUrl('logout.php') ?>" class="nav-item nav-item--logout">
        <span class="nav-icon">→</span> Déconnexion
      </a>
    </div>
  </aside>

  <!-- Main content -->
  <main class="main-content">
    <div class="page-header">
      <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
    </div>
    <div class="page-body">
