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

$stmt = $db->prepare("SELECT * FROM cv_knowledge $where ORDER BY created_at DESC");
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
            <button class="btn btn-danger btn-sm" onclick="deleteEntry(<?= $entry['id'] ?>)">✕</button>
          </div>
        </div>

        <!-- Contenu complet (masqué) -->
        <div id="expand-<?= $entry['id'] ?>" class="hidden card" style="border-top:none; border-radius:0 0 8px 8px; padding:16px 20px; background:#fafaf8;">
          <pre style="white-space:pre-wrap; font-family:inherit; font-size:13px; line-height:1.6;"><?= htmlspecialchars($entry['content']) ?></pre>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
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

// Expand entry
function expandEntry(id) {
  const el = document.getElementById('expand-' + id);
  el.classList.toggle('hidden');
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
