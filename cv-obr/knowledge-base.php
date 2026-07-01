<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireLogin();

$db = getDB();

// Filtre par type
$filter = $_GET['type'] ?? 'all';
$search = trim($_GET['q'] ?? '');

$where  = 'WHERE is_active = 1';
$params = [];
if ($filter !== 'all') {
    $where   .= ' AND type = ?';
    $params[] = $filter;
}
if ($search !== '') {
    $where   .= ' AND (title LIKE ? OR content LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $db->prepare("SELECT * FROM obr_knowledge $where ORDER BY type, IFNULL(period_start, 0) DESC, created_at DESC");
$stmt->execute($params);
$entries = $stmt->fetchAll();

$typeLabels = [
    'experience'  => 'Expérience',
    'competence'  => 'Compétence',
    'formation'   => 'Formation',
    'import'      => 'Import libre',
    'autre'       => 'Autre',
];

$pageTitle  = 'Base de connaissance';
$activePage = 'knowledge';
require_once __DIR__ . '/includes/header.php';
?>

<div class="tabs">
  <button class="tab-btn active" onclick="switchTab('add')">+ Ajouter</button>
  <button class="tab-btn" onclick="switchTab('browse')">
    Parcourir (<?= count($entries) ?>)
  </button>
</div>

<!-- TAB : Ajouter une entrée -->
<div id="tab-add" class="tab-content active">
  <div class="card">
    <div class="card-title">◎ Nouvelle entrée</div>

    <div class="alert alert-info">
      Colle ici n'importe quel texte décrivant ton expérience : extrait de CV, notes d'entretien,
      description d'un projet, bilan de mission… L'app l'utilisera comme source lors des candidatures.
    </div>

    <form id="form-add-knowledge">
      <div class="grid-2">
        <div class="form-group">
          <label>Type</label>
          <select name="type" class="form-control" id="knowledge-type">
            <option value="experience">💼 Expérience</option>
            <option value="competence">🛠 Compétence</option>
            <option value="formation">🎓 Formation</option>
            <option value="import">📋 Import libre</option>
            <option value="autre">📝 Autre</option>
          </select>
        </div>
        <div class="form-group">
          <label>Titre (optionnel)</label>
          <input type="text" name="title" class="form-control"
                 placeholder="ex: Directeur de Programme ANAH">
        </div>
      </div>

      <!-- Champs structurés pour expérience -->
      <div id="experience-fields" class="hidden">
        <div class="grid-2">
          <div class="form-group">
            <label>Entreprise</label>
            <input type="text" name="company" class="form-control" placeholder="ANAH, Société Générale...">
          </div>
          <div class="form-group">
            <label>Poste</label>
            <input type="text" name="role" class="form-control" placeholder="Directeur de Programme">
          </div>
        </div>
        <div class="form-group">
          <label>Période</label>
          <input type="text" name="period" class="form-control" placeholder="ex: 2022 – 2024">
        </div>
      </div>

      <div class="form-group">
        <label>Contenu</label>
        <textarea name="content" class="form-control" rows="8"
                  placeholder="Décris ton expérience, tes compétences ou tes réalisations en détail. Plus c'est précis, mieux l'app pourra matcher avec les fiches de poste."></textarea>
      </div>

      <div class="flex flex-gap">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <button type="reset" class="btn btn-ghost">Effacer</button>
      </div>
      <div id="knowledge-msg" class="hidden alert" style="margin-top:12px;"></div>
    </form>
  </div>
</div>

<!-- TAB : Parcourir -->
<div id="tab-browse" class="tab-content">

  <!-- Filtres -->
  <div class="card" style="padding:16px 20px; margin-bottom:16px;">
    <form method="GET" class="flex flex-gap items-center">
      <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
             class="form-control" placeholder="Rechercher..." style="max-width:260px;">
      <select name="type" class="form-control" style="max-width:200px;">
        <option value="all" <?= $filter==='all'?'selected':'' ?>>Tous les types</option>
        <?php foreach ($typeLabels as $val => $label): ?>
          <option value="<?= $val ?>" <?= $filter===$val?'selected':'' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-outline btn-sm">Filtrer</button>
      <?php if ($filter !== 'all' || $search !== ''): ?>
        <a href="knowledge-base.php" class="btn btn-ghost btn-sm">Réinitialiser</a>
      <?php endif; ?>
    </form>
  </div>

  <?php if (empty($entries)): ?>
    <div class="card text-center" style="padding:40px;">
      <p class="text-muted">Aucune entrée trouvée.</p>
    </div>
  <?php else: ?>
    <div class="knowledge-list">
      <?php foreach ($entries as $entry): ?>
        <div class="knowledge-item" id="entry-<?= $entry['id'] ?>">
          <div class="knowledge-body">
            <div class="flex items-center flex-gap mb-4" style="margin-bottom:6px;">
              <span class="badge badge-<?= $entry['type'] ?>"><?= $typeLabels[$entry['type']] ?></span>
              <?php if ($entry['title']): ?>
                <span class="knowledge-title"><?= htmlspecialchars($entry['title']) ?></span>
              <?php endif; ?>
              <span class="text-muted" style="font-size:12px; margin-left:auto;">
                <?= date('d/m/Y', strtotime($entry['created_at'])) ?>
              </span>
            </div>
            <div class="knowledge-preview"><?= htmlspecialchars($entry['content']) ?></div>
          </div>
          <div class="knowledge-actions">
            <button class="btn btn-ghost btn-sm" onclick="expandEntry(<?= $entry['id'] ?>)">Voir</button>
            <button class="btn btn-outline btn-sm" onclick="editEntry(<?= $entry['id'] ?>)">Modifier</button>
            <button class="btn btn-danger btn-sm" onclick="deleteEntry(<?= $entry['id'] ?>)">✕</button>
          </div>
        </div>

        <!-- Contenu complet / formulaire d'édition (masqué par défaut) -->
        <?php
          $meta    = $entry['meta_json'] ? json_decode($entry['meta_json'], true) : [];
          $eRole   = htmlspecialchars($meta['role']    ?? '');
          $eComp   = htmlspecialchars($meta['company'] ?? '');
          $ePeriod = htmlspecialchars($meta['period']  ?? '');
          $hasMeta = $entry['type'] === 'experience' && ($eRole || $eComp || $ePeriod);
        ?>
        <div id="expand-<?= $entry['id'] ?>" class="hidden card" style="border-top:none; border-radius:0 0 8px 8px; padding:0;">
          <?php if ($hasMeta): ?>
          <div style="display:flex; justify-content:space-between; align-items:baseline; padding:10px 20px; font-size:12px; border-bottom:1px solid var(--border);">
            <span><strong><?= $eRole ?></strong><?= $eComp ? ' &middot; ' . $eComp : '' ?></span>
            <span style="color:var(--text-muted);"><?= $ePeriod ?></span>
          </div>
          <?php endif; ?>
          <pre style="white-space:pre-wrap; font-family:inherit; font-size:13px; line-height:1.6; padding:16px 20px; margin:0;"><?= htmlspecialchars($entry['content']) ?></pre>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
// Données des entrées (pour l'édition inline)
const knowledgeData = {
<?php foreach ($entries as $e):
    $meta = $e['meta_json'] ? json_decode($e['meta_json'], true) : []; ?>
  <?= (int)$e['id'] ?>: {
    type:    <?= json_encode($e['type']) ?>,
    title:   <?= json_encode($e['title'] ?? '') ?>,
    content: <?= json_encode($e['content']) ?>,
    company: <?= json_encode($meta['company'] ?? '') ?>,
    role:    <?= json_encode($meta['role'] ?? '') ?>,
    period:  <?= json_encode($meta['period'] ?? '') ?>,
  },
<?php endforeach; ?>
};

// Tabs
function switchTab(name) {
  document.querySelectorAll('.tab-btn').forEach((b,i) => {
    b.classList.toggle('active', ['add','browse'][i] === name);
  });
  document.querySelectorAll('.tab-content').forEach((c,i) => {
    c.classList.toggle('active', ['tab-add','tab-browse'][i] === 'tab-' + name);
  });
}

// Champs expérience conditionnels
document.getElementById('knowledge-type').addEventListener('change', function() {
  document.getElementById('experience-fields').classList.toggle('hidden', this.value !== 'experience');
});

// Échapper les caractères HTML
function esc(s) {
  return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Messages inline
function showMsg(el, text, type) {
  if (!el) return;
  el.textContent = text;
  el.className = 'alert alert-' + type;
  el.classList.remove('hidden');
  if (type === 'success') setTimeout(() => el.classList.add('hidden'), 3000);
}

// Expand entry (mode lecture)
function expandEntry(id) {
  const el = document.getElementById('expand-' + id);
  // Si on est en mode édition, revenir d'abord en lecture
  if (el.querySelector('select, textarea')) cancelEdit(id);
  else el.classList.toggle('hidden');
}

// Ouvrir le formulaire d'édition inline
function editEntry(id) {
  const d = knowledgeData[id];
  if (!d) return;
  const isExp = d.type === 'experience';
  const expEl = document.getElementById('expand-' + id);
  expEl.innerHTML = `
    <div style="padding:16px 20px;">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
        <div>
          <label style="font-size:12px;font-weight:600;color:#6b6b65;display:block;margin-bottom:4px;">Type</label>
          <select id="edit-type-${id}" class="form-control" style="height:36px;" onchange="toggleEditExpFields(${id})">
            <option value="experience" ${d.type==='experience'?'selected':''}>Expérience</option>
            <option value="competence" ${d.type==='competence'?'selected':''}>Compétence</option>
            <option value="formation"  ${d.type==='formation' ?'selected':''}>Formation</option>
            <option value="import"     ${d.type==='import'    ?'selected':''}>Import libre</option>
            <option value="autre"      ${d.type==='autre'     ?'selected':''}>Autre</option>
          </select>
        </div>
        <div>
          <label style="font-size:12px;font-weight:600;color:#6b6b65;display:block;margin-bottom:4px;">Titre</label>
          <input type="text" id="edit-title-${id}" class="form-control" value="${esc(d.title)}" style="height:36px;">
        </div>
      </div>
      <div id="edit-expfields-${id}" style="${isExp?'':'display:none;'}margin-bottom:12px;">
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
          <div>
            <label style="font-size:12px;font-weight:600;color:#6b6b65;display:block;margin-bottom:4px;">Entreprise</label>
            <input type="text" id="edit-company-${id}" class="form-control" value="${esc(d.company)}" style="height:36px;" placeholder="Entreprise">
          </div>
          <div>
            <label style="font-size:12px;font-weight:600;color:#6b6b65;display:block;margin-bottom:4px;">Poste</label>
            <input type="text" id="edit-role-${id}" class="form-control" value="${esc(d.role)}" style="height:36px;" placeholder="Poste">
          </div>
          <div>
            <label style="font-size:12px;font-weight:600;color:#6b6b65;display:block;margin-bottom:4px;">Période</label>
            <input type="text" id="edit-period-${id}" class="form-control" value="${esc(d.period)}" style="height:36px;" placeholder="2022–2024">
          </div>
        </div>
      </div>
      <div>
        <label style="font-size:12px;font-weight:600;color:#6b6b65;display:block;margin-bottom:4px;">Contenu</label>
        <textarea id="edit-content-${id}" class="form-control" rows="8">${esc(d.content)}</textarea>
      </div>
      <div class="flex flex-gap" style="margin-top:12px;">
        <button class="btn btn-primary btn-sm" onclick="saveEntry(${id})">Enregistrer</button>
        <button class="btn btn-ghost btn-sm" onclick="cancelEdit(${id})">Annuler</button>
      </div>
      <div id="edit-msg-${id}" class="hidden alert" style="margin-top:8px;font-size:13px;"></div>
    </div>`;
  expEl.classList.remove('hidden');
}

// Construit le HTML du panneau lecture (méta + contenu)
function buildExpandContent(d) {
  let meta = '';
  if (d.type === 'experience' && (d.role || d.company || d.period)) {
    meta = `<div style="display:flex;justify-content:space-between;align-items:baseline;padding:10px 20px;font-size:12px;border-bottom:1px solid var(--border);">
      <span><strong>${esc(d.role)}</strong>${d.company ? ' &middot; ' + esc(d.company) : ''}</span>
      <span style="color:var(--text-muted);">${esc(d.period)}</span>
    </div>`;
  }
  return meta + `<pre style="white-space:pre-wrap;font-family:inherit;font-size:13px;line-height:1.6;padding:16px 20px;margin:0;">${esc(d.content)}</pre>`;
}

// Annuler l'édition → revenir en mode lecture
function cancelEdit(id) {
  const d = knowledgeData[id];
  const expEl = document.getElementById('expand-' + id);
  expEl.innerHTML = buildExpandContent(d);
  expEl.classList.add('hidden');
}

// Afficher/masquer les champs expérience dans le formulaire d'édition
function toggleEditExpFields(id) {
  const type    = document.getElementById('edit-type-' + id)?.value;
  const fieldsEl = document.getElementById('edit-expfields-' + id);
  if (fieldsEl) fieldsEl.style.display = type === 'experience' ? '' : 'none';
}

// Enregistrer les modifications
function saveEntry(id) {
  const type    = document.getElementById('edit-type-' + id)?.value;
  const title   = document.getElementById('edit-title-' + id)?.value?.trim() ?? '';
  const content = document.getElementById('edit-content-' + id)?.value?.trim() ?? '';
  const company = document.getElementById('edit-company-' + id)?.value?.trim() ?? '';
  const role    = document.getElementById('edit-role-' + id)?.value?.trim() ?? '';
  const period  = document.getElementById('edit-period-' + id)?.value?.trim() ?? '';
  const msgEl   = document.getElementById('edit-msg-' + id);

  if (!content) { showMsg(msgEl, 'Le contenu est obligatoire.', 'error'); return; }

  fetch('php/update-knowledge.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id, type, title, content, company, role, period })
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      knowledgeData[id] = { type, title, content, company, role, period };
      cancelEdit(id);
      const entryEl = document.getElementById('entry-' + id);
      if (entryEl) {
        entryEl.style.transition = 'background .3s';
        entryEl.style.background = '#f0faf4';
        setTimeout(() => entryEl.style.background = '', 1800);
      }
    } else {
      showMsg(msgEl, d.error || 'Erreur lors de la sauvegarde.', 'error');
    }
  });
}

// Supprimer entrée
function deleteEntry(id) {
  if (!confirm('Supprimer cette entrée ?')) return;
  fetch('php/delete-knowledge.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id})
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      document.getElementById('entry-' + id).remove();
      const exp = document.getElementById('expand-' + id);
      if (exp) exp.remove();
    }
  });
}

// Sauvegarder
document.getElementById('form-add-knowledge').addEventListener('submit', function(e) {
  e.preventDefault();
  const data = Object.fromEntries(new FormData(this));
  const msg  = document.getElementById('knowledge-msg');

  fetch('php/save-knowledge.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  })
  .then(r => r.json())
  .then(d => {
    msg.className = 'alert ' + (d.success ? 'alert-success' : 'alert-error');
    msg.textContent = d.success ? 'Entrée enregistrée.' : (d.error || 'Erreur.');
    msg.classList.remove('hidden');
    if (d.success) { this.reset(); setTimeout(() => msg.classList.add('hidden'), 3000); }
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
