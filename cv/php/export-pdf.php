<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die('ID invalide.');

$db   = getDB();
$stmt = $db->prepare("SELECT cv_content, position, company FROM cv_applications WHERE id = ? AND status = 'completed'");
$stmt->execute([$id]);
$app  = $stmt->fetch();
if (!$app) die('CV introuvable ou non finalisé.');

// Nom du fichier PDF suggéré
$pos      = $app['position'] ?? '';
$ascii    = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $pos);
$clean    = strtoupper(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $ascii)));
$slug     = preg_replace('/\s+/', '_', $clean);
$filename = 'CV_GFAY_' . ($slug ?: 'CV');
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($filename) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --aubergine: #6D155D;
      --font-title: 'Cormorant Garamond', Georgia, serif;
      --font-body:  'DM Sans', system-ui, sans-serif;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: var(--font-body);
      font-size: 11.5pt;
      color: #1c1c18;
      background: #fff;
    }

    /* ── Barre d'action (masquée à l'impression) ── */
    #print-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 10px 24px;
      background: #6D155D;
      color: #fff;
      font-size: 13px;
      font-family: var(--font-body);
    }
    #print-bar strong { font-weight: 600; }
    #print-bar button {
      padding: 7px 20px;
      background: #D3A625;
      color: #3a0d2e;
      border: none;
      border-radius: 20px;
      font-weight: 700;
      font-size: 13px;
      cursor: pointer;
      font-family: var(--font-body);
    }
    #print-bar button:hover { background: #e8bc3a; }

    /* ── CV ── */
    #cv-wrap {
      max-width: 800px;
      margin: 24px auto;
      padding: 48px 52px;
      background: #fff;
      line-height: 1.55;
    }

    .cv-header { text-align: center; margin-bottom: 18px; }
    h1 {
      font-family: var(--font-title);
      font-size: 26px;
      font-weight: 700;
      letter-spacing: 2px;
      color: #1c1c18;
      margin: 0 0 6px;
      text-transform: uppercase;
    }
    .cv-subtitle { font-size: 13.5px; font-weight: 600; color: var(--aubergine); margin: 0 0 5px; }
    .cv-tagline  { font-size: 11px; color: #555; margin: 0 0 4px; }
    .cv-contact  { font-size: 11px; color: #666; margin: 0; }

    .cv-section { margin-bottom: 15px; }
    .cv-section-title {
      font-size: 9.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.7px;
      color: var(--aubergine);
      border-bottom: 1px solid var(--aubergine);
      padding-bottom: 3px;
      margin: 0 0 9px;
    }
    p { margin: 0 0 6px; font-size: 12px; line-height: 1.55; }

    .cv-list-2col {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3px 28px;
      list-style: none;
      padding: 0; margin: 0;
      font-size: 11.5px;
    }
    .cv-list-2col li { position: relative; padding-left: 13px; }
    .cv-list-2col li::before { content: '▸'; color: var(--aubergine); position: absolute; left: 0; }

    .cv-job { margin-bottom: 12px; }
    .cv-job-header { display: flex; justify-content: space-between; align-items: baseline; gap: 16px; }
    .cv-job-title  { font-size: 12px; flex: 1; }
    .cv-job-date   { font-size: 11px; color: #555; white-space: nowrap; flex-shrink: 0; }
    .cv-job-context{ font-size: 11px; color: #555; margin: 2px 0 5px; font-style: italic; }
    .cv-job-bullets { list-style: none; padding: 0; margin: 0; }
    .cv-job-bullets li {
      font-size: 11.5px; padding-left: 14px;
      margin-bottom: 3px; position: relative; line-height: 1.5;
    }
    .cv-job-bullets li::before { content: '▸'; color: var(--aubergine); position: absolute; left: 0; }
    .cv-job-note { font-size: 11px; color: #777; font-style: italic; margin-top: 3px; }
    strong { font-weight: 600; }
    em { font-style: italic; }

    /* ── Impression ── */
    @media print {
      #print-bar { display: none !important; }
      #cv-wrap { margin: 0; padding: 12mm 14mm; max-width: 100%; box-shadow: none; }
      body { background: #fff; }
    }
  </style>
</head>
<body>

<div id="print-bar">
  <span>
    Fichier : <strong><?= htmlspecialchars($filename) ?>.pdf</strong>
    &nbsp;·&nbsp; Dans la fenêtre d'impression, sélectionne <strong>Enregistrer en PDF</strong>
  </span>
  <button onclick="window.print()">🖨 Enregistrer en PDF</button>
</div>

<div id="cv-wrap">
  <?= $app['cv_content'] ?>
</div>

<script>
  // Attendre le chargement des polices avant d'imprimer
  if (document.fonts) {
    document.fonts.ready.then(() => window.print());
  } else {
    window.onload = () => setTimeout(() => window.print(), 600);
  }
</script>

</body>
</html>
