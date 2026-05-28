<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireLogin();

$db = getDB();

$search  = trim($_GET['q'] ?? '');
$status  = $_GET['status'] ?? 'all';
$hiring  = $_GET['hiring'] ?? 'all';

$where  = 'WHERE 1=1';
$params = [];
if ($search !== '') {
    $where   .= ' AND (company LIKE ? OR position LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($status !== 'all') {
    $where   .= ' AND status = ?';
    $params[] = $status;
}
if ($hiring !== 'all') {
    $where   .= ' AND hiring_status = ?';
    $params[] = $hiring;
}

$stmt = $db->prepare("SELECT * FROM cv_applications $where ORDER BY updated_at DESC");
$stmt->execute($params);
$apps = $stmt->fetchAll();

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

$pageTitle  = 'Historique';
$activePage = 'history';
require_once __DIR__ . '/includes/header.php';
?>

<div class="card" style="padding:16px 20px; margin-bottom:16px;">
  <form method="GET" class="flex flex-gap items-center" style="flex-wrap:wrap;">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
           class="form-control" placeholder="Rechercher (entreprise, poste)..." style="max-width:280px;">
    <select name="status" class="form-control" style="max-width:160px;">
      <option value="all">Tous les statuts</option>
      <?php foreach ($statusLabels as $val => $label): ?>
        <option value="<?= $val ?>" <?= $status === $val ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
    <select name="hiring" class="form-control" style="max-width:160px;">
      <option value="all">Pipeline complet</option>
      <?php foreach ($hiringLabels as $val => $label): ?>
        <option value="<?= $val ?>" <?= $hiring === $val ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Filtrer</button>
    <?php if ($search || $status !== 'all' || $hiring !== 'all'): ?>
      <a href="history.php" class="btn btn-ghost btn-sm">Réinitialiser</a>
    <?php endif; ?>
  </form>
</div>

<?php if (empty($apps)): ?>
  <div class="card text-center" style="padding:40px;">
    <p class="text-muted">Aucune candidature trouvée.</p>
    <a href="new-application.php" class="btn btn-primary btn-sm" style="margin-top:12px;">Nouvelle candidature</a>
  </div>
<?php else: ?>
  <div class="applications-list">
    <?php foreach ($apps as $app):
      $hs = $app['hiring_status'] ?? 'non_envoye';
    ?>
      <div class="application-item" style="flex-wrap:wrap;">
        <div style="flex:1; min-width:200px;">
          <div class="application-company"><?= htmlspecialchars($app['company']) ?></div>
          <div class="application-position"><?= htmlspecialchars($app['position']) ?>
            <?php if (!empty($app['source_url'])): ?>
              <span class="source-platform">
                — <a href="<?= htmlspecialchars($app['source_url']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars(detectPlatform($app['source_url'])) ?></a>
              </span>
            <?php endif; ?>
          </div>
        </div>
        <span class="badge badge-status-<?= $app['status'] ?>">
          <?= $statusLabels[$app['status']] ?? $app['status'] ?>
        </span>
        <select class="hiring-select hiring-<?= $hs ?>"
                onchange="updateHiringStatus(<?= $app['id'] ?>, this)">
          <?php foreach ($hiringLabels as $val => $label): ?>
            <option value="<?= $val ?>" <?= $hs === $val ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
        <span class="application-date"><?= date('d/m/Y H:i', strtotime($app['updated_at'])) ?></span>
        <div class="flex flex-gap" style="flex-shrink:0;">
          <a href="new-application.php?id=<?= $app['id'] ?>" class="btn btn-ghost btn-sm">
            <?= $app['status'] === 'completed' ? 'Voir' : 'Continuer' ?>
          </a>
          <?php if ($app['status'] === 'completed'): ?>
            <a href="php/export-word.php?id=<?= $app['id'] ?>" class="btn btn-outline btn-sm">⬇ Word</a>
          <?php endif; ?>
          <button class="btn btn-danger btn-sm" onclick="deleteApp(<?= $app['id'] ?>)">✕</button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

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

function deleteApp(id) {
  if (!confirm('Supprimer définitivement cette candidature et son CV ?')) return;
  fetch('php/delete-application.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id})
  })
  .then(r => r.json())
  .then(d => { if (d.success) window.location.reload(); });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
