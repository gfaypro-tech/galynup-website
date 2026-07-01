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

// Filtres (s'appliquent par-dessus le filtre actif implicite)
$filterStatus  = $_GET['status']  ?? 'all';
$filterHiring  = $_GET['hiring']  ?? 'all';
$filterMonth   = trim($_GET['month'] ?? '');

// Dashboard = candidatures actives uniquement
$where  = "WHERE avancement IN ('en_cours', 'a_relancer')";
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
    $where   .= " AND DATE_FORMAT(updated_at, '%Y-%m') = ?";
    $params[] = $filterMonth;
}
$hasFilter = ($filterStatus !== 'all' || $filterHiring !== 'all' || $filterMonth !== '');

$stmt = $db->prepare("SELECT * FROM cv_applications $where ORDER BY updated_at DESC");
$stmt->execute($params);
$recent = $stmt->fetchAll();

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
  <button onclick="openDirectModal()" class="btn btn-outline">+ Candidature directe</button>
  <a href="knowledge-base.php" class="btn btn-ghost">Base de connaissance</a>
</div>

<div class="card" style="padding:16px 20px; margin-bottom:16px;">
  <form method="GET" class="flex flex-gap items-center" style="flex-wrap:wrap;">
    <select name="status" class="form-control" style="max-width:180px;">
      <option value="all">Tous les parcours</option>
      <?php foreach ($statusLabels as $val => $label): ?>
        <option value="<?= $val ?>" <?= $filterStatus === $val ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
    <select name="hiring" class="form-control" style="max-width:160px;">
      <option value="all">Tous les statuts</option>
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
  <div class="card-title">◷ Candidatures en cours<?= $hasFilter ? ' — filtrées' : '' ?></div>

  <?php if (empty($recent)): ?>
    <p class="text-muted text-center" style="padding:32px 0;">
      <?php if ($hasFilter): ?>
        Aucune candidature active ne correspond aux filtres.
      <?php else: ?>
        Aucune candidature en cours.<br>
        <a href="new-application.php" class="btn btn-gold btn-sm" style="margin-top:12px;">Commencer</a>
      <?php endif; ?>
    </p>
  <?php else: ?>
    <div class="applications-list">
      <?php foreach ($recent as $app):
        $hs       = $app['hiring_status']      ?? 'non_envoye';
        $av       = $app['avancement']          ?? 'en_cours';
        $dateCand = $app['date_candidature']    ?? '';
        $comment  = $app['commentaire_relance'] ?? '';
      ?>
        <div class="application-item" data-id="<?= $app['id'] ?>">

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

          <span class="application-date">
            <?= date('d/m/Y', strtotime($app['updated_at'])) ?>
          </span>

        </div>
      <?php endforeach; ?>
    </div>

    <div style="margin-top:16px;">
      <a href="history.php" class="btn btn-ghost btn-sm">Voir tout l'historique (<?= $totalApps ?>)</a>
    </div>
  <?php endif; ?>
</div>

<!-- ── Modal : candidature directe ──────────────────────── -->
<div id="modal-direct" class="modal-overlay" style="display:none;"
     onclick="if(event.target===this)closeDirectModal()">
  <div class="modal-box">
    <div class="modal-header">
      <h3>Candidature directe</h3>
      <button onclick="closeDirectModal()" class="modal-close" aria-label="Fermer">✕</button>
    </div>
    <div class="modal-body">
      <p class="text-muted" style="margin-bottom:16px;font-size:13px;">
        Enregistrez une candidature sans passer par le générateur de CV.
        Vous pourrez toujours y associer un CV plus tard.
      </p>
      <div class="grid-2">
        <div class="form-group">
          <label for="direct-company">Entreprise *</label>
          <input type="text" id="direct-company" class="form-control" placeholder="Nom de l'entreprise">
        </div>
        <div class="form-group">
          <label for="direct-position">Poste *</label>
          <input type="text" id="direct-position" class="form-control" placeholder="Intitulé du poste">
        </div>
      </div>
      <div class="grid-2">
        <div class="form-group">
          <label for="direct-date">Date d'envoi <span style="font-weight:400;text-transform:none;">(optionnel)</span></label>
          <input type="date" id="direct-date" class="form-control">
        </div>
        <div class="form-group">
          <label for="direct-hiring">Statut candidature</label>
          <select id="direct-hiring" class="form-control">
            <?php foreach ($hiringLabels as $val => $label): ?>
              <option value="<?= $val ?>"><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group" style="margin-bottom:0;">
        <label for="direct-source">URL de l'offre <span style="font-weight:400;text-transform:none;">(optionnel)</span></label>
        <input type="url" id="direct-source" class="form-control" placeholder="https://…">
      </div>
    </div>
    <div class="modal-footer">
      <button onclick="closeDirectModal()" class="btn btn-ghost">Annuler</button>
      <button onclick="saveDirectApplication()" class="btn btn-primary" id="btn-direct-save">Enregistrer</button>
    </div>
  </div>
</div>

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
// ── Hiring status ────────────────────────────────────────
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

// ── Avancement ───────────────────────────────────────────
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
      if (val === 'cloture') {
        const row = document.querySelector(`.application-item[data-id="${id}"]`);
        if (row) {
          row.style.transition = 'opacity 0.35s ease';
          row.style.opacity = '0';
          setTimeout(() => row.remove(), 380);
        }
      }
    }
  });
}

// ── Date candidature ─────────────────────────────────────
function updateDateCandidature(id, inputEl) {
  fetch('php/update-date-candidature.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id, date_candidature: inputEl.value})
  })
  .then(r => r.json())
  .then(d => { if (!d.success) alert(d.error || 'Erreur lors de la sauvegarde.'); });
}

// ── Commentaire de relance ───────────────────────────────
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
  const id         = document.getElementById('comment-app-id').value;
  const commentaire = document.getElementById('comment-text').value.trim();
  const btn        = document.getElementById('btn-comment-save');
  btn.disabled     = true;
  btn.textContent  = 'Enregistrement…';

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

// ── Candidature directe ──────────────────────────────────
function openDirectModal() {
  document.getElementById('modal-direct').style.display = 'flex';
  document.getElementById('direct-company').focus();
}

function closeDirectModal() {
  document.getElementById('modal-direct').style.display = 'none';
  ['direct-company','direct-position','direct-source','direct-date'].forEach(id => {
    document.getElementById(id).value = '';
  });
  document.getElementById('direct-hiring').value = 'non_envoye';
}

function saveDirectApplication() {
  const company          = document.getElementById('direct-company').value.trim();
  const position         = document.getElementById('direct-position').value.trim();
  const source_url       = document.getElementById('direct-source').value.trim();
  const date_candidature = document.getElementById('direct-date').value;
  const hiring_status    = document.getElementById('direct-hiring').value;

  if (!company || !position) {
    alert('Veuillez renseigner l\'entreprise et le poste.');
    return;
  }

  const btn = document.getElementById('btn-direct-save');
  btn.disabled    = true;
  btn.textContent = 'Enregistrement…';

  fetch('php/save-direct-application.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({company, position, source_url, date_candidature, hiring_status})
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      window.location.reload();
    } else {
      alert(d.error || 'Erreur lors de l\'enregistrement.');
      btn.disabled    = false;
      btn.textContent = 'Enregistrer';
    }
  })
  .catch(() => {
    alert('Erreur réseau.');
    btn.disabled    = false;
    btn.textContent = 'Enregistrer';
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
