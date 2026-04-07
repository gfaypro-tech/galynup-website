<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Diagnostic Maturité Infrastructure SI — Galyn'Up</title>
  <meta name="description" content="Évaluez la maturité de votre infrastructure SI sur 7 domaines. Outil propriétaire Galyn'Up — CIO Advisory.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
:root {
  --di-aubergine: #6D155D;
  --di-gold: #D3A625;
  --di-gold-light: #F0C040;
  --di-dark: #1a1018;
  --di-mid: #4a3a47;
  --di-muted: #8a7085;
  --di-border: #e8dae5;
  --di-bg: #faf8fa;
  --di-white: #ffffff;
  --di-pale: #f0ebf0;
  --di-red: #c0392b;
  --di-orange: #d35400;
  --di-lime: #6aaa00;
  --di-green: #1a7a4a;
}

#diag-infra-wrap * { box-sizing: border-box; }
#diag-infra-wrap { font-family: 'DM Sans', sans-serif; color: var(--di-dark); font-size: 14px; line-height: 1.6; padding-top: 100px; }

.di-progress-bar-wrap { background: var(--di-pale); border-bottom: 1px solid var(--di-border); padding: 14px 40px; display: flex; align-items: center; gap: 16px; margin-bottom: 32px; }
.di-progress-label { font-size: 12px; color: var(--di-muted); white-space: nowrap; }
.di-progress-track { flex:1; height:6px; background:var(--di-border); border-radius:3px; overflow:hidden; }
.di-progress-fill { height:100%; background: linear-gradient(90deg, var(--di-dark), var(--di-gold)); border-radius:3px; transition: width 0.4s ease; width:0%; }
.di-progress-pct { font-size:12px; font-weight:500; color:var(--di-dark); min-width:36px; text-align:right; }

.di-main { max-width: 860px; margin: 0 auto; padding: 0 24px 80px; }

