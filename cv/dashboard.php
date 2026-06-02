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
$entretienCount = $db->query("SELECT COUNT(*) FROM cv_applications WHERE hiring_status IN ('entretien','offre')")->fetchColumn();

// Filtres dashboard
$filterStatus  = $_GET['status']  ?? 'all';
$filterHiring  = $_GET['hiring']  ?? 'all';
$filterMonth   = trim($_GET['month'] ?? '');   // format YYYY-MM

$where  = 'WHERE 1=1';
$params = [];
if ($filterStatus !== 'all') {
    $where   .= ' AND status = ?';
    $params[] = $filterStatus;
}
if ($filterHiring !== 'all') {
    $where   .= ' AND hiring_status = ?';
    $params[] = $filterHiring;
}
if ($filterMonth !== '' && preg_match('/^\d{4}-\d{2}$/', $filterMonth)) {
    $where   .= ' AND DATE_FORMAT(updated_at, \'%Y-%m\') = ?';
    $params[] = $filterMonth;
}
$hasFilter = ($filterStatus !== 'all' || $filterHiring !== 'all' || $filterMonth !== '');
$limit     = $hasFilter ? '' : 'LIMIT 20';

$stmt = $db->prepare("SELECT * FROM cv_applications $where ORDER BY updated_at DESC $limit");
$stmt->execute($params);
$recent = $stmt->fetchAll();

$statusLabels = [
    'draft'      => 'Brouillon',
    'analysis'   => 'Analyse',
    'matching'   => 'Matching',
    'dialogue'   => 'Enrichissement',
    'generating' => 'Génération',
    'completed'  => 'Terminé',
];

$hiringLabels = [
    'non_envoye' => 'Non envoyé',
    'envoye'     => 'Envoyé',
    'repondu'    => 'Répondu',
    'entretien'  => 'Entretien',
    'offre'      => 'Offre reçue',
    'refuse'     => 'Refusé',
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
    <div class="stat-number"><?= $entretienCount ?></div>
    <div class="stat-label">Entretiens / Offres</div>
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

<div class="card" style="padding:16px 20px; margin-bottom:16px;">
  <form method="GET" class="flex flex-gap items-center" style="flex-wrap:wrap;">
    <select name="status" class="form-control" style="max-width:180px;">
      <option value="all">Tous les statuts</option>
      <?php foreach ($statusLabels as $val => $label): ?>
        <option value="<?= $val ?>" <?= $filterStatus === $val ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
    <select name="hiring" class="form-control" style="max-width:160px;">
      <option value="all">Pipeline complet</option>
      <?php foreach ($hiringLabels as $val => $label): ?>
        <option value="<?= $val ?>" <?= $filterHiring === $val ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
    <input type="month" name="month" value="<?= htmlspecialchars($filterMonth) ?>"
           class="form-control" style="max-width:160px;" title="Filtrer par mois">
    <button type="submit" class="btn btn-outline btn-sm">Filtrer</button>
    <?php if ($hasFilter): ?>
      <a href="dashboard.php" class="btn btn-ghost btn-sm">Réinitialiser</a>
    <?php endif; ?>
  </form>
</div>

<div class="card">
  <div class="card-title">◷ <?= $hasFilter ? 'Candidatures filtrées' : 'Candidatures récentes' ?></div>

  <?php if (empty($recent)): ?>
    <p class="text-muted text-center" style="padding: 32px 0;">
      <?php if ($hasFilter): ?>
        Aucune candidature ne correspond aux filtres sélectionnés.
      <?php else: ?>
        Aucune candidature pour l'instant.<br>
        <a href="new-application.php" class="btn btn-gold btn-sm" style="margin-top:12px;">Commencer</a>
      <?php endif; ?>
    </p>
  <?php else: ?>
    <div class="applications-list">
      <?php foreach ($recent as $app):
        $hs = $app['hiring_status'] ?? 'non_envoye';
      ?>
        <div class="application-item">
          <a href="new-application.php?id=<?= $app['id'] ?>" class="application-link">
            <div class="application-company"><?= htmlspecialchars($app['company']) ?></div>
            <div class="application-position"><?= htmlspecialchars($app['position']) ?>
              <?php if (!empty($app['source_url'])): ?>
                <span class="source-platform">
                  — <a href="<?= htmlspecialchars($app['source_url']) ?>" target="_blank" rel="noopener"
                       onclick="event.stopPropagation()"><?= htmlspecialchars(detectPlatform($app['source_url'])) ?></a>
                </span>
              <?php endif; ?>
            </div>
          </a>
          <span class="badge badge-status-<?= $app['status'] ?>">
            <?= $statusLabels[$app['status']] ?? $app['status'] ?>
          </span>
          <select class="hiring-select hiring-<?= $hs ?>"
                  onchange="updateHiringStatus(<?= $app['id'] ?>, this)">
            <?php foreach ($hiringLabels as $val => $label): ?>
              <option value="<?= $val ?>" <?= $hs === $val ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
          <span class="application-date">
            <?= date('d/m/Y', strtotime($app['updated_at'])) ?>
          </span>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (!$hasFilter && $totalApps > 20): ?>
      <div style="margin-top:16px;">
        <a href="history.php" class="btn btn-ghost btn-sm">Voir tout l'historique (<?= $totalApps ?>)</a>
      </div>
    <?php elseif ($hasFilter): ?>
      <div style="margin-top:16px;">
        <a href="history.php?<?= http_build_query(array_filter(['status' => $filterStatus !== 'all' ? $filterStatus : null])) ?>" class="btn btn-ghost btn-sm">Voir dans l'historique complet</a>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<script>
function updateHiringStatus(id, selectEl) {
  const newStatus = selectEl.value;
  fetch('php/update-hiring-status.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id, hiring_status: newStatus})
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      selectEl.className = selectEl.className.replace(/hiring-\S+/, 'hiring-' + newStatus);
    }
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
