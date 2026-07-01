<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireLogin();

$db = getDB();

$search  = trim($_GET['q']       ?? '');
$status  = $_GET['status']       ?? 'all';
$hiring  = $_GET['hiring']       ?? 'all';
$avanc   = $_GET['avancement']   ?? 'all';

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
if ($avanc !== 'all') {
    $where   .= ' AND avancement = ?';
    $params[] = $avanc;
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
    'direct'     => 'Directe',
];

$hiringLabels = [
    'non_envoye' => 'Non envoyé',
    'envoye'     => 'Envoyé',
    'repondu'    => 'Répondu',
    'relance'    => 'Relancé',
    'entretien'  => 'Entretien',
    'offre'      => 'Offre reçue',
    'refuse'     => 'Refusé',
    'abandon'    => 'Abandonné',
];

$avancementLabels = [
    'en_cours'   => 'En cours',
    'a_relancer' => 'À relancer',
    'cloture'    => 'Clôturé',
];

$pageTitle  = 'Historique';
$activePage = 'history';
require_once __DIR__ . '/includes/header.php';
?>

<div class="card" style="padding:16px 20px; margin-bottom:16px;">
  <form method="GET" class="flex flex-gap items-center" style="flex-wrap:wrap;">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
           class="form-control" placeholder="Rechercher (entreprise, poste)…" style="max-width:260px;">
    <select name="status" class="form-control" style="max-width:160px;">
      <option value="all">Tous les parcours</option>
      <?php foreach ($statusLabels as $val => $label): ?>
        <option value="<?= $val ?>" <?= $status === $val ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
    <select name="hiring" class="form-control" style="max-width:150px;">
      <option value="all">Tous les statuts</option>
      <?php foreach ($hiringLabels as $val => $label): ?>
        <option value="<?= $val ?>" <?= $hiring === $val ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
    <select name="avancement" class="form-control" style="max-width:150px;">
      <option value="all">Tout l'avancement</option>
      <?php foreach ($avancementLabels as $val => $label): ?>
        <option value="<?= $val ?>" <?= $avanc === $val ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Filtrer</button>
    <?php if ($search || $status !== 'all' || $hiring !== 'all' || $avanc !== 'all'): ?>
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
      $hs       = $app['hiring_status']      ?? 'non_envoye';
      $av       = $app['avancement']          ?? 'en_cours';
      $dateCand = $app['date_candidature']    ?? '';
      $comment  = $app['commentaire_relance'] ?? '';
    ?>
      <div class="application-item" data-id="<?= $app['id'] ?>" style="flex-wrap:wrap;">

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

        <div class="app-statuses">

          <div class="app-status-group">
            <span class="app-status-prefix">Parcours CV</span>
            <span class="badge badge-status-<?= $app['status'] ?>">
              <?= $statusLabels[$app['status']] ?? $app['status'] ?>
            </span>
          </div>

          <div class="app-status-group">
            <span class="app-status-prefix">Statut</span>
            <div class="status-with-comment">
              <select class="hiring-select hiring-<?= $hs ?>"
                      onchange="updateHiringStatus(<?= $app['id'] ?>, this)">
                <?php foreach ($hiringLabels as $val => $label): ?>
                  <option value="<?= $val ?>" <?= $hs === $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn-comment <?= $comment !== '' ? 'has-comment' : '' ?>"
                      onclick="openCommentaireModal(<?= $app['id'] ?>, this.dataset.comment)"
                      data-comment="<?= htmlspecialchars($comment) ?>"
                      title="<?= $comment !== '' ? htmlspecialchars($comment) : 'Ajouter un commentaire de relance' ?>">💬</button>
            </div>
          </div>

          <div class="app-status-group">
            <span class="app-status-prefix">Avancement</span>
            <select class="avancement-select avancement-<?= $av ?>"
                    onchange="updateAvancement(<?= $app['id'] ?>, this)">
              <?php foreach ($avancementLabels as $val => $label): ?>
                <option value="<?= $val ?>" <?= $av === $val ? 'selected' : '' ?>><?= $label ?></option>
              <?php endforeach; ?>
            </select>
          </div>

        </div>

        <div class="app-date-cand">
          <span class="app-status-prefix">Envoi</span>
          <input type="date" class="date-cand-input"
                 value="<?= htmlspecialchars($dateCand) ?>"
                 onchange="updateDateCandidature(<?= $app['id'] ?>, this)"
                 title="Date d'envoi de la candidature">
        </div>

        <span class="application-date"><?= date('d/m/Y H:i', strtotime($app['updated_at'])) ?></span>

        <div class="flex flex-gap" style="flex-shrink:0;">
          <a href="new-application.php?id=<?= $app['id'] ?>" class="btn btn-ghost btn-sm">
            <?php if ($app['status'] === 'completed'): ?>Voir
            <?php elseif ($app['status'] === 'direct'): ?>Ouvrir
            <?php else: ?>Continuer<?php endif; ?>
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