.di-domain-block { margin-bottom: 40px; }
.di-domain-header { display:flex; align-items:center; gap:14px; padding:16px 20px; background:var(--di-white); border:1px solid var(--di-border); border-left:4px solid var(--di-dark); border-radius:8px; margin-bottom:16px; }
.di-domain-icon { width:38px; height:38px; background:var(--di-pale); border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.di-domain-title { font-family:'Cormorant Garamond', serif; font-size:20px; font-weight:600; color:var(--di-dark); }
.di-domain-desc { font-size:12px; color:var(--di-muted); margin-top:1px; }

.di-question-card { background:var(--di-white); border:1px solid var(--di-border); border-radius:8px; padding:18px 20px; margin-bottom:10px; transition:border-color 0.2s; }
.di-question-card.answered { border-color: var(--di-dark); }
.di-question-text { font-size:13.5px; color:var(--di-dark); margin-bottom:14px; font-weight:400; }
.di-question-num { font-size:11px; color:var(--di-muted); margin-bottom:4px; }

.di-options { display:flex; flex-direction:column; gap:6px; }
.di-option-btn { display:flex; align-items:center; gap:10px; padding:9px 13px; border:1.5px solid var(--di-border); border-radius:6px; background:var(--di-bg); cursor:pointer; font-size:13px; color:var(--di-mid); text-align:left; transition:all 0.15s; font-family:'DM Sans', sans-serif; width:100%; }
.di-option-btn:hover { border-color:var(--di-dark); background:var(--di-pale); }
.di-option-btn.selected { border-color:var(--di-dark); background:var(--di-pale); color:var(--di-dark); font-weight:500; }
.di-option-score { min-width:20px; height:20px; border-radius:50%; font-size:10px; font-weight:500; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.di-score-0 { background:#fde8e6; color:var(--di-red); }
.di-score-1 { background:#fde8e6; color:var(--di-red); }
.di-score-2 { background:#fdf0e0; color:var(--di-orange); }
.di-score-3 { background:#edfff0; color:var(--di-lime); }

.di-nav-bar { position:sticky; bottom:0; background:rgba(250,248,250,0.95); backdrop-filter:blur(8px); border-top:1px solid var(--di-border); padding:14px 24px; display:flex; justify-content:space-between; align-items:center; }
.di-nav-inner { max-width:860px; margin:0 auto; width:100%; display:flex; justify-content:space-between; align-items:center; }
.di-btn { padding:10px 24px; border-radius:6px; font-family:'DM Sans', sans-serif; font-size:13px; font-weight:500; cursor:pointer; transition:all 0.2s; border:none; }
.di-btn-primary { background:var(--di-dark); color:white; }
.di-btn-primary:hover { background:#2d1f2a; }
.di-btn-primary:disabled { opacity:0.4; cursor:not-allowed; }
.di-btn-secondary { background:transparent; color:var(--di-dark); border:1.5px solid var(--di-dark); }
.di-btn-secondary:hover { background:var(--di-pale); }
.di-btn-gold { background:var(--di-gold); color:white; }
.di-btn-gold:hover { background:var(--di-gold-light); }
.di-section-counter { font-size:12px; color:var(--di-muted); }

.di-intro-card { background:var(--di-white); border:1px solid var(--di-border); border-radius:10px; padding:36px 40px; margin-bottom:32px; }
.di-intro-card h2 { font-family:'Cormorant Garamond', serif; font-size:26px; font-weight:500; color:var(--di-dark); margin-bottom:12px; }
.di-intro-card p { color:var(--di-mid); margin-bottom:10px; }
.di-intro-meta { display:flex; gap:20px; flex-wrap:wrap; margin-top:20px; padding-top:20px; border-top:1px solid var(--di-border); }
.di-meta-item { display:flex; flex-direction:column; gap:2px; }
.di-meta-label { font-size:11px; color:var(--di-muted); text-transform:uppercase; letter-spacing:0.06em; }
.di-meta-value { font-size:13px; font-weight:500; color:var(--di-dark); }
.di-client-form { margin-top:20px; display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.di-form-group { display:flex; flex-direction:column; gap:4px; }
.di-form-group label { font-size:12px; color:var(--di-muted); }
.di-form-group input, .di-form-group select { padding:9px 12px; border:1.5px solid var(--di-border); border-radius:6px; font-family:'DM Sans', sans-serif; font-size:13px; background:var(--di-bg); color:var(--di-dark); outline:none; transition:border-color 0.2s; }
.di-form-group input:focus, .di-form-group select:focus { border-color:var(--di-dark); }

#di-results-section { display:none; }
.di-result-header { background:var(--di-dark); color:white; border-radius:10px; padding:28px 32px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; }
.di-result-score-big { font-family:'Cormorant Garamond', serif; font-size:64px; font-weight:600; line-height:1; color:var(--di-gold-light); }
.di-result-level { font-size:22px; font-weight:500; margin-top:4px; }
.di-result-client { font-size:13px; opacity:0.7; margin-top:6px; }
.di-radar-wrap { background:var(--di-white); border:1px solid var(--di-border); border-radius:10px; padding:24px; margin-bottom:20px; }
.di-radar-wrap h3 { font-family:'Cormorant Garamond', serif; font-size:18px; color:var(--di-dark); margin-bottom:16px; }
.di-domain-scores { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:20px; }
.di-domain-score-card { background:var(--di-white); border:1px solid var(--di-border); border-radius:8px; padding:14px 16px; }
.di-ds-name { font-size:13px; font-weight:500; color:var(--di-dark); margin-bottom:8px; }
.di-ds-bar-track { height:8px; background:var(--di-bg); border-radius:4px; overflow:hidden; margin-bottom:6px; }
.di-ds-bar-fill { height:100%; border-radius:4px; }
.di-ds-pct { font-size:12px; font-weight:500; }

/* Sections flouées */
.di-reco-section { background:var(--di-white); border:1px solid var(--di-border); border-radius:10px; padding:24px; }
.di-reco-section h3 { font-family:'Cormorant Garamond', serif; font-size:18px; color:var(--di-dark); margin-bottom:16px; }
.di-reco-item { display:flex; gap:12px; padding:12px; border-radius:7px; margin-bottom:8px; border-left:3px solid; }
.di-reco-critical { background:#fdf0ef; border-color:var(--di-red); }
.di-reco-important { background:#fdf6ed; border-color:var(--di-orange); }
.di-reco-prio { font-size:10px; font-weight:500; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:2px; }
.di-reco-text { font-size:13px; color:var(--di-mid); }
.di-timeline-wrap { background:var(--di-white); border:1px solid var(--di-border); border-radius:10px; padding:24px; margin-top:16px; }
.di-timeline-wrap h3 { font-family:'Cormorant Garamond', serif; font-size:18px; color:var(--di-dark); margin-bottom:16px; }
.di-timeline { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.di-tl-phase { border:1px solid var(--di-border); border-radius:8px; padding:14px 16px; }
.di-tl-phase h4 { font-size:13px; font-weight:500; margin-bottom:8px; padding-bottom:6px; border-bottom:1px solid var(--di-border); }
.di-tl-item { font-size:12px; color:var(--di-mid); padding:3px 0; display:flex; gap:6px; }
.di-tl-dot { width:6px; height:6px; border-radius:50%; background:var(--di-gold); margin-top:5px; flex-shrink:0; }

/* ── Gate / Blur ─────────────────────────────────────────────────────────── */
.di-gate-wrapper { position: relative; margin-bottom: 20px; }
.di-blurred { filter: blur(5px); pointer-events: none; user-select: none; }
.di-gate-overlay {
  position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  background: rgba(250,248,250,0.88);
  border-radius: 10px; z-index: 10; padding: 32px;
  transition: opacity 0.6s ease;
  overflow-y: auto;
}
.di-gate-inner { max-width: 480px; width: 100%; text-align: center; }
.di-gate-inner h3 { font-family: 'Cormorant Garamond', serif; font-size: 22px; font-weight: 500; color: var(--di-dark); margin-bottom: 10px; }
.di-gate-inner p { font-size: 13px; color: var(--di-mid); }
.di-gate-form { text-align: left; margin-top: 18px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.di-gate-form .di-full { grid-column: 1 / -1; }
.di-gate-form input, .di-gate-form textarea { width: 100%; padding: 9px 12px; border: 1.5px solid var(--di-border); border-radius: 6px; font-family: 'DM Sans', sans-serif; font-size: 13px; background: var(--di-white); color: var(--di-dark); outline: none; transition: border-color 0.2s; }
.di-gate-form input:focus, .di-gate-form textarea:focus { border-color: var(--di-dark); }
.di-gate-form textarea { resize: vertical; min-height: 60px; }
.di-gate-label { font-size: 12px; color: var(--di-muted); display: block; margin-bottom: 4px; }
.di-gate-msg { margin-top: 10px; font-size: 13px; display: none; }
.di-gate-msg.success { color: var(--di-green); }
.di-gate-msg.error { color: var(--di-red); }
.di-gate-confirm-icon { font-size: 40px; margin-bottom: 12px; }

.di-export-bar { display:flex; gap:10px; margin-top:20px; flex-wrap:wrap; }
.di-hidden { display:none !important; }
.di-section-page { display:none; }
.di-section-page.active { display:block; }
canvas { max-width:100%; }
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
              <a href="simulateur-llm.php" class="nav-dropdown-item">🧮 Simulateur LLM</a>
              <a href="audit-dsi.php" class="nav-dropdown-item">◈ Audit Maturité DSI</a>
              <a href="diag-cyber.php" class="nav-dropdown-item">🔒 Diag Cybersécurité</a>
              <a href="diag-infra.php" class="nav-dropdown-item active">🖥️ Diag Infrastructure</a>
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

  <div id="diag-infra-wrap">

    <div class="di-progress-bar-wrap">
      <span class="di-progress-label">Progression</span>
      <div class="di-progress-track"><div class="di-progress-fill" id="diProgressFill"></div></div>
      <span class="di-progress-pct" id="diProgressPct">0%</span>
    </div>

    <div class="di-main">

      <!-- INTRO -->
      <div id="di-intro-section">
        <div class="di-intro-card">
          <h2>Évaluation de la maturité infrastructure SI</h2>
          <p>Ce diagnostic couvre <strong>7 domaines</strong> de l'infrastructure des systèmes d'information, soit <strong>35 questions</strong> structurées selon une échelle de maturité en 4 niveaux. Conçu pour un entretien de 60 à 90 minutes avec le DSI ou le Responsable Infrastructure.</p>
          <p>Le résultat produit un score global, un plan de recommandations priorisé et une feuille de route sur 12 mois.</p>
          <div class="di-intro-meta">
            <div class="di-meta-item"><span class="di-meta-label">Durée estimée</span><span class="di-meta-value">60 – 90 min</span></div>
            <div class="di-meta-item"><span class="di-meta-label">Domaines</span><span class="di-meta-value">7</span></div>
            <div class="di-meta-item"><span class="di-meta-label">Questions</span><span class="di-meta-value">35</span></div>
            <div class="di-meta-item"><span class="di-meta-label">Référentiels</span><span class="di-meta-value">ITIL 4 · TOGAF · ISO 20000</span></div>
          </div>
          <div class="di-client-form">
            <div class="di-form-group"><label>Nom du client</label><input type="text" id="diClientName" placeholder="Ex : Assurance Métropole"></div>
            <div class="di-form-group"><label>Secteur</label>
              <select id="diClientSector">
                <option value="">-- Sélectionner --</option>
                <option>Banque / Finance</option><option>Assurance</option>
                <option>Secteur public</option><option>Industrie</option>
                <option>Santé</option><option>Autre</option>
              </select>
            </div>
            <div class="di-form-group"><label>Interlocuteur</label><input type="text" id="diClientContact" placeholder="Ex : Sophie Durand, DSI"></div>
            <div class="di-form-group"><label>Date du diagnostic</label><input type="date" id="diDiagDate"></div>
            <div class="di-form-group"><label>Taille SI (nb serveurs physiques)</label>
              <select id="diSiSize">
                <option value="PME">PME (&lt; 50 serveurs)</option>
                <option value="ETI">ETI (50 – 300 serveurs)</option>
                <option value="GE">Grande entreprise (&gt; 300 serveurs)</option>
              </select>
            </div>
            <div class="di-form-group"><label>Maturité cloud actuelle</label>
              <select id="diCloudMat">
                <option value="on-prem">100% On-premise</option>
                <option value="hybrid">Hybride (cloud partiel)</option>
                <option value="cloud-first">Cloud-first</option>
              </select>
            </div>
          </div>
        </div>
        <div style="text-align:center; margin-top:8px;">
          <button class="di-btn di-btn-primary" onclick="diStartDiag()" style="padding:12px 36px; font-size:15px;">Démarrer le diagnostic →</button>
        </div>
      </div>

      <!-- QUESTIONS -->
      <div id="di-questions-section" class="di-hidden">
        <div id="di-domain-pages"></div>
      </div>

      <!-- RESULTS -->
      <div id="di-results-section">

        <div class="di-result-header">
          <div>
            <div style="font-size:13px; opacity:0.7; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.06em;">Rapport de diagnostic</div>
            <div class="di-result-level" id="di-result-level-text">—</div>
            <div class="di-result-client" id="di-result-client-text">—</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:12px; opacity:0.6; margin-bottom:4px;">Score global</div>
            <div class="di-result-score-big" id="di-result-score-big">—</div>
            <div style="font-size:13px; opacity:0.7;">/ 100</div>
          </div>
        </div>

        <div class="di-radar-wrap">
          <h3>Cartographie par domaine</h3>
          <canvas id="diRadarChart" height="320"></canvas>
        </div>

        <div class="di-domain-scores" id="di-domain-scores-grid"></div>

        <!-- ── Recommandations + Feuille de route — GATED ─────────────────── -->
        <div class="di-gate-wrapper" id="diRecoGate">

          <!-- Contenu flouté -->
          <div class="di-blurred" id="diRecoContent">
            <div class="di-reco-section">
              <h3>Plan de recommandations</h3>
              <div id="di-reco-list"></div>
            </div>
            <div class="di-timeline-wrap">
              <h3>Feuille de route suggérée — 12 mois</h3>
              <div class="di-timeline" id="di-roadmap-grid"></div>
            </div>
          </div>

          <!-- Overlay gate -->
          <div class="di-gate-overlay" id="diGateOverlay">

            <!-- Étape 1 : CTA -->
            <div class="di-gate-inner" id="diGateCta">
              <div style="font-size:36px; margin-bottom:14px;">🔒</div>
              <h3>Analyse approfondie</h3>
              <p>Votre plan de recommandations et votre feuille de route personnalisée vous seront envoyés avec un rapport PDF et un export CSV complet du diagnostic.</p>
              <button class="di-btn di-btn-primary" onclick="diRevealForm()" style="margin-top:20px; padding:12px 28px;">
                Demander l'analyse approfondie →
              </button>
            </div>

            <!-- Étape 2 : Formulaire -->
            <div class="di-gate-inner" id="diGateForm" style="display:none;">
              <h3>Analyse approfondie</h3>
              <p>Laissez vos coordonnées — vous recevrez le rapport complet sous 48h.</p>
              <div class="di-gate-form">
                <div>
                  <label class="di-gate-label">Nom *</label>
                  <input type="text" id="diContactName" placeholder="Votre nom">
                </div>
                <div>
                  <label class="di-gate-label">Email *</label>
                  <input type="email" id="diContactEmail" placeholder="votre@email.com">
                </div>
                <div class="di-full">
                  <label class="di-gate-label">Message (optionnel)</label>
                  <textarea id="diContactMessage" placeholder="Contexte, questions, disponibilités…"></textarea>
                </div>
              </div>
              <div style="margin-top:16px; text-align:center;">
                <button class="di-btn di-btn-primary" id="diSendBtn" onclick="diSendContact()">Envoyer →</button>
              </div>
              <div class="di-gate-msg" id="diGateMsg"></div>
            </div>

            <!-- Étape 3 : Confirmation -->
            <div class="di-gate-inner" id="diGateConfirm" style="display:none;">
              <div class="di-gate-confirm-icon">✓</div>
              <h3>Demande envoyée</h3>
              <p>Merci ! Nous vous contacterons sous 48h avec votre analyse complète, les fichiers PDF et CSV du diagnostic.</p>
            </div>

          </div><!-- /#diGateOverlay -->
        </div><!-- /.di-gate-wrapper -->

        <!-- Bouton nouveau diagnostic uniquement -->
        <div class="di-export-bar">
          <button class="di-btn di-btn-secondary" onclick="diResetDiag()">Nouveau diagnostic</button>
        </div>

      </div><!-- /#di-results-section -->

    </div><!-- /.di-main -->
  </div><!-- /#diag-infra-wrap -->

  <div class="di-nav-bar" id="di-nav-bar" style="display:none;">
    <div class="di-nav-inner">
      <button class="di-btn di-btn-secondary" id="di-btn-prev" onclick="diPrevDomain()">← Précédent</button>
      <span class="di-section-counter" id="di-section-counter"></span>
      <button class="di-btn di-btn-primary" id="di-btn-next" onclick="diNextDomain()">Suivant →</button>
    </div>
  </div>

  <footer style="text-align:center;padding:22px;font-size:11px;color:#9e9d96;border-top:1px solid #dddcd7;">
    Outil réalisé par <a href="https://galynup.fr" target="_blank" style="color:#6D155D;text-decoration:none;">Galyn'Up</a> — CIO Advisory &nbsp;·&nbsp;
    © 2026 Galyn'Up — Tous droits réservés
  </footer>

  <script src="js/script.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script>
const DI_DOMAINS = [
  {
    id: 'arch', icon: '🏗️', title: 'Architecture & Urbanisation',
    desc: "Schéma directeur SI, urbanisation applicative, cartographie",
    questions: [
      { text: "L'organisation dispose-t-elle d'un schéma directeur SI formalisé et aligné sur la stratégie métier ?", opts: ["Aucun schéma directeur","Document informel et non maintenu","Schéma directeur approuvé et à jour","Schéma directeur piloté avec roadmap pluriannuelle"] },
      { text: "La cartographie applicative (applications, flux, dépendances) est-elle maintenue à jour ?", opts: ["Aucune cartographie","Cartographie partielle et obsolète","Cartographie documentée, revue annuellement","Cartographie temps réel dans un outil CMDB/EA"] },
      { text: "Une architecture de référence (patterns, standards technologiques) est-elle définie et respectée ?", opts: ["Aucun standard défini","Standards informels, non respectés","Référentiel documenté avec comité d'architecture","Gouvernance EA avec revue des dérogations"] },
      { text: "La dette technique est-elle identifiée, mesurée et gérée activement ?", opts: ["Dette inconnue / ignorée","Connue informellement, non documentée","Cartographiée avec plan de résorption","Gérée en continu, intégrée au backlog produit"] },
      { text: "Les interfaces et les flux entre applications sont-ils documentés et standardisés ?", opts: ["Aucune documentation des flux","Flux documentés partiellement","Cartographie des flux avec ESB/API Gateway","Catalogue d'API géré, flux monitorés temps réel"] }
    ]
  },
  {
    id: 'infra', icon: '🖥️', title: 'Infrastructure & Datacenter',
    desc: "Serveurs, virtualisation, datacenter, capacité",
    questions: [
      { text: "Le niveau de virtualisation des serveurs est-il optimisé ?", opts: ["Infra physique majoritaire (< 30% virtualisé)","Virtualisation partielle (30-60%)","Haute virtualisation (> 60%), VMware ou Hyper-V","Full virtualisation ou conteneurisation (K8s)"] },
      { text: "Le datacenter (propre ou externe) respecte-t-il des standards de disponibilité (Tier / SLA) ?", opts: ["Aucun SLA défini, datacenter non certifié","SLA informel, datacenter sous-dimensionné","SLA contractualisé, datacenter Tier 2/3","Tier 3+ avec PUE optimisé et redondance totale"] },
      { text: "Une gestion des capacités (capacity management) est-elle en place ?", opts: ["Aucune gestion des capacités","Réactivité aux pannes uniquement","Planification annuelle des capacités","Gestion prédictive avec alertes automatiques"] },
      { text: "Le cycle de vie des équipements (matériels obsolètes, EOL) est-il maîtrisé ?", opts: ["Matériels EOL en production sans inventaire","Inventaire partiel, remplacements ad hoc","Inventaire complet avec plan de renouvellement","Gestion du cycle de vie intégrée à la CMDB"] },
      { text: "Des mécanismes de haute disponibilité (HA) sont-ils déployés sur les systèmes critiques ?", opts: ["Aucune redondance sur les systèmes critiques","Redondance partielle, non testée","HA sur les systèmes critiques, testée régulièrement","Architecture active-active, zero downtime déployé"] }
    ]
  },
  {
    id: 'reseau', icon: '🌐', title: 'Réseau & Connectivité',
    desc: "LAN, WAN, SD-WAN, connectivité cloud, performance",
    questions: [
      { text: "L'architecture réseau (LAN, WAN, DMZ) est-elle documentée et segmentée ?", opts: ["Réseau plat, non documenté","Documentation partielle, segmentation limitée","Architecture documentée avec VLAN et DMZ","Segmentation avancée, Zero Trust Network Access"] },
      { text: "Les liens WAN inter-sites sont-ils dimensionnés et redondés ?", opts: ["Liens uniques non redondés","Redondance partielle sur les sites critiques","Liens redondés avec bascule automatique","SD-WAN avec load balancing et QoS dynamique"] },
      { text: "La performance réseau est-elle mesurée et des SLA sont-ils en place ?", opts: ["Aucune mesure de performance","Mesures ponctuelles en cas de problème","Monitoring continu avec seuils d'alerte","SLA réseau contractualisés avec tableau de bord"] },
      { text: "La connectivité vers le cloud (internet, ExpressRoute, Direct Connect) est-elle sécurisée et optimisée ?", opts: ["Connectivité cloud via internet public uniquement","VPN site-to-cloud basique","Liens dédiés (ExpressRoute / Direct Connect)","Architecture multi-cloud avec peering optimisé"] },
      { text: "La gestion des incidents réseau est-elle automatisée (détection, alerte, remédiation) ?", opts: ["Aucune supervision réseau","Supervision basique avec alertes email","NOC avec runbooks de remédiation","AIOps pour détection et remédiation automatique"] }
    ]
  },
  {
    id: 'cloud', icon: '☁️', title: 'Cloud & Modernisation',
    desc: "Stratégie cloud, migration, FinOps, containers",
    questions: [
      { text: "Une stratégie cloud formalisée (cloud-first, hybride, multi-cloud) est-elle définie ?", opts: ["Aucune stratégie cloud définie","Réflexion en cours, non formalisée","Stratégie documentée et approuvée","Stratégie avec gouvernance cloud et Cloud CoE"] },
      { text: "Les charges de travail migrables vers le cloud sont-elles identifiées et priorisées ?", opts: ["Aucune analyse réalisée","Identification informelle","Analyse 6R réalisée avec plan de migration","Programme de migration avec vague planning"] },
      { text: "Les coûts cloud sont-ils maîtrisés (FinOps) ?", opts: ["Aucune visibilité sur les coûts cloud","Factures cloud suivies sans optimisation","Tagging et allocation de coûts en place","FinOps pratiqué : rightsizing, réservations, alertes"] },
      { text: "Les applications critiques s'appuient-elles sur des architectures cloud-native (containers, serverless) ?", opts: ["Aucune architecture cloud-native","Quelques conteneurs en développement","Docker/K8s en production sur apps non critiques","Cloud-native généralisé avec CI/CD et service mesh"] },
      { text: "La gouvernance du cloud (sécurité, conformité, IAM cloud) est-elle en place ?", opts: ["Aucune gouvernance cloud","Accès cloud non gouvernés","Policies et guardrails définis","Cloud Security Posture Management (CSPM) déployé"] }
    ]
  },
  {
    id: 'donnees', icon: '💾', title: 'Stockage, Données & Sauvegarde',
    desc: "SAN/NAS, bases de données, backup, archivage, PRA",
    questions: [
      { text: "La stratégie de stockage (SAN, NAS, objet, cloud) est-elle adaptée aux besoins et optimisée ?", opts: ["Stockage non structuré et hétérogène","Stockage managé sans optimisation","Architecture de stockage définie par tier","Stockage software-defined avec tiering automatique"] },
      { text: "Les bases de données critiques sont-elles protégées (HA, réplication, backup) ?", opts: ["Bases sans protection ni backup validé","Backup quotidien non testé","HA + backup testé trimestriellement","Réplication synchrone + backup immuable testé"] },
      { text: "La stratégie de sauvegarde (règle 3-2-1, RPO, RTO) est-elle formalisée et testée ?", opts: ["Pas de stratégie de sauvegarde formalisée","Backup existant, jamais testé","Stratégie documentée, tests annuels","Tests de restauration trimestriels, RPO/RTO validés"] },
      { text: "Un Plan de Reprise d'Activité (PRA) infra est-il en place avec un site de secours ?", opts: ["Aucun PRA infra","PRA documenté sans infrastructure de secours","Site de secours avec bascule testée annuellement","PRA testé semestriellement avec RTO < 4h"] },
      { text: "L'archivage légal et réglementaire des données est-il conforme (durées de conservation, RGPD) ?", opts: ["Aucune politique d'archivage","Archivage informel sans durées définies","Politique d'archivage documentée","Archivage automatisé conforme RGPD avec audit trail"] }
    ]
  },
  {
    id: 'ops', icon: '⚙️', title: 'Exploitation & ITSM',
    desc: "Supervision, ITSM, gestion des incidents, changements, CMDB",
    questions: [
      { text: "Un outil ITSM (ServiceNow, Jira Service, GLPI…) est-il en place pour gérer les incidents et demandes ?", opts: ["Aucun outil ITSM, gestion par email","Outil basique sans processus formalisés","ITSM déployé avec processus Incident/Change","ITSM intégré avec CMDB, catalogue de services"] },
      { text: "La supervision applicative et infra est-elle centralisée et proactive ?", opts: ["Aucune supervision centralisée","Supervision partielle, réactive","Supervision centralisée avec seuils d'alerte","Observabilité (logs, métriques, traces) avec AIOps"] },
      { text: "La gestion des changements (Change Management) est-elle processée et contrôlée ?", opts: ["Changements non contrôlés","CAB informel, changements non tracés","CAB formel avec analyse d'impact","Change Management automatisé avec CI/CD gates"] },
      { text: "Les SLA de disponibilité des services SI sont-ils définis et mesurés ?", opts: ["Aucun SLA défini","SLA internes estimés sans mesure","SLA mesurés et reportés mensuellement","SLA contractualisés avec pénalités et tableau de bord"] },
      { text: "La CMDB est-elle maintenue et utilisée pour l'impact analysis lors des incidents ?", opts: ["Aucune CMDB","CMDB partielle et non maintenue","CMDB complète, revue semestrielle","CMDB auto-découverte, utilisée en temps réel"] }
    ]
  },
  {
    id: 'devops', icon: '🚀', title: 'DevOps & Chaîne de Livraison',
    desc: "CI/CD, automatisation, IaC, intégration dev-ops",
    questions: [
      { text: "Une chaîne CI/CD est-elle en place pour les applications majeures ?", opts: ["Déploiements manuels uniquement","CI partielle (intégration automatisée)","CI/CD complet sur les applications principales","CI/CD avec tests automatisés et déploiement continu"] },
      { text: "L'Infrastructure as Code (IaC) est-elle pratiquée pour le provisionnement ?", opts: ["Aucun IaC, tout est manuel","Scripts de déploiement non versionnés","IaC avec Terraform/Ansible sur les nouveaux projets","IaC généralisé, versioning, drift detection"] },
      { text: "Les environnements (dev, recette, prod) sont-ils cloisonnés et cohérents ?", opts: ["Pas d'environnement de recette distinct","Environnements partiellement séparés","Dev/Recette/Prod distincts et documentés","Environnements éphémères créés à la demande (IaC)"] },
      { text: "Des métriques DevOps (DORA : fréquence de déploiement, MTTR, taux d'échec) sont-elles suivies ?", opts: ["Aucune métrique DevOps","Quelques métriques ad hoc","Métriques DORA suivies et reportées","Amélioration continue guidée par les métriques DORA"] },
      { text: "La sécurité est-elle intégrée dans la chaîne DevOps (DevSecOps, SAST, DAST) ?", opts: ["Sécurité hors du pipeline de livraison","Tests de sécurité manuels post-déploiement","SAST intégré dans le pipeline CI","DevSecOps complet avec SAST, DAST, SCA, secrets scanning"] }
    ]
  }
];

let diCurrentDomain = 0;
let diAnswers = {};
let diClientInfo = {};

function diStartDiag() {
  diClientInfo = {
    name: document.getElementById('diClientName').value || 'Client',
    sector: document.getElementById('diClientSector').value || '—',
    contact: document.getElementById('diClientContact').value || '—',
    date: document.getElementById('diDiagDate').value || new Date().toLocaleDateString('fr-FR'),
    size: document.getElementById('diSiSize').value,
    cloud: document.getElementById('diCloudMat').value
  };
  document.getElementById('di-intro-section').classList.add('di-hidden');
  document.getElementById('di-questions-section').classList.remove('di-hidden');
  document.getElementById('di-nav-bar').style.display = 'flex';
  diBuildPages();
  diShowDomain(0);
}

function diBuildPages() {
  const container = document.getElementById('di-domain-pages');
  container.innerHTML = '';
  DI_DOMAINS.forEach((domain, di) => {
    const page = document.createElement('div');
    page.className = 'di-section-page';
    page.id = 'di-page-' + di;
    let html = `<div class="di-domain-block">
      <div class="di-domain-header">
        <div class="di-domain-icon">${domain.icon}</div>
        <div><div class="di-domain-title">${domain.title}</div><div class="di-domain-desc">${domain.desc}</div></div>
      </div>`;
    domain.questions.forEach((q, qi) => {
      const qKey = di + '_' + qi;
      html += `<div class="di-question-card" id="di-qcard-${qKey}">
        <div class="di-question-num">Question ${qi+1}/${domain.questions.length}</div>
        <div class="di-question-text">${q.text}</div>
        <div class="di-options">`;
      q.opts.forEach((opt, oi) => {
        html += `<button class="di-option-btn" id="di-opt-${qKey}-${oi}" onclick="diSelectOpt(${di},${qi},${oi})">
          <span class="di-option-score di-score-${oi < 3 ? oi : 3}">${oi}</span>${opt}</button>`;
      });
      html += `</div></div>`;
    });
    html += '</div>';
    page.innerHTML = html;
    container.appendChild(page);
  });
}

function diSelectOpt(di, qi, score) {
  const qKey = di + '_' + qi;
  diAnswers[qKey] = score;
  document.getElementById('di-qcard-' + qKey).classList.add('answered');
  DI_DOMAINS[di].questions[qi].opts.forEach((_, oi) => {
    const b = document.getElementById('di-opt-' + qKey + '-' + oi);
    if (b) b.classList.remove('selected');
  });
  const selBtn = document.getElementById('di-opt-' + qKey + '-' + score);
  if (selBtn) selBtn.classList.add('selected');
  diUpdateProgress();
}

function diShowDomain(idx) {
  document.querySelectorAll('.di-section-page').forEach(p => p.classList.remove('active'));
  const page = document.getElementById('di-page-' + idx);
  if (page) { page.classList.add('active'); window.scrollTo(0,0); }
  diCurrentDomain = idx;
  document.getElementById('di-btn-prev').disabled = idx === 0;
  const isLast = idx === DI_DOMAINS.length - 1;
  const nextBtn = document.getElementById('di-btn-next');
  nextBtn.textContent = isLast ? 'Voir les résultats →' : 'Suivant →';
  nextBtn.className = isLast ? 'di-btn di-btn-gold' : 'di-btn di-btn-primary';
  document.getElementById('di-section-counter').textContent = `Domaine ${idx+1} / ${DI_DOMAINS.length} — ${DI_DOMAINS[idx].title}`;
  diUpdateProgress();
}

function diPrevDomain() { if (diCurrentDomain > 0) diShowDomain(diCurrentDomain - 1); }
function diNextDomain() {
  if (diCurrentDomain < DI_DOMAINS.length - 1) diShowDomain(diCurrentDomain + 1);
  else diShowResults();
}

function diUpdateProgress() {
  const total = DI_DOMAINS.reduce((s,d) => s + d.questions.length, 0);
  const done = Object.keys(diAnswers).length;
  const pct = Math.round((done / total) * 100);
  document.getElementById('diProgressFill').style.width = pct + '%';
  document.getElementById('diProgressPct').textContent = pct + '%';
}

function diCalcDomainScore(di) {
  const d = DI_DOMAINS[di];
  let sum = 0, count = 0;
  d.questions.forEach((_, qi) => {
    const k = di + '_' + qi;
    if (diAnswers[k] !== undefined) { sum += diAnswers[k]; count++; }
  });
  if (count === 0) return 0;
  return Math.round((sum / (count * 3)) * 100);
}

function diGetColor(pct) {
  if (pct < 25) return { bar:'#c0392b', text:'#c0392b', label:'Initial' };
  if (pct < 50) return { bar:'#d35400', text:'#d35400', label:'Basique' };
  if (pct < 75) return { bar:'#c8a200', text:'#856a00', label:'Défini' };
  return { bar:'#1a7a4a', text:'#1a7a4a', label:'Optimisé' };
}

function diGetRecos() {
  const recos = [];
  DI_DOMAINS.forEach((d, di) => {
    d.questions.forEach((q, qi) => {
      const k = di + '_' + qi;
      const ans = diAnswers[k] !== undefined ? diAnswers[k] : 0;
      if (ans <= 1) recos.push({ prio: 'Priorité critique', domain: d.title, action: q.text, next: q.opts[Math.min(ans+1,3)] });
      else if (ans === 2) recos.push({ prio: 'Priorité haute', domain: d.title, action: q.text, next: q.opts[3] });
    });
  });
  recos.sort((a,b) => a.prio === 'Priorité critique' ? -1 : 1);
  return recos.slice(0, 12);
}

function diGetRoadmap(domainScores) {
  const phases = [
    { label: 'Phase 1 - Fondations (M1-M3)', items: [] },
    { label: 'Phase 2 - Consolidation (M4-M6)', items: [] },
    { label: 'Phase 3 - Optimisation (M7-M9)', items: [] },
    { label: 'Phase 4 - Excellence (M10-M12)', items: [] }
  ];
  DI_DOMAINS.forEach((d, di) => {
    const sc = domainScores[di];
    if (sc < 25) phases[0].items.push(d.title + ' : mise a niveau urgente');
    else if (sc < 50) phases[1].items.push(d.title + ' : structuration et formalisation');
    else if (sc < 75) phases[2].items.push(d.title + ' : optimisation des pratiques');
    else phases[3].items.push(d.title + ' : amelioration continue');
  });
  return phases;
}

function diShowResults() {
  document.getElementById('di-questions-section').classList.add('di-hidden');
  document.getElementById('di-nav-bar').style.display = 'none';
  document.getElementById('di-results-section').style.display = 'block';

  const domainScores = DI_DOMAINS.map((_, di) => diCalcDomainScore(di));
  const globalScore = Math.round(domainScores.reduce((a,b) => a+b, 0) / domainScores.length);
  const col = diGetColor(globalScore);

  document.getElementById('di-result-score-big').textContent = globalScore;
  document.getElementById('di-result-level-text').textContent = 'Niveau : ' + col.label;
  document.getElementById('di-result-client-text').textContent = diClientInfo.name + ' · ' + diClientInfo.sector + ' · ' + diClientInfo.date;

  const dsGrid = document.getElementById('di-domain-scores-grid');
  dsGrid.innerHTML = '';
  DI_DOMAINS.forEach((d, di) => {
    const sc = domainScores[di];
    const c = diGetColor(sc);
    dsGrid.innerHTML += `<div class="di-domain-score-card">
      <div class="di-ds-name">${d.icon} ${d.title}</div>
      <div class="di-ds-bar-track"><div class="di-ds-bar-fill" style="width:${sc}%;background:${c.bar}"></div></div>
      <div class="di-ds-pct" style="color:${c.text}">${sc}% — ${c.label}</div>
    </div>`;
  });

  diBuildRadar(domainScores);
  diBuildRecoList();
  diBuildRoadmap(domainScores);
  diUpdateProgress();
  window.scrollTo(0, 0);
}

function diBuildRadar(domainScores) {
  const ctx = document.getElementById('diRadarChart').getContext('2d');
  new Chart(ctx, {
    type: 'radar',
    data: {
      labels: DI_DOMAINS.map(d => d.title),
      datasets: [{
        label: diClientInfo.name,
        data: domainScores,
        backgroundColor: 'rgba(26,16,24,0.12)',
        borderColor: '#1a1018',
        borderWidth: 2,
        pointBackgroundColor: '#1a1018',
        pointRadius: 4
      }, {
        label: 'Cible recommandée',
        data: [75,75,75,75,75,75,75],
        backgroundColor: 'rgba(211,166,37,0.08)',
        borderColor: '#D3A625',
        borderWidth: 1.5,
        borderDash: [4,4],
        pointRadius: 0
      }]
    },
    options: {
      scales: { r: { min:0, max:100, ticks:{ stepSize:25, font:{size:10} }, grid:{color:'#e8dae5'}, pointLabels:{font:{size:11,family:'DM Sans'}} } },
      plugins: { legend: { labels: { font:{size:12,family:'DM Sans'} } } }
    }
  });
}

function diBuildRecoList() {
  const recos = diGetRecos();
  const list = document.getElementById('di-reco-list');
  list.innerHTML = '';
  if (recos.length === 0) {
    list.innerHTML = '<div style="color:#1a7a4a;font-size:13px;padding:12px">Tres bon niveau de maturite — aucune action critique identifiee.</div>';
    return;
  }
  recos.forEach(r => {
    const cls = r.prio === 'Priorité critique' ? 'di-reco-critical' : 'di-reco-important';
    const clr = r.prio === 'Priorité critique' ? '#c0392b' : '#d35400';
    list.innerHTML += `<div class="di-reco-item ${cls}">
      <div>
        <div class="di-reco-prio" style="color:${clr}">${r.prio} · ${r.domain}</div>
        <div class="di-reco-text"><strong style="color:#1a1018">Situation :</strong> ${r.action}</div>
        <div class="di-reco-text" style="margin-top:3px"><strong style="color:#1a1018">Prochaine étape :</strong> ${r.next}</div>
      </div>
    </div>`;
  });
}

function diBuildRoadmap(domainScores) {
  const phases = [
    { label: 'Phase 1 — Fondations (M1-M3)', bg:'#fdf0ef', border:'#c0392b', items:[] },
    { label: 'Phase 2 — Consolidation (M4-M6)', bg:'#fdf6ed', border:'#d35400', items:[] },
    { label: 'Phase 3 — Optimisation (M7-M9)', bg:'#fffce6', border:'#c8a200', items:[] },
    { label: 'Phase 4 — Excellence (M10-M12)', bg:'#f0fdf4', border:'#1a7a4a', items:[] }
  ];
  DI_DOMAINS.forEach((d, di) => {
    const sc = domainScores[di];
    if (sc < 25) phases[0].items.push(`${d.icon} ${d.title} : mise à niveau urgente`);
    else if (sc < 50) phases[1].items.push(`${d.icon} ${d.title} : structuration et formalisation`);
    else if (sc < 75) phases[2].items.push(`${d.icon} ${d.title} : optimisation des pratiques`);
    else phases[3].items.push(`${d.icon} ${d.title} : amélioration continue`);
  });
  const grid = document.getElementById('di-roadmap-grid');
  grid.innerHTML = '';
  phases.forEach(p => {
    if (p.items.length === 0) p.items.push('Aucun domaine dans cette phase');
    const itemsHtml = p.items.map(i => `<div class="di-tl-item"><div class="di-tl-dot"></div><div>${i}</div></div>`).join('');
    grid.innerHTML += `<div class="di-tl-phase" style="background:${p.bg};border-left:3px solid ${p.border}">
      <h4 style="color:${p.border}">${p.label}</h4>${itemsHtml}</div>`;
  });
}

// ── Gate ──────────────────────────────────────────────────────────────────────
function diRevealForm() {
  document.getElementById('diGateCta').style.display = 'none';
  document.getElementById('diGateForm').style.display = 'block';
}

function diUnlockContent() {
  const overlay = document.getElementById('diGateOverlay');
  document.getElementById('diGateForm').style.display = 'none';
  document.getElementById('diGateConfirm').style.display = 'block';
  setTimeout(() => {
    overlay.style.transition = 'opacity 0.7s ease';
    overlay.style.opacity = '0';
    setTimeout(() => {
      overlay.style.display = 'none';
      const content = document.getElementById('diRecoContent');
      if (content) content.classList.remove('di-blurred');
    }, 700);
  }, 2000);
}

// ── Génération PDF ────────────────────────────────────────────────────────────
function hexToRgbDI(hex) {
  return [parseInt(hex.slice(1,3),16), parseInt(hex.slice(3,5),16), parseInt(hex.slice(5,7),16)];
}

function diGeneratePDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  const domainScores = DI_DOMAINS.map((_, di) => diCalcDomainScore(di));
  const globalScore = Math.round(domainScores.reduce((a,b) => a+b, 0) / domainScores.length);
  const col = diGetColor(globalScore);

  let y = 20;

  // Titre
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(18);
  doc.setTextColor(26, 16, 24);
  doc.text('Diagnostic Maturite Infrastructure SI', 20, y);
  y += 8;
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(10);
  doc.setTextColor(138, 112, 133);
  doc.text("Galyn'Up - CIO Advisory | ITIL 4 - TOGAF - ISO 20000", 20, y);
  y += 10;
  doc.setDrawColor(211, 166, 37);
  doc.setLineWidth(0.8);
  doc.line(20, y, 190, y);
  y += 10;

  // Infos client
  doc.setFontSize(10);
  doc.setTextColor(26, 16, 24);
  [['Client', diClientInfo.name || '-'],
   ['Secteur', diClientInfo.sector || '-'],
   ['Interlocuteur', diClientInfo.contact || '-'],
   ['Date', diClientInfo.date || '-'],
   ['Taille SI', diClientInfo.size || '-'],
   ['Maturite cloud', diClientInfo.cloud || '-']
  ].forEach(([lbl, val]) => {
    doc.setFont('helvetica', 'bold'); doc.text(lbl + ' :', 20, y);
    doc.setFont('helvetica', 'normal'); doc.text(val, 65, y);
    y += 6;
  });
  y += 4;

  // Score global
  doc.setFillColor(240, 235, 240);
  doc.roundedRect(20, y, 170, 14, 2, 2, 'F');
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(12);
  doc.setTextColor(26, 16, 24);
  doc.text('Score global : ' + globalScore + ' / 100  -  Niveau ' + col.label, 28, y + 9);
  y += 22;

  // Scores par domaine
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(12);
  doc.setTextColor(26, 16, 24);
  doc.text('Scores par domaine', 20, y);
  doc.setDrawColor(232, 218, 229);
  doc.setLineWidth(0.4);
  doc.line(20, y + 2, 190, y + 2);
  y += 10;

  DI_DOMAINS.forEach((d, di) => {
    const sc = domainScores[di];
    const c = diGetColor(sc);
    const [r,g,b] = hexToRgbDI(c.bar);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.setTextColor(26, 16, 24);
    doc.text(d.title, 22, y);
    doc.setFillColor(232, 218, 229);
    doc.roundedRect(130, y-4, 40, 5, 1, 1, 'F');
    doc.setFillColor(r, g, b);
    doc.roundedRect(130, y-4, Math.max(1, sc * 0.4), 5, 1, 1, 'F');
    doc.setFont('helvetica', 'bold');
    doc.setTextColor(r, g, b);
    doc.text(sc + '% - ' + c.label, 175, y);
    y += 7;
  });
  y += 8;

  // Recommandations
  doc.addPage();
  y = 20;
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(12);
  doc.setTextColor(26, 16, 24);
  doc.text('Plan de recommandations', 20, y);
  doc.setDrawColor(232, 218, 229);
  doc.setLineWidth(0.4);
  doc.line(20, y + 2, 190, y + 2);
  y += 10;

  const recos = diGetRecos();
  recos.forEach((r, i) => {
    if (y > 265) { doc.addPage(); y = 20; }
    const [r2,g2,b2] = hexToRgbDI(r.prio === 'Priorité critique' ? '#c0392b' : '#d35400');
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(9);
    doc.setTextColor(r2, g2, b2);
    doc.text((i+1) + '. ' + r.prio + ' - ' + r.domain, 20, y);
    y += 5;
    doc.setFont('helvetica', 'normal');
    doc.setTextColor(26, 16, 24);
    const aLines = doc.splitTextToSize('Situation : ' + r.action, 165);
    doc.text(aLines, 23, y);
    y += aLines.length * 4.5 + 1;
    doc.setTextColor(74, 58, 71);
    const nLines = doc.splitTextToSize('Etape suivante : ' + r.next, 165);
    doc.text(nLines, 23, y);
    y += nLines.length * 4.5 + 5;
  });

  // Feuille de route
  if (y > 230) { doc.addPage(); y = 20; }
  else { y += 8; }
  doc.setDrawColor(232, 218, 229);
  doc.line(20, y, 190, y);
  y += 8;
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(12);
  doc.setTextColor(26, 16, 24);
  doc.text('Feuille de route - 12 mois', 20, y);
  y += 8;

  const roadmap = diGetRoadmap(domainScores);
  roadmap.forEach(phase => {
    if (y > 265) { doc.addPage(); y = 20; }
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10);
    doc.setTextColor(74, 58, 71);
    doc.text(phase.label, 20, y);
    y += 5;
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);
    doc.setTextColor(74, 58, 71);
    phase.items.forEach(item => {
      doc.text('- ' + item, 25, y);
      y += 5;
    });
    y += 3;
  });

  // Footer
  const pages = doc.internal.getNumberOfPages();
  for (let p = 1; p <= pages; p++) {
    doc.setPage(p);
    doc.setFontSize(8);
    doc.setTextColor(138, 112, 133);
    doc.text("Galyn'Up - CIO Advisory | galynup.fr", 20, 290);
    doc.text('Page ' + p + '/' + pages, 190, 290, { align: 'right' });
  }

  return doc.output('datauristring').split(',')[1];
}

function diGenerateCSVBase64() {
  let csv = '\uFEFF';
  csv += 'Domaine,Question,Reponse selectionnee,Score (0-3)\n';
  DI_DOMAINS.forEach((d, di) => {
    d.questions.forEach((q, qi) => {
      const k = di + '_' + qi;
      const ans = diAnswers[k] !== undefined ? diAnswers[k] : '';
      const opt = ans !== '' ? q.opts[ans] : '';
      csv += '"' + d.title + '","' + q.text.replace(/"/g,'""') + '","' + opt.replace(/"/g,'""') + '",' + ans + '\n';
    });
  });
  return btoa(unescape(encodeURIComponent(csv)));
}

// ── Envoi formulaire ──────────────────────────────────────────────────────────
function diSendContact() {
  const name    = document.getElementById('diContactName').value.trim();
  const email   = document.getElementById('diContactEmail').value.trim();
  const message = document.getElementById('diContactMessage').value.trim();
  const msgEl   = document.getElementById('diGateMsg');
  const btn     = document.getElementById('diSendBtn');

  if (!name || !email) {
    msgEl.textContent = 'Merci de renseigner votre nom et votre email.';
    msgEl.className = 'di-gate-msg error';
    msgEl.style.display = 'block';
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Envoi en cours…';
  msgEl.style.display = 'none';

  const domainScores = DI_DOMAINS.map((d, di) => ({ domain: d.title, score: diCalcDomainScore(di) }));
  const globalScore  = Math.round(domainScores.reduce((a,b) => a + b.score, 0) / domainScores.length);
  const recos        = diGetRecos();
  const roadmap      = diGetRoadmap(domainScores.map(d => d.score));

  const diagData = JSON.stringify({ client: diClientInfo, domainScores, globalScore, recos, roadmap });

  let pdfB64 = '', csvB64 = '';
  try { pdfB64 = diGeneratePDF(); } catch(e) { console.warn('PDF generation failed', e); }
  try { csvB64 = diGenerateCSVBase64(); } catch(e) { console.warn('CSV generation failed', e); }

  const formData = new FormData();
  formData.append('name', name);
  formData.append('email', email);
  formData.append('message', message);
  formData.append('diag_data', diagData);
  formData.append('pdf_data', pdfB64);
  formData.append('csv_data', csvB64);

  fetch('php/send-diag-infra.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        diUnlockContent();
      } else {
        btn.disabled = false;
        btn.textContent = 'Envoyer →';
        msgEl.textContent = 'Erreur lors de l\'envoi. Veuillez réessayer.';
        msgEl.className = 'di-gate-msg error';
        msgEl.style.display = 'block';
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.textContent = 'Envoyer →';
      msgEl.textContent = 'Erreur réseau. Veuillez réessayer.';
      msgEl.className = 'di-gate-msg error';
      msgEl.style.display = 'block';
    });
}

function diResetDiag() {
  diAnswers = {}; diClientInfo = {}; diCurrentDomain = 0;
  document.getElementById('di-results-section').style.display = 'none';
  document.getElementById('di-intro-section').classList.remove('di-hidden');
  document.getElementById('di-nav-bar').style.display = 'none';
  document.getElementById('diProgressFill').style.width = '0%';
  document.getElementById('diProgressPct').textContent = '0%';
  const overlay = document.getElementById('diGateOverlay');
  overlay.style.display = 'flex';
  overlay.style.opacity = '1';
  const content = document.getElementById('diRecoContent');
  if (content) content.classList.add('di-blurred');
  document.getElementById('diGateCta').style.display = 'block';
  document.getElementById('diGateForm').style.display = 'none';
  document.getElementById('diGateConfirm').style.display = 'none';
}

document.getElementById('diDiagDate').valueAsDate = new Date();
  </script>
</body>
</html>
