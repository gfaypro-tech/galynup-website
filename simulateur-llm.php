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
  <title>Simulateur coût LLM + hallucination — Galyn'Up</title>
  <meta name="description" content="Comparez le coût mensuel et le taux d'hallucination des principaux LLM pour votre projet IA. Outil DSI gratuit par Galyn'Up.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    :root {
      --aubergine: #6D155D;
      --aubergine-hover: #8a1c77;
      --aubergine-pale: #f7eef5;
      --aubergine-border: rgba(109,21,93,0.18);
      --gold: #D3A625;
      --gold-pale: #fdf8ec;
      --gold-border: rgba(211,166,37,0.4);
      --white: #ffffff;
      --off-white: #fafaf8;
      --gray-50: #f6f6f4;
      --gray-100: #eeede9;
      --gray-200: #dddcd7;
      --gray-400: #9e9d96;
      --gray-600: #5c5b55;
      --text: #1c1c18;
      --green: #2d6e1f;  --green-pale: #eef6eb;
      --blue: #1a5da0;   --blue-pale: #e8f2fc;
      --amber: #8a5e10;  --amber-pale: #fdf1dc;
      --red: #b83232;    --red-pale: #fdeaea;
      --radius: 12px; --radius-sm: 7px;
      --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 14px rgba(0,0,0,0.04);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DM Sans', sans-serif; font-size: 14px; color: var(--text); background: var(--off-white); min-height: 100vh; }

    /* HEADER */
    header { background: var(--aubergine); padding: 22px 36px; display: flex; align-items: center; justify-content: space-between; }
    .brand-name { font-family: 'Cormorant Garamond', serif; font-weight: 600; font-size: 24px; color: #fff; letter-spacing: 0.03em; }
    .brand-name span { color: var(--gold); }
    .brand-sub { font-size: 10px; font-weight: 300; color: rgba(255,255,255,0.5); letter-spacing: 0.14em; text-transform: uppercase; margin-top: 2px; }
    .header-badge { font-size: 11px; font-weight: 500; color: rgba(255,255,255,0.7); border: 1px solid rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px; }

    /* LAYOUT */
    main { max-width: 1000px; margin: 0 auto; padding: 170px 20px 64px; }
    .page-title { font-family: 'Cormorant Garamond', serif; font-weight: 600; font-size: 32px; color: var(--aubergine); line-height: 1.15; margin-bottom: 8px; }
    .page-sub { font-size: 13px; color: var(--gray-600); line-height: 1.65; max-width: 620px; margin-bottom: 30px; }

    /* PANEL */
    .panel { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 22px 24px; margin-bottom: 16px; box-shadow: var(--shadow); }
    .panel-title { font-size: 10px; font-weight: 500; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 18px; display: flex; align-items: center; gap: 10px; }
    .panel-title::after { content: ''; flex: 1; height: 1px; background: var(--gray-100); }

    /* GRID */
    .input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 28px; }
    .field label { font-size: 12px; color: var(--gray-600); display: flex; align-items: center; gap: 5px; margin-bottom: 6px; }
    .field-row { display: flex; align-items: center; gap: 10px; }
    input[type=range] { flex: 1; -webkit-appearance: none; height: 3px; background: var(--gray-200); border-radius: 2px; outline: none; cursor: pointer; }
    input[type=range]::-webkit-slider-thumb { -webkit-appearance: none; width: 17px; height: 17px; border-radius: 50%; background: var(--aubergine); border: 2px solid #fff; box-shadow: 0 0 0 1px var(--aubergine); cursor: pointer; }
    input[type=range]::-moz-range-thumb { width: 17px; height: 17px; border-radius: 50%; background: var(--aubergine); border: 2px solid #fff; box-shadow: 0 0 0 1px var(--aubergine); cursor: pointer; }
    .field-val { font-size: 13px; font-weight: 500; color: var(--aubergine); min-width: 72px; text-align: right; }

    /* SELECT */
    select { width: 100%; padding: 9px 12px; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--text); background: var(--gray-50); outline: none; cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%239e9d96' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px; }
    select:focus { border-color: var(--aubergine); }

    /* TOOLTIP */
    .info-btn { display: inline-flex; align-items: center; justify-content: center; width: 15px; height: 15px; border-radius: 50%; background: var(--gray-200); color: var(--gray-600); font-size: 9px; font-weight: 700; cursor: pointer; border: none; font-family: 'DM Sans', sans-serif; flex-shrink: 0; position: relative; line-height: 1; }
    .info-btn:hover { background: var(--aubergine); color: #fff; }
    .tooltip-wrap { position: relative; display: inline-flex; }
    .tooltip-box { display: none; position: absolute; bottom: calc(100% + 8px); left: 50%; transform: translateX(-50%); background: var(--text); color: #fff; font-size: 11px; line-height: 1.55; padding: 9px 12px; border-radius: 7px; width: 220px; z-index: 100; pointer-events: none; }
    .tooltip-box::after { content: ''; position: absolute; top: 100%; left: 50%; transform: translateX(-50%); border: 5px solid transparent; border-top-color: var(--text); }
    .tooltip-wrap:hover .tooltip-box,
    .tooltip-wrap:focus-within .tooltip-box { display: block; }

    /* MODEL SELECTORS */
    .model-selectors { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 20px; }
    .model-selector-card { background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: var(--radius-sm); padding: 14px 16px; }
    .model-selector-label { font-size: 10px; font-weight: 500; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .model-selector-label .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .dot-in  { background: var(--aubergine); }
    .dot-out { background: var(--gold); }
    .model-info-row { margin-top: 8px; font-size: 11px; color: var(--gray-400); display: flex; gap: 12px; flex-wrap: wrap; }
    .model-info-row b { color: var(--gray-600); font-weight: 500; }

    /* LANG */
    .lang-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 18px; }
    .lang-note { margin-top: 10px; font-size: 12px; color: var(--gray-600); line-height: 1.55; padding: 9px 12px; background: var(--gray-50); border-radius: var(--radius-sm); border: 1px solid var(--gray-100); }

    /* COST SUMMARY */
    .cost-summary { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 20px; }
    .cost-card { border-radius: var(--radius-sm); padding: 14px 16px; }
    .cost-card-in  { background: var(--aubergine-pale); border: 1px solid var(--aubergine-border); }
    .cost-card-out { background: var(--gold-pale);      border: 1px solid var(--gold-border); }
    .cost-card-total { background: var(--text); }
    .cost-card-label { font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.09em; margin-bottom: 4px; }
    .cost-card-in    .cost-card-label  { color: var(--aubergine); }
    .cost-card-out   .cost-card-label  { color: #7a5400; }
    .cost-card-total .cost-card-label  { color: rgba(255,255,255,0.6); }
    .cost-card-model { font-size: 11px; margin-bottom: 6px; }
    .cost-card-in    .cost-card-model  { color: var(--aubergine); }
    .cost-card-out   .cost-card-model  { color: #7a5400; }
    .cost-card-total .cost-card-model  { color: rgba(255,255,255,0.7); }
    .cost-card-amount { font-size: 24px; font-weight: 500; }
    .cost-card-in    .cost-card-amount { color: var(--aubergine); }
    .cost-card-out   .cost-card-amount { color: #7a5400; }
    .cost-card-total .cost-card-amount { color: #fff; }

    /* SORT BAR */
    .sort-bar { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; flex-wrap: wrap; }
    .sort-bar span { font-size: 12px; color: var(--gray-600); }
    .sort-btn { font-size: 11px; font-weight: 500; padding: 5px 12px; border: 1px solid var(--gray-200); border-radius: 20px; cursor: pointer; background: transparent; color: var(--gray-600); font-family: 'DM Sans', sans-serif; transition: all 0.12s; }
    .sort-btn:hover { border-color: var(--aubergine); color: var(--aubergine); }
    .sort-btn.active { background: var(--aubergine); color: #fff; border-color: var(--aubergine); }

    /* TABLE */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    thead tr { border-bottom: 1px solid var(--gray-200); }
    th { font-size: 10px; font-weight: 500; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.08em; padding: 0 8px 10px; text-align: left; white-space: nowrap; }
    th.r, td.r { text-align: right; }
    td { padding: 10px 8px; border-bottom: 1px solid var(--gray-50); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr.sel-in  td { background: rgba(109,21,93,0.04); }
    tr.sel-out td { background: rgba(211,166,37,0.06); }
    tr.sel-both td { background: linear-gradient(90deg, rgba(109,21,93,0.06), rgba(211,166,37,0.08)); }
    .model-name { font-weight: 500; color: var(--text); font-size: 13px; }
    .model-sub { font-size: 11px; color: var(--gray-400); margin-top: 1px; }
    .sel-pill { display: inline-block; font-size: 9px; font-weight: 500; padding: 1px 6px; border-radius: 10px; margin-left: 5px; vertical-align: middle; }
    .sel-pill-in   { background: var(--aubergine); color: #fff; }
    .sel-pill-out  { background: var(--gold); color: #fff; }

    /* TAGS */
    .tag { display: inline-block; font-size: 10px; font-weight: 500; padding: 2px 7px; border-radius: 4px; white-space: nowrap; }
    .tag-green { background: var(--green-pale); color: var(--green); }
    .tag-blue  { background: var(--blue-pale);  color: var(--blue);  }
    .tag-amber { background: var(--amber-pale); color: var(--amber); }
    .tag-gray  { background: var(--gray-100);   color: var(--gray-600); }
    .tag-red   { background: var(--red-pale);   color: var(--red); }
    .price-sm { font-size: 12px; color: var(--gray-600); }
    .cost-big { font-size: 15px; font-weight: 500; color: var(--text); }

    /* HALLUCINATION */
    .halu-cell { min-width: 110px; }
    .halu-val { font-size: 12px; font-weight: 500; margin-bottom: 3px; }
    .halu-bar-bg { height: 4px; background: var(--gray-100); border-radius: 2px; width: 80px; }
    .halu-bar-fill { height: 4px; border-radius: 2px; }
    .halu-nd { font-size: 11px; color: var(--gray-400); font-style: italic; }

    /* COMBO BUTTON */
    .combo-btn-wrap { display: flex; justify-content: center; margin: 22px 0 4px; }
    .combo-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; background: var(--aubergine); color: #fff; font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500; border: none; border-radius: var(--radius-sm); cursor: pointer; transition: background 0.15s, transform 0.1s; }
    .combo-btn:hover { background: var(--aubergine-hover); }
    .combo-btn:active { transform: scale(0.98); }

    /* COMBO RESULT */
    .combo-result { display: none; margin-top: 16px; border: 1px solid var(--aubergine-border); border-radius: var(--radius); overflow: hidden; }
    .combo-header { background: var(--aubergine); padding: 14px 20px; }
    .combo-header-title { font-family: 'Cormorant Garamond', serif; font-size: 18px; font-weight: 600; color: #fff; }
    .combo-body { padding: 18px 20px; background: var(--aubergine-pale); }
    .combo-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
    .combo-card { background: #fff; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); padding: 14px 16px; }
    .combo-card-label { font-size: 10px; font-weight: 500; color: var(--gray-400); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 6px; }
    .combo-card-model { font-weight: 500; font-size: 14px; color: var(--text); }
    .combo-card-provider { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
    .combo-card-cost { font-size: 20px; font-weight: 500; color: var(--aubergine); margin-top: 8px; }
    .combo-total { text-align: center; font-size: 13px; color: var(--gray-600); margin-bottom: 12px; }
    .combo-total strong { font-size: 20px; color: var(--aubergine); font-weight: 500; }
    .combo-warning { background: var(--amber-pale); border: 1px solid rgba(138,94,16,0.2); border-radius: var(--radius-sm); padding: 11px 14px; font-size: 12px; color: var(--amber); line-height: 1.55; display: flex; gap: 8px; align-items: flex-start; }

    /* SOURCE BOX */
    .source-box { background: var(--gray-50); border: 1px solid var(--gray-100); border-radius: var(--radius-sm); padding: 12px 16px; margin-top: 14px; font-size: 11px; color: var(--gray-600); line-height: 1.7; }
    .source-box b { font-weight: 500; }
    .source-box a { color: var(--aubergine); text-decoration: none; }

    /* CTA */
    .cta-bar { margin-top: 28px; padding: 18px 22px; background: var(--aubergine-pale); border: 1px solid var(--aubergine-border); border-radius: var(--radius); display: flex; align-items: center; justify-content: space-between; gap: 16px; }
    .cta-text { font-size: 13px; color: var(--aubergine); line-height: 1.5; }
    .cta-text strong { font-weight: 500; display: block; margin-bottom: 2px; }
    .cta-link { flex-shrink: 0; display: inline-block; padding: 10px 20px; background: var(--aubergine); color: #fff; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; border-radius: var(--radius-sm); text-decoration: none; white-space: nowrap; }
    .cta-link:hover { background: var(--aubergine-hover); }

    footer { text-align: center; padding: 22px; font-size: 11px; color: var(--gray-400); border-top: 1px solid var(--gray-200); }
    footer a { color: var(--aubergine); text-decoration: none; }

    /* LOADER */
    .calc-indicator { display: inline-flex; align-items: center; gap: 6px; font-size: 10px; font-weight: 400; color: var(--aubergine); opacity: 0; transition: opacity 0.15s; margin-left: 8px; text-transform: none; letter-spacing: 0; }
    .calc-indicator.visible { opacity: 1; }
    .calc-spinner { width: 11px; height: 11px; border: 1.5px solid rgba(109,21,93,0.2); border-top-color: var(--aubergine); border-radius: 50%; animation: spin 0.6s linear infinite; flex-shrink: 0; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .field-val.updating { animation: pulse-val 0.3s ease; }
    @keyframes pulse-val { 0% { opacity:1; } 40% { opacity:0.3; } 100% { opacity:1; } }

    /* GATE */
    .gate-wrap { position: relative; }
    .gate-overlay { position: absolute; inset: 0; backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); background: rgba(250,250,248,0.80); border-radius: var(--radius); display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 10; gap: 14px; padding: 32px 24px; text-align: center; }
    .gate-lock { font-size: 40px; line-height: 1; }
    .gate-title { font-family: 'Cormorant Garamond', serif; font-size: 22px; font-weight: 600; color: var(--aubergine); }
    .gate-sub { font-size: 13px; color: var(--gray-600); line-height: 1.65; max-width: 400px; }
    .gate-btn { display: inline-block; padding: 12px 26px; background: var(--aubergine); color: #fff; border-radius: var(--radius-sm); font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; text-decoration: none; transition: background 0.15s; }
    .gate-btn:hover { background: var(--aubergine-hover); }
    .apercu-note { margin-top: 14px; font-size: 12px; color: var(--gray-600); padding: 10px 14px; background: var(--gold-pale); border: 1px solid var(--gold-border); border-radius: var(--radius-sm); line-height: 1.55; }

    @media (max-width: 640px) {
      header { padding: 16px; flex-direction: column; align-items: flex-start; gap: 8px; }
      main { padding: 24px 14px 48px; }
      .input-grid, .lang-grid, .combo-cards, .model-selectors, .cost-summary { grid-template-columns: 1fr; }
      .cta-bar { flex-direction: column; }
      .page-title { font-size: 26px; }
    }
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
                            <a href="simulateur-llm.php" class="nav-dropdown-item active">🧮 Simulateur LLM</a>
                            <a href="audit-dsi.php" class="nav-dropdown-item">◈ Audit Maturité DSI</a>
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

<main>
  <h1 class="page-title">Simulateur LLM — Coût & Fiabilité</h1>
  <p class="page-sub">
    Estimez le coût mensuel de votre solution IA en combinant librement un modèle pour l'input et un modèle pour l'output — et comparez leur taux d'hallucination.
  </p>

  <!-- PANEL 1 : VOLUMES -->
  <div class="panel">
    <div class="panel-title">Volumes de tokens<span class="calc-indicator" id="calc-indicator"><span class="calc-spinner"></span>Calcul en cours…</span></div>
    <div class="input-grid">
      <div class="field">
        <label>
          Tokens input / mois
          <div class="tooltip-wrap">
            <button class="info-btn" tabindex="0">i</button>
            <div class="tooltip-box">Ce que vous envoyez au modèle : vos documents, vos instructions système, le contexte de conversation. 1 page A4 ≈ 500 tokens. Un email ≈ 150 tokens.</div>
          </div>
        </label>
        <div class="field-row">
          <input type="range" id="sl-in" min="100000" max="50000000" value="5000000" step="100000" oninput="update()">
          <span class="field-val" id="out-in">5 M</span>
        </div>
      </div>
      <div class="field">
        <label>
          Tokens output / mois
          <div class="tooltip-wrap">
            <button class="info-btn" tabindex="0">i</button>
            <div class="tooltip-box">Ce que le modèle génère en réponse : synthèses, analyses, réponses RAG, rapports. En général, l'output représente 10 à 30 % du volume de l'input.</div>
          </div>
        </label>
        <div class="field-row">
          <input type="range" id="sl-out" min="10000" max="10000000" value="1000000" step="10000" oninput="update()">
          <span class="field-val" id="out-out">1 M</span>
        </div>
      </div>
    </div>

    <div class="lang-grid">
      <div class="field">
        <label>
          Langue de l'input
          <div class="tooltip-wrap">
            <button class="info-btn" tabindex="0">i</button>
            <div class="tooltip-box">La langue de vos documents affecte le nombre de tokens. Le français génère ~20–30 % de tokens supplémentaires vs l'anglais pour un contenu équivalent.</div>
          </div>
        </label>
        <select id="lang-in" onchange="update()">
          <option value="fr" selected>🇫🇷 Français</option>
          <option value="en">🇬🇧 Anglais</option>
        </select>
      </div>
      <div class="field">
        <label>
          Langue de l'output
          <div class="tooltip-wrap">
            <button class="info-btn" tabindex="0">i</button>
            <div class="tooltip-box">La langue des réponses générées. Choisir l'anglais réduit le coût output d'environ 20 % par rapport au français.</div>
          </div>
        </label>
        <select id="lang-out" onchange="update()">
          <option value="fr" selected>🇫🇷 Français</option>
          <option value="en">🇬🇧 Anglais</option>
        </select>
      </div>
    </div>
    <div class="lang-note" id="lang-note"></div>
  </div>

  <!-- PANEL 2 : CHOIX DES MODÈLES -->
  <div class="panel">
    <div class="panel-title">Choix des modèles LLM</div>
    <div class="model-selectors">
      <div class="model-selector-card">
        <div class="model-selector-label">
          <span class="dot dot-in"></span>
          Modèle pour l'input
          <div class="tooltip-wrap">
            <button class="info-btn" tabindex="0">i</button>
            <div class="tooltip-box">Le modèle qui traite vos documents en entrée (ingestion, embedding, analyse). Vous pouvez choisir un modèle moins coûteux si la tâche est simple.</div>
          </div>
        </div>
        <select id="model-in" onchange="update()"></select>
        <div class="model-info-row" id="info-in"></div>
      </div>
      <div class="model-selector-card">
        <div class="model-selector-label">
          <span class="dot dot-out"></span>
          Modèle pour l'output
          <div class="tooltip-wrap">
            <button class="info-btn" tabindex="0">i</button>
            <div class="tooltip-box">Le modèle qui génère les réponses (RAG, synthèses, rapports). L'output est souvent plus coûteux — choisir un modèle adapté à la complexité de la tâche est clé.</div>
          </div>
        </div>
        <select id="model-out" onchange="update()"></select>
        <div class="model-info-row" id="info-out"></div>
      </div>
    </div>

    <!-- RÉCAP COÛT -->
    <div class="cost-summary" style="margin-top: 18px;" id="cost-summary"></div>
  </div>

  <!-- APERÇU STATIQUE -->
  <div class="panel">
    <div class="panel-title">Aperçu — exemple de comparaison</div>
    <p style="font-size:12px;color:var(--gray-600);margin-bottom:14px;">Volumes de référence : <strong>5 M tokens input + 1 M tokens output / mois</strong>, corpus en français.</p>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Modèle</th>
            <th>Provider</th>
            <th>Région</th>
            <th class="r">Input /1M</th>
            <th class="r">Output /1M</th>
            <th class="r">Coût mensuel</th>
            <th>Hallucination</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><div class="model-name">Gemini 2.0 Flash</div><div class="model-sub">Google</div></td>
            <td><span class="tag tag-amber">US</span></td>
            <td></td>
            <td class="r"><span class="price-sm">$0.10</span></td>
            <td class="r"><span class="price-sm">$0.40</span></td>
            <td class="r"><span class="cost-big">$1.1</span></td>
            <td><div class="halu-cell"><div class="halu-val" style="color:#2d6e1f">0.7 % <span style="font-size:9px;color:var(--gray-400)">avr. 2025</span></div><div class="halu-bar-bg"><div class="halu-bar-fill" style="width:4%;background:#2d6e1f"></div></div></div></td>
          </tr>
          <tr>
            <td><div class="model-name">Mistral Small 3.1</div><div class="model-sub">Mistral AI</div></td>
            <td><span class="tag tag-green">EU natif</span></td>
            <td></td>
            <td class="r"><span class="price-sm">$0.20</span></td>
            <td class="r"><span class="price-sm">$0.60</span></td>
            <td class="r"><span class="cost-big">$1.8</span></td>
            <td><div class="halu-cell"><div class="halu-val" style="color:#8a5e10">4.1 % <span style="font-size:9px;color:var(--gray-400)">mars 2026</span></div><div class="halu-bar-bg"><div class="halu-bar-fill" style="width:20%;background:#8a5e10"></div></div></div></td>
          </tr>
          <tr>
            <td><div class="model-name">GPT-4.1</div><div class="model-sub">OpenAI</div></td>
            <td><span class="tag tag-amber">US</span></td>
            <td></td>
            <td class="r"><span class="price-sm">$2.00</span></td>
            <td class="r"><span class="price-sm">$8.00</span></td>
            <td class="r"><span class="cost-big">$22.5</span></td>
            <td><div class="halu-cell"><div class="halu-val" style="color:#2d6e1f">1.8 % <span style="font-size:9px;color:var(--gray-400)">mars 2026</span></div><div class="halu-bar-bg"><div class="halu-bar-fill" style="width:9%;background:#2d6e1f"></div></div></div></td>
          </tr>
          <tr>
            <td><div class="model-name">o3</div><div class="model-sub">OpenAI</div></td>
            <td><span class="tag tag-amber">US</span></td>
            <td></td>
            <td class="r"><span class="price-sm">$10.00</span></td>
            <td class="r"><span class="price-sm">$40.00</span></td>
            <td class="r"><span class="cost-big">$113</span></td>
            <td><div class="halu-cell"><div class="halu-val" style="color:#2d6e1f">0.8 % <span style="font-size:9px;color:var(--gray-400)">mars 2026</span></div><div class="halu-bar-bg"><div class="halu-bar-fill" style="width:4%;background:#2d6e1f"></div></div></div></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="apercu-note">
      Cet aperçu présente 4 modèles sur 26 disponibles. Le simulateur complet vous permet d'ajuster les volumes, la langue, et d'obtenir le comparatif intégral avec la recommandation "Meilleure proposition".
    </div>
  </div>

  <!-- PANEL 3 : TABLEAU -->
  <div class="gate-wrap">
  <div class="panel">
    <div class="panel-title">Comparatif — tous les modèles</div>
    <div class="sort-bar">
      <span>Trier par :</span>
      <button class="sort-btn active" onclick="setSort('cost',this)">Coût ↑</button>
      <button class="sort-btn" onclick="setSort('halu',this)">Hallucination ↑</button>
      <button class="sort-btn" onclick="setSort('name',this)">Nom A→Z</button>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Modèle</th>
            <th>Provider</th>
            <th>Région</th>
            <th class="r">Input /1M</th>
            <th class="r">Output /1M</th>
            <th class="r">Coût total mensuel</th>
            <th>Hallucination</th>
          </tr>
        </thead>
        <tbody id="result-tbody"></tbody>
      </table>
    </div>

    <div class="source-box">
      <b>Sources des taux d'hallucination :</b> Vectara HHEM Leaderboard v2 — benchmark sur résumé de documents (7 700 articles, tâche grounded summarization). Données publiées entre <b>novembre 2025 et mars 2026</b>. Les modèles non testés sont marqués N/D. Le taux varie fortement selon le benchmark (résumé ≠ QA factuelle ≠ juridique ≠ médical) — <a href="https://github.com/vectara/hallucination-leaderboard" target="_blank">voir le leaderboard complet</a>.
    </div>

    <div class="combo-btn-wrap">
      <button class="combo-btn" onclick="showBestCombo()">
        <svg width="15" height="15" viewBox="0 0 16 16" fill="none"><path d="M8 1l1.8 3.6L14 5.6l-3 2.9.7 4.1L8 10.5l-3.7 2.1.7-4.1-3-2.9 4.2-.6L8 1z" fill="currentColor"/></svg>
        Meilleure proposition
      </button>
    </div>

    <div class="combo-result" id="combo-result">
      <div class="combo-header">
        <div class="combo-header-title">⭐ Meilleur combo LLM — coût optimisé</div>
      </div>
      <div class="combo-body">
        <div class="combo-cards" id="combo-cards"></div>
        <div class="combo-total" id="combo-total"></div>
        <div class="combo-warning">
          <span style="font-size:16px;flex-shrink:0;">⚠</span>
          <span><b>Avertissement qualité :</b> Cette recommandation est basée uniquement sur le coût. Le modèle le moins cher n'est pas nécessairement le plus adapté à votre cas d'usage. Qualité d'analyse, taux d'hallucination et conformité réglementaire (RGPD, DORA, NIS2) doivent être évalués sur votre corpus réel. Un POC est indispensable avant tout déploiement en production.</span>
        </div>
      </div>
    </div>
  </div>
<?php if (!$hasAccess): ?>
  <div class="gate-overlay">
    <div class="gate-lock">🔒</div>
    <div class="gate-title">Accès gratuit sur demande</div>
    <div class="gate-sub">Comparez les 26 modèles LLM sur vos volumes réels — coûts mensuels exacts et taux d'hallucination. Accès immédiat et gratuit après votre demande.</div>
    <a class="gate-btn" href="index.php#contact" target="_blank" rel="noopener">Demander l'accès gratuit →</a>
  </div>
<?php endif; ?>
  </div>

  <div class="cta-bar">
    <div class="cta-text">
      <strong>Vous intégrez un LLM dans votre SI ?</strong>
      Galyn'Up vous accompagne dans la gouvernance, le cadrage et le déploiement de vos projets IA — de l'évaluation des coûts à la mise en production sécurisée.
    </div>
    <a class="cta-link" href="index.php#contact">Contactez Galyn'Up →</a>
  </div>
</main>

<footer>
  Outil réalisé par <a href="https://galynup.fr" target="_blank">Galyn'Up</a> — CIO Advisory &nbsp;·&nbsp;
  Tarifs : OpenAI, Anthropic, Google, Mistral AI, Meta, DeepSeek, Cohere (avril 2026) &nbsp;·&nbsp;
  Hallucination : <a href="https://github.com/vectara/hallucination-leaderboard" target="_blank">Vectara HHEM Leaderboard</a> (nov. 2025 – mars 2026)
  &nbsp;·&nbsp; © 2026 Galyn'Up — Tous droits réservés
</footer>

<script>
const MODELS = [
  { id:'gpt4o',        name:'GPT-4o',             provider:'OpenAI',        region:'US',        rCls:'tag-amber', inP:2.50,  outP:10.00, langFr:1.25, halu:1.5,  haluDate:'avr. 2025' },
  { id:'gpt4o-mini',   name:'GPT-4o mini',         provider:'OpenAI',        region:'US',        rCls:'tag-amber', inP:0.15,  outP:0.60,  langFr:1.25, halu:2.0,  haluDate:'avr. 2025' },
  { id:'gpt41',        name:'GPT-4.1',             provider:'OpenAI',        region:'US',        rCls:'tag-amber', inP:2.00,  outP:8.00,  langFr:1.25, halu:1.8,  haluDate:'mars 2026' },
  { id:'gpt41-mini',   name:'GPT-4.1 mini',        provider:'OpenAI',        region:'US',        rCls:'tag-amber', inP:0.40,  outP:1.60,  langFr:1.25, halu:null, haluDate:null },
  { id:'o3',           name:'o3',                  provider:'OpenAI',        region:'US',        rCls:'tag-amber', inP:10.00, outP:40.00, langFr:1.25, halu:0.8,  haluDate:'mars 2026' },
  { id:'o4-mini',      name:'o4-mini',             provider:'OpenAI',        region:'US',        rCls:'tag-amber', inP:1.10,  outP:4.40,  langFr:1.25, halu:null, haluDate:null },
  { id:'opus46',       name:'Claude Opus 4.6',      provider:'Anthropic',     region:'US',        rCls:'tag-amber', inP:15.00, outP:75.00, langFr:1.20, halu:null, haluDate:null },
  { id:'sonnet46',     name:'Claude Sonnet 4.6',    provider:'Anthropic',     region:'US',        rCls:'tag-amber', inP:3.00,  outP:15.00, langFr:1.20, halu:3.0,  haluDate:'mars 2026' },
  { id:'haiku45',      name:'Claude Haiku 4.5',     provider:'Anthropic',     region:'US',        rCls:'tag-amber', inP:0.80,  outP:4.00,  langFr:1.20, halu:null, haluDate:null },
  { id:'gemini25pro',  name:'Gemini 2.5 Pro',       provider:'Google',        region:'US',        rCls:'tag-amber', inP:1.25,  outP:10.00, langFr:1.20, halu:4.2,  haluDate:'mars 2026' },
  { id:'gemini25fl',   name:'Gemini 2.5 Flash',     provider:'Google',        region:'US',        rCls:'tag-amber', inP:0.15,  outP:0.60,  langFr:1.20, halu:3.3,  haluDate:'mars 2026' },
  { id:'gemini20fl',   name:'Gemini 2.0 Flash',     provider:'Google',        region:'US',        rCls:'tag-amber', inP:0.10,  outP:0.40,  langFr:1.20, halu:0.7,  haluDate:'avr. 2025' },
  { id:'mistral-l',    name:'Mistral Large 3',      provider:'Mistral AI',    region:'EU natif',  rCls:'tag-green', inP:2.00,  outP:6.00,  langFr:1.10, halu:3.6,  haluDate:'mars 2026' },
  { id:'mistral-m',    name:'Mistral Medium 3',     provider:'Mistral AI',    region:'EU natif',  rCls:'tag-green', inP:0.40,  outP:2.00,  langFr:1.10, halu:null, haluDate:null },
  { id:'mistral-s',    name:'Mistral Small 3.1',    provider:'Mistral AI',    region:'EU natif',  rCls:'tag-green', inP:0.20,  outP:0.60,  langFr:1.10, halu:4.1,  haluDate:'mars 2026' },
  { id:'mistral-n',    name:'Mistral Nemo',         provider:'Mistral AI',    region:'EU natif',  rCls:'tag-green', inP:0.15,  outP:0.15,  langFr:1.10, halu:5.5,  haluDate:'avr. 2025' },
  { id:'az-gpt4o',     name:'GPT-4o',              provider:'Azure OpenAI',  region:'EU dispo',  rCls:'tag-blue',  inP:2.50,  outP:10.00, langFr:1.25, halu:1.5,  haluDate:'avr. 2025' },
  { id:'az-gpt4o-m',   name:'GPT-4o mini',         provider:'Azure OpenAI',  region:'EU dispo',  rCls:'tag-blue',  inP:0.15,  outP:0.60,  langFr:1.25, halu:2.0,  haluDate:'avr. 2025' },
  { id:'az-mist-l',    name:'Mistral Large 3',      provider:'Azure Mistral', region:'EU natif',  rCls:'tag-green', inP:2.00,  outP:6.00,  langFr:1.10, halu:3.6,  haluDate:'mars 2026' },
  { id:'az-mist-s',    name:'Mistral Small 3.1',    provider:'Azure Mistral', region:'EU natif',  rCls:'tag-green', inP:0.20,  outP:0.60,  langFr:1.10, halu:4.1,  haluDate:'mars 2026' },
  { id:'cmd-rplus',    name:'Command R+',           provider:'Cohere',        region:'US / EU',   rCls:'tag-blue',  inP:2.50,  outP:10.00, langFr:1.22, halu:6.7,  haluDate:'avr. 2025' },
  { id:'cmd-r',        name:'Command R',            provider:'Cohere',        region:'US / EU',   rCls:'tag-blue',  inP:0.15,  outP:0.60,  langFr:1.22, halu:7.2,  haluDate:'avr. 2025' },
  { id:'llama70',      name:'Llama 3.3 70B',        provider:'Meta (via API)','region':'Variable', rCls:'tag-gray', inP:0.23,  outP:0.40,  langFr:1.28, halu:5.2,  haluDate:'mars 2026' },
  { id:'llama8',       name:'Llama 3.1 8B',         provider:'Meta (via API)','region':'Variable', rCls:'tag-gray', inP:0.06,  outP:0.06,  langFr:1.28, halu:12.4, haluDate:'avr. 2025' },
  { id:'ds-v3',        name:'DeepSeek V3',          provider:'DeepSeek',      region:'CN / US',   rCls:'tag-gray',  inP:0.27,  outP:1.10,  langFr:1.15, halu:3.9,  haluDate:'mars 2026' },
  { id:'ds-r1',        name:'DeepSeek R1',          provider:'DeepSeek',      region:'CN / US',   rCls:'tag-gray',  inP:0.55,  outP:2.19,  langFr:1.15, halu:11.0, haluDate:'mars 2026' },
];

const LANG_NOTES = {
  'fr-fr': 'Input et output en français — le français génère ~20–30 % de tokens supplémentaires vs l\'anglais. Multiplicateur appliqué sur les deux flux.',
  'fr-en': 'Input en français (surcoût modéré), output en anglais (optimal). Cas courant : corpus DSI français interrogé en anglais.',
  'en-fr': 'Input en anglais (optimal), output en français (surcoût modéré). Cas typique : assistant répondant en français à partir de docs anglais.',
  'en-en': 'Les deux flux en anglais — tokenisation la plus efficace, coût de base sans multiplicateur.',
};

let currentSort = 'cost';
let calcTimer = null;

function fmtM(n) {
  if (n >= 1e6) return (n/1e6).toFixed(1).replace('.',',') + ' M';
  if (n >= 1000) return (n/1000).toFixed(0) + ' K';
  return n.toString();
}
function fmtCost(n) {
  if (n < 1)   return '$' + n.toFixed(2);
  if (n < 100) return '$' + n.toFixed(1);
  return '$\u202f' + Math.round(n).toLocaleString('fr-FR');
}
function fmtP(p) { return '$' + p.toFixed(2); }

function haluColor(h) {
  if (h === null) return '#9e9d96';
  if (h <= 2)  return '#2d6e1f';
  if (h <= 5)  return '#8a5e10';
  if (h <= 10) return '#c2601a';
  return '#b83232';
}

function calcCostFull(m, tokIn, tokOut, langIn, langOut) {
  const mI = langIn  === 'fr' ? m.langFr : 1.0;
  const mO = langOut === 'fr' ? m.langFr : 1.0;
  return (tokIn * mI / 1e6 * m.inP) + (tokOut * mO / 1e6 * m.outP);
}
function calcCostIn(m, tokIn, langIn) {
  return tokIn * (langIn === 'fr' ? m.langFr : 1.0) / 1e6 * m.inP;
}
function calcCostOut(m, tokOut, langOut) {
  return tokOut * (langOut === 'fr' ? m.langFr : 1.0) / 1e6 * m.outP;
}

// Populate selects
function populateSelects() {
  const opts = MODELS.map(m => `<option value="${m.id}">${m.name} — ${m.provider}</option>`).join('');
  document.getElementById('model-in').innerHTML  = opts;
  document.getElementById('model-out').innerHTML = opts;
  // Defaults
  document.getElementById('model-in').value  = 'mistral-s';
  document.getElementById('model-out').value = 'mistral-l';
}

function setSort(key, btn) {
  currentSort = key;
  document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  update();
}

function update() {
  // Loader
  const indicator = document.getElementById('calc-indicator');
  const valIn  = document.getElementById('out-in');
  const valOut = document.getElementById('out-out');
  indicator.classList.add('visible');
  valIn.classList.add('updating');
  valOut.classList.add('updating');
  clearTimeout(calcTimer);
  calcTimer = setTimeout(() => {
    indicator.classList.remove('visible');
    valIn.classList.remove('updating');
    valOut.classList.remove('updating');
    doUpdate();
  }, 350);
}

function doUpdate() {
  const tokIn   = +document.getElementById('sl-in').value;
  const tokOut  = +document.getElementById('sl-out').value;
  const langIn  = document.getElementById('lang-in').value;
  const langOut = document.getElementById('lang-out').value;
  const midIn   = document.getElementById('model-in').value;
  const midOut  = document.getElementById('model-out').value;

  document.getElementById('out-in').textContent  = fmtM(tokIn);
  document.getElementById('out-out').textContent = fmtM(tokOut);
  document.getElementById('lang-note').textContent = LANG_NOTES[langIn + '-' + langOut];

  const mIn  = MODELS.find(m => m.id === midIn);
  const mOut = MODELS.find(m => m.id === midOut);

  // Info sous les selects
  const infoIn = document.getElementById('info-in');
  const infoOut = document.getElementById('info-out');
  infoIn.innerHTML  = `<span><b>Input :</b> ${fmtP(mIn.inP)} /1M tok.</span><span><b>Hallucination :</b> ${mIn.halu !== null ? mIn.halu + ' %' : 'N/D'}</span><span><span class="tag ${mIn.rCls}" style="font-size:10px;">${mIn.region}</span></span>`;
  infoOut.innerHTML = `<span><b>Output :</b> ${fmtP(mOut.outP)} /1M tok.</span><span><b>Hallucination :</b> ${mOut.halu !== null ? mOut.halu + ' %' : 'N/D'}</span><span><span class="tag ${mOut.rCls}" style="font-size:10px;">${mOut.region}</span></span>`;

  const costIn   = calcCostIn(mIn, tokIn, langIn);
  const costOut  = calcCostOut(mOut, tokOut, langOut);
  const costTotal = costIn + costOut;

  // Cost summary
  const same = midIn === midOut;
  document.getElementById('cost-summary').innerHTML = `
    <div class="cost-card cost-card-in">
      <div class="cost-card-label">Coût input</div>
      <div class="cost-card-model">${mIn.name}</div>
      <div class="cost-card-amount">${fmtCost(costIn)}<span style="font-size:12px;font-weight:400;opacity:0.6"> /mois</span></div>
    </div>
    <div class="cost-card cost-card-out">
      <div class="cost-card-label">Coût output</div>
      <div class="cost-card-model">${mOut.name}</div>
      <div class="cost-card-amount">${fmtCost(costOut)}<span style="font-size:12px;font-weight:400;opacity:0.6"> /mois</span></div>
    </div>
    <div class="cost-card cost-card-total">
      <div class="cost-card-label">Total mensuel</div>
      <div class="cost-card-model">${same ? mIn.name : mIn.name + ' + ' + mOut.name}</div>
      <div class="cost-card-amount">${fmtCost(costTotal)}<span style="font-size:12px;font-weight:400;opacity:0.6"> /mois</span></div>
    </div>`;

  // Table
  const data = MODELS.map(m => ({ ...m, cost: calcCostFull(m, tokIn, tokOut, langIn, langOut) }));
  if (currentSort === 'cost') data.sort((a,b) => a.cost - b.cost);
  else if (currentSort === 'halu') data.sort((a,b) => {
    if (a.halu === null && b.halu === null) return 0;
    if (a.halu === null) return 1;
    if (b.halu === null) return -1;
    return a.halu - b.halu;
  });
  else data.sort((a,b) => (a.name + a.provider).localeCompare(b.name + b.provider));

  document.getElementById('result-tbody').innerHTML = data.map(m => {
    const isIn   = m.id === midIn;
    const isOut  = m.id === midOut;
    const rowCls = (isIn && isOut) ? 'sel-both' : isIn ? 'sel-in' : isOut ? 'sel-out' : '';
    const pills  = (isIn  ? '<span class="sel-pill sel-pill-in">INPUT</span>'  : '') +
                   (isOut ? '<span class="sel-pill sel-pill-out">OUTPUT</span>' : '');
    const haluHtml = m.halu !== null
      ? `<div class="halu-cell">
           <div class="halu-val" style="color:${haluColor(m.halu)}">${m.halu.toFixed(1)} % <span style="font-size:9px;color:var(--gray-400)">${m.haluDate}</span></div>
           <div class="halu-bar-bg"><div class="halu-bar-fill" style="width:${Math.min(m.halu/20*100,100)}%;background:${haluColor(m.halu)}"></div></div>
         </div>`
      : `<span class="halu-nd">N/D</span>`;

    return `<tr class="${rowCls}">
      <td><div class="model-name">${m.name}${pills}</div><div class="model-sub">${m.provider}</div></td>
      <td><span class="tag ${m.rCls}">${m.region}</span></td>
      <td></td>
      <td class="r"><span class="price-sm">${fmtP(m.inP)}</span></td>
      <td class="r"><span class="price-sm">${fmtP(m.outP)}</span></td>
      <td class="r"><span class="cost-big">${fmtCost(m.cost)}</span></td>
      <td>${haluHtml}</td>
    </tr>`;
  }).join('');

  document.getElementById('combo-result').style.display = 'none';
}

function showBestCombo() {
  const tokIn   = +document.getElementById('sl-in').value;
  const tokOut  = +document.getElementById('sl-out').value;
  const langIn  = document.getElementById('lang-in').value;
  const langOut = document.getElementById('lang-out').value;

  const bestIn  = [...MODELS].sort((a,b) => calcCostIn(a,tokIn,langIn)   - calcCostIn(b,tokIn,langIn))[0];
  const bestOut = [...MODELS].sort((a,b) => calcCostOut(a,tokOut,langOut) - calcCostOut(b,tokOut,langOut))[0];
  const cIn  = calcCostIn(bestIn, tokIn, langIn);
  const cOut = calcCostOut(bestOut, tokOut, langOut);
  const bySingle = [...MODELS].sort((a,b) => calcCostFull(a,tokIn,tokOut,langIn,langOut) - calcCostFull(b,tokIn,tokOut,langIn,langOut))[0];
  const same = bestIn.id === bestOut.id;

  document.getElementById('combo-cards').innerHTML = `
    <div class="combo-card">
      <div class="combo-card-label">Meilleur modèle pour l'input</div>
      <div class="combo-card-model">${bestIn.name}</div>
      <div class="combo-card-provider">${bestIn.provider} · <span class="tag ${bestIn.rCls}" style="font-size:10px;">${bestIn.region}</span></div>
      <div class="combo-card-cost">${fmtCost(cIn)}<span style="font-size:11px;font-weight:400;color:var(--gray-400)"> /mois</span></div>
      <div style="font-size:11px;color:var(--gray-400);margin-top:4px;">${fmtP(bestIn.inP)} /1M tok. · Hallucination : ${bestIn.halu !== null ? bestIn.halu+'%' : 'N/D'}</div>
    </div>
    <div class="combo-card">
      <div class="combo-card-label">Meilleur modèle pour l'output</div>
      <div class="combo-card-model">${bestOut.name}</div>
      <div class="combo-card-provider">${bestOut.provider} · <span class="tag ${bestOut.rCls}" style="font-size:10px;">${bestOut.region}</span></div>
      <div class="combo-card-cost">${fmtCost(cOut)}<span style="font-size:11px;font-weight:400;color:var(--gray-400)"> /mois</span></div>
      <div style="font-size:11px;color:var(--gray-400);margin-top:4px;">${fmtP(bestOut.outP)} /1M tok. · Hallucination : ${bestOut.halu !== null ? bestOut.halu+'%' : 'N/D'}</div>
    </div>`;

  document.getElementById('combo-total').innerHTML = same
    ? `Coût mensuel total : <strong>${fmtCost(cIn+cOut)}</strong> avec <em>${bestIn.name}</em> (${bestIn.provider}) pour les deux flux.`
    : `Coût mensuel total (combo) : <strong>${fmtCost(cIn+cOut)}</strong> &nbsp;·&nbsp; Meilleur modèle unique : <em>${bySingle.name}</em> à <strong>${fmtCost(calcCostFull(bySingle,tokIn,tokOut,langIn,langOut))}</strong>`;

  const el = document.getElementById('combo-result');
  el.style.display = 'block';
  setTimeout(() => el.scrollIntoView({ behavior:'smooth', block:'nearest' }), 50);
}

populateSelects();
update();
</script>
<!-- © 2026 Galyn'Up — Gaëlle FAY — galynup.fr | Outil protégé par le droit d'auteur (CPI art. L111-1). Reproduction interdite sans autorisation. -->
</body>
</html>