<!-- ── Modal : commentaire de relance ───────────────────── -->
<div id="modal-commentaire" class="modal-overlay" style="display:none;"
     onclick="if(event.target===this)closeCommentaireModal()">
  <div class="modal-box" style="max-width:420px;">
    <div class="modal-header">
      <h3>Commentaire de relance</h3>
      <button onclick="closeCommentaireModal()" class="modal-close" aria-label="Fermer">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="comment-app-id">
      <div class="form-group" style="margin-bottom:0;">
        <label for="comment-text">Note sur cette relance</label>
        <textarea id="comment-text" class="form-control" rows="4"
                  placeholder="Ex : relancé par email le 15/06, en attente de réponse…"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button onclick="closeCommentaireModal()" class="btn btn-ghost">Passer</button>
      <button onclick="saveCommentaire()" class="btn btn-primary" id="btn-comment-save">Enregistrer</button>
    </div>
  </div>
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
      selectEl.className = 'hiring-select hiring-' + newStatus;
      if (newStatus === 'relance') openCommentaireModal(id, '');
    }
  });
}

function updateAvancement(id, selectEl) {
  const val = selectEl.value;
  fetch('php/update-avancement.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id, avancement: val})
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      selectEl.className = 'avancement-select avancement-' + val;
    }
  });
}

function updateDateCandidature(id, inputEl) {
  fetch('php/update-date-candidature.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id, date_candidature: inputEl.value})
  })
  .then(r => r.json())
  .then(d => { if (!d.success) alert(d.error || 'Erreur lors de la sauvegarde.'); });
}

function openCommentaireModal(id, existingComment) {
  document.getElementById('comment-app-id').value = id;
  document.getElementById('comment-text').value   = existingComment || '';
  document.getElementById('modal-commentaire').style.display = 'flex';
  setTimeout(() => document.getElementById('comment-text').focus(), 50);
}

function closeCommentaireModal() {
  document.getElementById('modal-commentaire').style.display = 'none';
}

function saveCommentaire() {
  const id          = document.getElementById('comment-app-id').value;
  const commentaire = document.getElementById('comment-text').value.trim();
  const btn         = document.getElementById('btn-comment-save');
  btn.disabled      = true;
  btn.textContent   = 'Enregistrement…';

  fetch('php/save-commentaire-relance.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id, commentaire})
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      const row = document.querySelector(`.application-item[data-id="${id}"]`);
      if (row) {
        const btnC = row.querySelector('.btn-comment');
        if (btnC) {
          btnC.classList.toggle('has-comment', commentaire !== '');
          btnC.dataset.comment = commentaire;
          btnC.title = commentaire || 'Ajouter un commentaire de relance';
        }
      }
      closeCommentaireModal();
    } else {
      alert(d.error || 'Erreur.');
    }
    btn.disabled    = false;
    btn.textContent = 'Enregistrer';
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
