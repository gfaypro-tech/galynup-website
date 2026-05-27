<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireLogin();

$db = getDB();

// Stats
$totalApps      = $db->query("SELECT COUNT(*) FROM cv_applications")->fetchColumn();
$completedApps  = $db->query("SELECT COUNT(*) FROM cv_applications WHERE status = 'completed'")->fetchColumn();
$knowledgeCount = $db->query("SELECT COUNT(*) FROM cv_knowledge WHERE is_active = 1")->fetchColumn();

// Candidatures récentes (5 dernières)
$recent = $db->query("SELECT * FROM cv_applications ORDER BY updated_at DESC LIMIT 5")->fetchAll();

$statusLabels = [
    'draft'      => 'Brouillon',
    'analysis'   => 'Analyse',
    'matching'   => 'Matching',
    'dialogue'   => 'Dialogue',
    'generating' => 'Génération',
    'completed'  => 'Terminé',
];

$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-number"><?= $totalApps ?></div>
    <div class="stat-label">Candidatures totales</div>
  </div>
  <div class="stat-card">
    <div class="stat-number"><?= $completedApps ?></div>
    <div class="stat-label">CV générés</div>
  </div>
  <div class="stat-card">
    <div class="stat-number"><?= $knowledgeCount ?></div>
    <div class="stat-label">Entrées base de connaissance</div>
  </div>
</div>

<div class="flex flex-gap mb-16">
  <a href="new-application.php" class="btn btn-primary btn-lg">+ Nouvelle candidature</a>
  <a href="knowledge-base.php" class="btn btn-outline">Gérer la base de connaissance</a>
</div>

<div class="card">
  <div class="card-title">◷ Candidatures récentes</div>

  <?php if (empty($recent)): ?>
    <p class="text-muted text-center" style="padding: 32px 0;">
      Aucune candidature pour l'instant.<br>
      <a href="new-application.php" class="btn btn-gold btn-sm" style="margin-top:12px;">Commencer</a>
    </p>
  <?php else: ?>
    <div class="applications-list">
      <?php foreach ($recent as $app): ?>
        <a href="new-application.php?id=<?= $app['id'] ?>" class="application-item">
          <div>
            <div class="application-company"><?= htmlspecialchars($app['company']) ?></div>
            <div class="application-position"><?= htmlspecialchars($app['position']) ?></div>
          </div>
          <span class="badge badge-status-<?= $app['status'] ?>">
            <?= $statusLabels[$app['status']] ?? $app['status'] ?>
          </span>
          <span class="application-date">
            <?= date('d/m/Y', strtotime($app['updated_at'])) ?>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
    <?php if ($totalApps > 5): ?>
      <div style="margin-top:16px;">
        <a href="history.php" class="btn btn-ghost btn-sm">Voir tout l'historique (<?= $totalApps ?>)</a>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
