<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Diagnostic Maturité Cybersécurité — Galyn'Up</title>
  <meta name="description" content="Évaluez la maturité cybersécurité de votre organisation sur 6 domaines clés. Outil propriétaire Galyn'Up — CIO Advisory.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
:root {
  --dc-aubergine: #6D155D;
  --dc-aubergine-light: #8B1F78;
  --dc-aubergine-pale: #f5edf3;
  --dc-gold: #D3A625;
  --dc-gold-light: #F0C040;
  --dc-dark: #1a1018;
  --dc-mid: #4a3a47;
  --dc-muted: #8a7085;
  --dc-border: #e8dae5;
  --dc-bg: #faf8fa;
  --dc-white: #ffffff;
  --dc-red: #c0392b;
  --dc-orange: #d35400;
  --dc-lime: #6aaa00;
  --dc-green: #1a7a4a;
}

#diag-cyber-wrap * { box-sizing: border-box; }
#diag-cyber-wrap { font-family: 'DM Sans', sans-serif; color: var(--dc-dark); font-size: 14px; line-height: 1.6; padding-top: 100px; }

.dc-progress-bar-wrap { background: var(--dc-aubergine-pale); border-bottom: 1px solid var(--dc-border); padding: 14px 40px; display: flex; align-items: center; gap: 16px; margin-bottom: 32px; }
.dc-progress-label { font-size: 12px; color: var(--dc-muted); white-space: nowrap; }
.dc-progress-track { flex: 1; height: 6px; background: var(--dc-border); border-radius: 3px; overflow: hidden; }
.dc-progress-fill { height: 100%; background: linear-gradient(90deg, var(--dc-aubergine), var(--dc-gold)); border-radius: 3px; transition: width 0.4s ease; width: 0%; }
.dc-progress-pct { font-size: 12px; font-weight: 500; color: var(--dc-aubergine); min-width: 36px; text-align: right; }

.dc-main { max-width: 860px; margin: 0 auto; padding: 0 24px 80px; }

.dc-domain-block { margin-bottom: 40px; }
.dc-domain-header { display: flex; align-items: center; gap: 14px; padding: 16px 20px; background: var(--dc-white); border: 1px solid var(--dc-border); border-left: 4px solid var(--dc-aubergine); border-radius: 8px; margin-bottom: 16px; }
.dc-domain-icon { width: 38px; height: 38px; background: var(--dc-aubergine-pale); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.dc-domain-title { font-family: 'Cormorant Garamond', serif; font-size: 20px; font-weight: 600; color: var(--dc-aubergine); }
.dc-domain-desc { font-size: 12px; color: var(--dc-muted); margin-top: 1px; }

.dc-question-card { background: var(--dc-white); border: 1px solid var(--dc-border); border-radius: 8px; padding: 18px 20px; margin-bottom: 10px; transition: border-color 0.2s; }
.dc-question-card.answered { border-color: var(--dc-aubergine); }
.dc-question-text { font-size: 13.5px; color: var(--dc-dark); margin-bottom: 14px; font-weight: 400; }
.dc-question-num { font-size: 11px; color: var(--dc-muted); margin-bottom: 4px; }

.dc-options { display: flex; flex-direction: column; gap: 6px; }
.dc-option-btn { display: flex; align-items: center; gap: 10px; padding: 9px 13px; border: 1.5px solid var(--dc-border); border-radius: 6px; background: var(--dc-bg); cursor: pointer; font-size: 13px; color: var(--dc-mid); text-align: left; transition: all 0.15s; font-family: 'DM Sans', sans-serif; width: 100%; }
.dc-option-btn:hover { border-color: var(--dc-aubergine-light); background: var(--dc-aubergine-pale); }
.dc-option-btn.selected { border-color: var(--dc-aubergine); background: var(--dc-aubergine-pale); color: var(--dc-aubergine); font-weight: 500; }
.dc-option-score { min-width: 20px; height: 20px; border-radius: 50%; font-size: 10px; font-weight: 500; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.dc-score-0 { background: #fde8e6; color: var(--dc-red); }
.dc-score-1 { background: #fde8e6; color: var(--dc-red); }
.dc-score-2 { background: #fdf0e0; color: var(--dc-orange); }
.dc-score-3 { background: #edfff0; color: var(--dc-lime); }

.dc-nav-bar { position: sticky; bottom: 0; background: rgba(250,248,250,0.95); backdrop-filter: blur(8px); border-top: 1px solid var(--dc-border); padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
.dc-nav-inner { max-width: 860px; margin: 0 auto; width: 100%; display: flex; justify-content: space-between; align-items: center; }
.dc-btn { padding: 10px 24px; border-radius: 6px; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; border: none; }
.dc-btn-primary { background: var(--dc-aubergine); color: white; }
.dc-btn-primary:hover { background: var(--dc-aubergine-light); }
.dc-btn-primary:disabled { opacity: 0.4; cursor: not-allowed; }
.dc-btn-secondary { background: transparent; color: var(--dc-aubergine); border: 1.5px solid var(--dc-aubergine); }
.dc-btn-secondary:hover { background: var(--dc-aubergine-pale); }
.dc-btn-gold { background: var(--dc-gold); color: white; }
.dc-btn-gold:hover { background: var(--dc-gold-light); }
.dc-section-counter { font-size: 12px; color: var(--dc-muted); }

.dc-intro-card { background: var(--dc-white); border: 1px solid var(--dc-border); border-radius: 10px; padding: 36px 40px; margin-bottom: 32px; }
.dc-intro-card h2 { font-family: 'Cormorant Garamond', serif; font-size: 26px; font-weight: 500; color: var(--dc-aubergine); margin-bottom: 12px; }
.dc-intro-card p { color: var(--dc-mid); margin-bottom: 10px; }
.dc-intro-meta { display: flex; gap: 20px; flex-wrap: wrap; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--dc-border); }
.dc-meta-item { display: flex; flex-direction: column; gap: 2px; }
.dc-meta-label { font-size: 11px; color: var(--dc-muted); text-transform: uppercase; letter-spacing: 0.06em; }
.dc-meta-value { font-size: 13px; font-weight: 500; color: var(--dc-dark); }
.dc-client-form { margin-top: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.dc-form-group { display: flex; flex-direction: column; gap: 4px; }
.dc-form-group label { font-size: 12px; color: var(--dc-muted); }
.dc-form-group input, .dc-form-group select { padding: 9px 12px; border: 1.5px solid var(--dc-border); border-radius: 6px; font-family: 'DM Sans', sans-serif; font-size: 13px; background: var(--dc-bg); color: var(--dc-dark); outline: none; transition: border-color 0.2s; }
.dc-form-group input:focus, .dc-form-group select:focus { border-color: var(--dc-aubergine); }

#dc-results-section { display: none; }
.dc-result-header { background: var(--dc-aubergine); color: white; border-radius: 10px; padding: 28px 32px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; }
.dc-result-score-big { font-family: 'Cormorant Garamond', serif; font-size: 64px; font-weight: 600; line-height: 1; color: var(--dc-gold-light); }
.dc-result-level { font-size: 22px; font-weight: 500; margin-top: 4px; }
.dc-result-client { font-size: 13px; opacity: 0.7; margin-top: 6px; }
.dc-radar-wrap { background: var(--dc-white); border: 1px solid var(--dc-border); border-radius: 10px; padding: 24px; margin-bottom: 20px; }
.dc-radar-wrap h3 { font-family: 'Cormorant Garamond', serif; font-size: 18px; color: var(--dc-aubergine); margin-bottom: 16px; }
.dc-domain-scores { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
.dc-domain-score-card { background: var(--dc-white); border: 1px solid var(--dc-border); border-radius: 8px; padding: 14px 16px; }
.dc-ds-name { font-size: 13px; font-weight: 500; color: var(--dc-dark); margin-bottom: 8px; }
.dc-ds-bar-track { height: 8px; background: var(--dc-bg); border-radius: 4px; overflow: hidden; margin-bottom: 6px; }
.dc-ds-bar-fill { height: 100%; border-radius: 4px; }
.dc-ds-pct { font-size: 12px; font-weight: 500; }

/* ── Gate / Blur ─────────────────────────────────────────────────────────── */
.dc-gate-wrapper { position: relative; margin-bottom: 20px; }
.dc-blurred { filter: blur(5px); pointer-events: none; user-select: none; }
.dc-gate-overlay {
  position: absolute; top: 0; left: 0; right: 0; bottom: 0;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  background: rgba(250,248,250,0.88);
  border-radius: 10px; z-index: 10; padding: 32px;
  transition: opacity 0.6s ease;
  overflow-y: auto;
}
.dc-gate-inner { max-width: 480px; width: 100%; text-align: center; }
.dc-gate-inner h3 { font-family: 'Cormorant Garamond', serif; font-size: 22px; font-weight: 500; color: var(--dc-aubergine); margin-bottom: 10px; }
.dc-gate-inner p { font-size: 13px; color: var(--dc-mid); }
.dc-gate-form { text-align: left; margin-top: 18px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.dc-gate-form .dc-full { grid-column: 1 / -1; }
.dc-gate-form input, .dc-gate-form textarea { width: 100%; padding: 9px 12px; border: 1.5px solid var(--dc-border); border-radius: 6px; font-family: 'DM Sans', sans-serif; font-size: 13px; background: var(--dc-white); color: var(--dc-dark); outline: none; transition: border-color 0.2s; }
.dc-gate-form input:focus, .dc-gate-form textarea:focus { border-color: var(--dc-aubergine); }
.dc-gate-form textarea { resize: vertical; min-height: 60px; }
.dc-gate-label { font-size: 12px; color: var(--dc-muted); display: block; margin-bottom: 4px; }
.dc-gate-msg { margin-top: 10px; font-size: 13px; display: none; }
.dc-gate-msg.success { color: var(--dc-green); }
.dc-gate-msg.error { color: var(--dc-red); }
.dc-gate-confirm-icon { font-size: 40px; margin-bottom: 12px; }

/* Reco preview (visible sous le blur) */
.dc-reco-section { background: var(--dc-white); border: 1px solid var(--dc-border); border-radius: 10px; padding: 24px; }
.dc-reco-section h3 { font-family: 'Cormorant Garamond', serif; font-size: 18px; color: var(--dc-aubergine); margin-bottom: 16px; }
.dc-reco-item { display: flex; gap: 12px; padding: 12px; border-radius: 7px; margin-bottom: 8px; border-left: 3px solid; }
.dc-reco-critical { background: #fdf0ef; border-color: var(--dc-red); }
.dc-reco-important { background: #fdf6ed; border-color: var(--dc-orange); }
.dc-reco-prio { font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 2px; }
.dc-reco-text { font-size: 13px; color: var(--dc-mid); }

.dc-export-bar { display: flex; gap: 10px; margin-top: 20px; flex-wrap: wrap; }
.dc-hidden { display: none !important; }
.dc-section-page { display: none; }
.dc-section-page.active { display: block; }
canvas { max-width: 100%; }
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
              <a href="diag-cyber.php" class="nav-dropdown-item active">🔒 Diag Cybersécurité</a>
              <a href="diag-infra.php" class="nav-dropdown-item">🖥️ Diag Infrastructure</a>
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

  <div id="diag-cyber-wrap">

    <div class="dc-progress-bar-wrap">
      <span class="dc-progress-label">Progression</span>
      <div class="dc-progress-track"><div class="dc-progress-fill" id="dcProgressFill"></div></div>
      <span class="dc-progress-pct" id="dcProgressPct">0%</span>
    </div>

    <div class="dc-main">

      <!-- INTRO -->
      <div id="dc-intro-section">
        <div class="dc-intro-card">
          <h2>Évaluation de la maturité cybersécurité</h2>
          <p>Ce diagnostic couvre <strong>6 domaines clés</strong> de la cybersécurité, soit <strong>30 questions</strong> structurées selon une échelle de maturité en 4 niveaux (Initial → Optimisé). Conçu pour un entretien de 45 à 60 minutes avec le RSSI ou le DSI.</p>
          <p>Le résultat produit un score global, une cartographie par domaine, et un plan de recommandations priorisées.</p>
          <div class="dc-intro-meta">
            <div class="dc-meta-item"><span class="dc-meta-label">Durée estimée</span><span class="dc-meta-value">45 – 60 min</span></div>
            <div class="dc-meta-item"><span class="dc-meta-label">Domaines</span><span class="dc-meta-value">6</span></div>
            <div class="dc-meta-item"><span class="dc-meta-label">Questions</span><span class="dc-meta-value">30</span></div>
            <div class="dc-meta-item"><span class="dc-meta-label">Référentiel</span><span class="dc-meta-value">NIST CSF · ISO 27001</span></div>
          </div>
          <div class="dc-client-form">
            <div class="dc-form-group"><label>Nom du client</label><input type="text" id="dcClientName" placeholder="Ex : Banque Régionale Ouest"></div>
            <div class="dc-form-group"><label>Secteur</label>
              <select id="dcClientSector">
                <option value="">-- Sélectionner --</option>
                <option>Banque / Finance</option><option>Assurance</option>
                <option>Secteur public</option><option>Industrie</option>
                <option>Santé</option><option>Autre</option>
              </select>
            </div>
            <div class="dc-form-group"><label>Interlocuteur</label><input type="text" id="dcClientContact" placeholder="Ex : Jean Martin, RSSI"></div>
            <div class="dc-form-group"><label>Date du diagnostic</label><input type="date" id="dcDiagDate"></div>
          </div>
        </div>
        <div style="text-align:center; margin-top:8px;">
          <button class="dc-btn dc-btn-primary" onclick="dcStartDiag()" style="padding:12px 36px; font-size:15px;">Démarrer le diagnostic →</button>
        </div>
      </div>

      <!-- QUESTIONS -->
      <div id="dc-questions-section" class="dc-hidden">
        <div id="dc-domain-pages"></div>
      </div>

      <!-- RESULTS -->
      <div id="dc-results-section">

        <div class="dc-result-header">
          <div>
            <div style="font-size:13px; opacity:0.7; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.06em;">Rapport de diagnostic</div>
            <div class="dc-result-level" id="dc-result-level-text">—</div>
            <div class="dc-result-client" id="dc-result-client-text">—</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:12px; opacity:0.6; margin-bottom:4px;">Score global</div>
            <div class="dc-result-score-big" id="dc-result-score-big">—</div>
            <div style="font-size:13px; opacity:0.7;">/ 100</div>
          </div>
        </div>

        <div class="dc-radar-wrap">
          <h3>Cartographie par domaine</h3>
          <canvas id="dcRadarChart" height="320"></canvas>
        </div>

        <div class="dc-domain-scores" id="dc-domain-scores-grid"></div>

        <!-- ── Plan de recommandations — GATED ────────────────────────────── -->
        <div class="dc-gate-wrapper" id="dcRecoGate">

          <!-- Contenu flouté -->
          <div class="dc-blurred" id="dcRecoContent">
            <div class="dc-reco-section">
              <h3>Plan de recommandations</h3>
              <div id="dc-reco-list"></div>
            </div>
          </div>

          <!-- Overlay gate -->
          <div class="dc-gate-overlay" id="dcGateOverlay">

            <!-- Étape 1 : CTA -->
            <div class="dc-gate-inner" id="dcGateCta">
              <div style="font-size:36px; margin-bottom:14px;">🔒</div>
              <h3>Analyse approfondie</h3>
              <p>Votre plan de recommandations personnalisé vous sera envoyé avec un rapport PDF et un export CSV complet du diagnostic.</p>
              <button class="dc-btn dc-btn-primary" onclick="dcRevealForm()" style="margin-top:20px; padding:12px 28px;">
                Demander l'analyse approfondie →
              </button>
            </div>

            <!-- Étape 2 : Formulaire -->
            <div class="dc-gate-inner" id="dcGateForm" style="display:none;">
              <h3>Analyse approfondie</h3>
              <p>Laissez vos coordonnées — vous recevrez le rapport complet sous 48h.</p>
              <div class="dc-gate-form">
                <div>
                  <label class="dc-gate-label">Nom *</label>
                  <input type="text" id="dcContactName" placeholder="Votre nom">
                </div>
                <div>
                  <label class="dc-gate-label">Email *</label>
                  <input type="email" id="dcContactEmail" placeholder="votre@email.com">
                </div>
                <div class="dc-full">
                  <label class="dc-gate-label">Message (optionnel)</label>
                  <textarea id="dcContactMessage" placeholder="Contexte, questions, disponibilités…"></textarea>
                </div>
              </div>
              <div style="margin-top:16px; text-align:center;">
                <button class="dc-btn dc-btn-primary" id="dcSendBtn" onclick="dcSendContact()">Envoyer →</button>
              </div>
              <div class="dc-gate-msg" id="dcGateMsg"></div>
            </div>

            <!-- Étape 3 : Confirmation -->
            <div class="dc-gate-inner" id="dcGateConfirm" style="display:none;">
              <div class="dc-gate-confirm-icon">✓</div>
              <h3>Demande envoyée</h3>
              <p>Merci ! Nous vous contacterons sous 48h avec votre analyse et les fichiers PDF et CSV du diagnostic.</p>
            </div>

          </div><!-- /#dcGateOverlay -->
        </div><!-- /.dc-gate-wrapper -->

        <!-- Bouton nouveau diagnostic uniquement -->
        <div class="dc-export-bar">
          <button class="dc-btn dc-btn-secondary" onclick="dcResetDiag()">Nouveau diagnostic</button>
        </div>

      </div><!-- /#dc-results-section -->

    </div><!-- /.dc-main -->
  </div><!-- /#diag-cyber-wrap -->

  <div class="dc-nav-bar" id="dc-nav-bar" style="display:none;">
    <div class="dc-nav-inner">
      <button class="dc-btn dc-btn-secondary" id="dc-btn-prev" onclick="dcPrevDomain()">← Précédent</button>
      <span class="dc-section-counter" id="dc-section-counter"></span>
      <button class="dc-btn dc-btn-primary" id="dc-btn-next" onclick="dcNextDomain()">Suivant →</button>
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
const DC_DOMAINS = [
  {
    id: 'gouvernance', icon: '⚖️', title: 'Gouvernance & Politique',
    desc: 'Cadre stratégique, politique de sécurité, organisation RSSI',
    questions: [
      { text: "La politique de sécurité du SI est-elle formalisée et approuvée par la direction ?", opts: ["Aucune politique formalisée","Politique partielle, non approuvée","Politique documentée et approuvée","Politique revue et mise à jour annuellement"] },
      { text: "Existe-t-il un RSSI (ou équivalent) identifié avec des responsabilités claires ?", opts: ["Aucun rôle dédié","Rôle informel, non officiel","RSSI nommé mais ressources limitées","RSSI avec budget, équipe et reporting COMEX"] },
      { text: "Les risques cyber sont-ils intégrés à la cartographie des risques de l'organisation ?", opts: ["Aucune cartographie des risques","Risques identifiés sans formalisation","Cartographie formalisée mais non maintenue","Cartographie intégrée au ERM, revue périodique"] },
      { text: "Des indicateurs de sécurité (KPI/KRI) sont-ils suivis et reportés à la direction ?", opts: ["Aucun indicateur","Quelques métriques ad hoc","Tableau de bord technique mensuel","Dashboard stratégique présenté au COMEX"] },
      { text: "L'organisation dispose-t-elle d'un plan de traitement des risques priorisé ?", opts: ["Aucun plan","Actions ponctuelles sans priorisation","Plan documenté non budgétisé","Plan budgétisé avec suivi trimestriel"] }
    ]
  },
  {
    id: 'acces', icon: '🔐', title: 'Gestion des Accès & Identités',
    desc: 'IAM, authentification, droits, privilèges',
    questions: [
      { text: "L'authentification multifacteur (MFA) est-elle déployée sur les accès critiques ?", opts: ["MFA inexistant","MFA sur quelques comptes admin","MFA sur tous les comptes admin et VPN","MFA généralisé à tous les utilisateurs"] },
      { text: "Le principe du moindre privilège est-il appliqué dans l'attribution des droits ?", opts: ["Droits larges accordés sans contrôle","Revue ponctuelle des droits","Processus de revue des droits semestriel","Revue continue, accès Just-in-Time"] },
      { text: "Les comptes à privilèges élevés (admin, root) sont-ils contrôlés et tracés ?", opts: ["Comptes partagés sans traçabilité","Comptes nominatifs sans supervision","Journalisation des actions admin","PAM déployé avec supervision temps réel"] },
      { text: "La gestion des identités (onboarding/offboarding) est-elle automatisée ?", opts: ["Processus manuel et informel","Checklist manuelle avec oublis fréquents","Processus documenté avec validation RH","IAM automatisé, synchronisation RH temps réel"] },
      { text: "Les accès des prestataires et tiers sont-ils contrôlés et limités dans le temps ?", opts: ["Accès permanents non supervisés","Accès créés manuellement sans durée","Accès temporaires avec revue périodique","Accès segmentés, durée limitée, auditables"] }
    ]
  },
  {
    id: 'protection', icon: '🛡️', title: 'Protection & Sécurité Réseau',
    desc: 'Pare-feux, segmentation, chiffrement, endpoint',
    questions: [
      { text: "La segmentation réseau est-elle en place (DMZ, VLAN, micro-segmentation) ?", opts: ["Réseau plat, aucune segmentation","Quelques VLAN sans règles strictes","Segmentation documentée et maintenue","Micro-segmentation Zero Trust déployée"] },
      { text: "Les endpoints (postes, mobiles) sont-ils protégés par un EDR / antivirus managé ?", opts: ["Aucune protection centralisée","Antivirus basique non managé","EDR déployé avec supervision centralisée","EDR avec réponse automatisée (XDR/SOAR)"] },
      { text: "Les communications sensibles sont-elles chiffrées (VPN, TLS, chiffrement disque) ?", opts: ["Aucun chiffrement systématique","Chiffrement partiel (HTTPS uniquement)","VPN + chiffrement disque déployés","Chiffrement de bout en bout, gestion de clés"] },
      { text: "Un pare-feu applicatif (WAF) protège-t-il les applications exposées sur internet ?", opts: ["Aucun WAF","Pare-feu réseau uniquement","WAF déployé sur les apps principales","WAF managé avec règles adaptatives"] },
      { text: "Les vulnérabilités sont-elles identifiées et corrigées de façon structurée ?", opts: ["Aucun scan de vulnérabilités","Scans ponctuels sans suivi","Scan mensuel avec plan de patch","Gestion continue des vulnérabilités (VM)"] }
    ]
  },
  {
    id: 'detection', icon: '👁️', title: 'Détection & Surveillance',
    desc: 'SOC, SIEM, monitoring, logs',
    questions: [
      { text: "Un SIEM ou outil de corrélation d'événements de sécurité est-il en place ?", opts: ["Aucune centralisation des logs","Logs centralisés sans analyse","SIEM déployé avec règles de détection","SIEM managé avec SOC interne ou externe"] },
      { text: "Les événements de sécurité sont-ils monitorés 24h/24 ?", opts: ["Aucune surveillance continue","Supervision en heures ouvrées uniquement","Alertes automatiques avec astreinte","SOC 24/7 avec SLA de réponse défini"] },
      { text: "Des tests de détection (purple team, pentest) sont-ils réalisés régulièrement ?", opts: ["Aucun test réalisé","Pentest ponctuel sans suivi","Pentest annuel avec plan de remédiation","Red/Purple team régulier, tests continus"] },
      { text: "La durée de conservation des logs est-elle suffisante pour les investigations ?", opts: ["Logs non conservés ou < 1 mois","1 à 3 mois de rétention","6 mois de rétention","12 mois+ avec intégrité garantie"] },
      { text: "Des indicateurs de compromission (IoC) sont-ils activement surveillés ?", opts: ["Aucune veille sur les IoC","Abonnement flux de veille sans usage","Intégration IoC dans les outils de détection","Threat Intelligence active et contextualisée"] }
    ]
  },
  {
    id: 'resilience', icon: '🔄', title: 'Résilience & Continuité',
    desc: 'PCA, PRA, sauvegardes, gestion de crise',
    questions: [
      { text: "Un Plan de Continuité d'Activité (PCA) incluant la dimension cyber est-il formalisé ?", opts: ["Aucun PCA","PCA partiel, non testé","PCA documenté et approuvé","PCA testé annuellement avec exercice de crise"] },
      { text: "Les sauvegardes sont-elles réalisées, testées et isolées du réseau principal ?", opts: ["Sauvegardes inexistantes ou non testées","Sauvegardes réalisées, jamais testées","Sauvegardes testées avec restauration validée","Règle 3-2-1 appliquée, backup offline, immuable"] },
      { text: "Un plan de réponse aux incidents cyber est-il défini et connu des équipes ?", opts: ["Aucune procédure définie","Procédures informelles","Playbooks documentés pour scénarios majeurs","CSIRT/équipe de réponse active, exercices réguliers"] },
      { text: "Les délais de reprise (RTO/RPO) sont-ils définis pour les systèmes critiques ?", opts: ["RTO/RPO non définis","RTO/RPO estimés sans contrat","RTO/RPO formalisés et contractualisés","RTO/RPO testés et respectés lors d'exercices"] },
      { text: "Une cellule de crise cyber est-elle organisée avec des rôles clairs ?", opts: ["Aucune organisation de crise","Organisation ad hoc en cas d'incident","Cellule définie avec annuaire de crise","Cellule rodée, exercices COMEX inclus"] }
    ]
  },
  {
    id: 'conformite', icon: '📋', title: 'Conformité & Sensibilisation',
    desc: 'RGPD, réglementations sectorielles, formation',
    questions: [
      { text: "Les exigences réglementaires applicables (RGPD, NIS2, DORA, etc.) sont-elles identifiées ?", opts: ["Non identifiées","Partiellement connues","Cartographiées et analysées","Suivi de conformité avec plan d'actions"] },
      { text: "Un programme de sensibilisation à la cybersécurité existe-t-il pour tous les collaborateurs ?", opts: ["Aucune sensibilisation","Communication ponctuelle (email)","Formation annuelle obligatoire","Programme continu avec simulations de phishing"] },
      { text: "Les tiers et prestataires sont-ils soumis à des exigences de sécurité contractuelles ?", opts: ["Aucune exigence contractuelle","Clauses génériques","Clauses cyber spécifiques dans les contrats","Audit fournisseurs et scoring de risque tiers"] },
      { text: "Des audits de sécurité internes ou externes sont-ils réalisés ?", opts: ["Aucun audit","Audit ponctuel sans suivi","Audit annuel avec plan de remédiation","Audit continu + certification (ISO 27001, HDS)"] },
      { text: "Les incidents de sécurité font-ils l'objet d'un retour d'expérience (RETEX) formalisé ?", opts: ["Aucun RETEX","Discussion informelle post-incident","RETEX documenté avec actions correctives","RETEX systématique intégré à l'amélioration continue"] }
    ]
  }
];

let dcCurrentDomain = 0;
let dcAnswers = {};
let dcClientInfo = {};

function dcStartDiag() {
  dcClientInfo = {
    name: document.getElementById('dcClientName').value || 'Client',
    sector: document.getElementById('dcClientSector').value || '—',
    contact: document.getElementById('dcClientContact').value || '—',
    date: document.getElementById('dcDiagDate').value || new Date().toLocaleDateString('fr-FR')
  };
  document.getElementById('dc-intro-section').classList.add('dc-hidden');
  document.getElementById('dc-questions-section').classList.remove('dc-hidden');
  document.getElementById('dc-nav-bar').style.display = 'flex';
  dcBuildPages();
  dcShowDomain(0);
}

function dcBuildPages() {
  const container = document.getElementById('dc-domain-pages');
  container.innerHTML = '';
  DC_DOMAINS.forEach((domain, di) => {
    const page = document.createElement('div');
    page.className = 'dc-section-page';
    page.id = 'dc-page-' + di;
    let html = `<div class="dc-domain-block">
      <div class="dc-domain-header">
        <div class="dc-domain-icon">${domain.icon}</div>
        <div><div class="dc-domain-title">${domain.title}</div><div class="dc-domain-desc">${domain.desc}</div></div>
      </div>`;
    domain.questions.forEach((q, qi) => {
      const qKey = di + '_' + qi;
      html += `<div class="dc-question-card" id="dc-qcard-${qKey}">
        <div class="dc-question-num">Question ${qi+1}/${domain.questions.length}</div>
        <div class="dc-question-text">${q.text}</div>
        <div class="dc-options">`;
      q.opts.forEach((opt, oi) => {
        html += `<button class="dc-option-btn" id="dc-opt-${qKey}-${oi}" onclick="dcSelectOpt(${di},${qi},${oi})">
          <span class="dc-option-score dc-score-${oi < 3 ? oi : 3}">${oi}</span>${opt}</button>`;
      });
      html += `</div></div>`;
    });
    html += '</div>';
    page.innerHTML = html;
    container.appendChild(page);
  });
}

function dcSelectOpt(di, qi, score) {
  const qKey = di + '_' + qi;
  dcAnswers[qKey] = score;
  document.getElementById('dc-qcard-' + qKey).classList.add('answered');
  DC_DOMAINS[di].questions[qi].opts.forEach((_, oi) => {
    const b = document.getElementById('dc-opt-' + qKey + '-' + oi);
    if (b) b.classList.remove('selected');
  });
  const selBtn = document.getElementById('dc-opt-' + qKey + '-' + score);
  if (selBtn) selBtn.classList.add('selected');
  dcUpdateProgress();
}

function dcShowDomain(idx) {
  document.querySelectorAll('.dc-section-page').forEach(p => p.classList.remove('active'));
  const page = document.getElementById('dc-page-' + idx);
  if (page) { page.classList.add('active'); window.scrollTo(0, 0); }
  dcCurrentDomain = idx;
  document.getElementById('dc-btn-prev').disabled = idx === 0;
  const isLast = idx === DC_DOMAINS.length - 1;
  const nextBtn = document.getElementById('dc-btn-next');
  nextBtn.textContent = isLast ? 'Voir les résultats →' : 'Suivant →';
  nextBtn.className = isLast ? 'dc-btn dc-btn-gold' : 'dc-btn dc-btn-primary';
  document.getElementById('dc-section-counter').textContent = `Domaine ${idx+1} / ${DC_DOMAINS.length} — ${DC_DOMAINS[idx].title}`;
  dcUpdateProgress();
}

function dcPrevDomain() { if (dcCurrentDomain > 0) dcShowDomain(dcCurrentDomain - 1); }
function dcNextDomain() {
  if (dcCurrentDomain < DC_DOMAINS.length - 1) dcShowDomain(dcCurrentDomain + 1);
  else dcShowResults();
}

function dcUpdateProgress() {
  const total = DC_DOMAINS.reduce((s, d) => s + d.questions.length, 0);
  const done = Object.keys(dcAnswers).length;
  const pct = Math.round((done / total) * 100);
  document.getElementById('dcProgressFill').style.width = pct + '%';
  document.getElementById('dcProgressPct').textContent = pct + '%';
}

function dcCalcDomainScore(di) {
  const d = DC_DOMAINS[di];
  let sum = 0, count = 0;
  d.questions.forEach((_, qi) => {
    const k = di + '_' + qi;
    if (dcAnswers[k] !== undefined) { sum += dcAnswers[k]; count++; }
  });
  if (count === 0) return 0;
  return Math.round((sum / (count * 3)) * 100);
}

function dcGetColor(pct) {
  if (pct < 25) return { bar: '#c0392b', text: '#c0392b', label: 'Initial' };
  if (pct < 50) return { bar: '#d35400', text: '#d35400', label: 'Basique' };
  if (pct < 75) return { bar: '#c8a200', text: '#856a00', label: 'Défini' };
  return { bar: '#1a7a4a', text: '#1a7a4a', label: 'Optimisé' };
}

function dcGetRecos() {
  const recos = [];
  DC_DOMAINS.forEach((d, di) => {
    d.questions.forEach((q, qi) => {
      const k = di + '_' + qi;
      const ans = dcAnswers[k] !== undefined ? dcAnswers[k] : 0;
      if (ans <= 1) recos.push({ prio: 'Priorité critique', domain: d.title, action: q.text, next: q.opts[Math.min(ans+1,3)] });
      else if (ans === 2) recos.push({ prio: 'Priorité haute', domain: d.title, action: q.text, next: q.opts[3] });
    });
  });
  recos.sort((a,b) => a.prio === 'Priorité critique' ? -1 : 1);
  return recos.slice(0, 12);
}

function dcShowResults() {
  document.getElementById('dc-questions-section').classList.add('dc-hidden');
  document.getElementById('dc-nav-bar').style.display = 'none';
  document.getElementById('dc-results-section').style.display = 'block';

  const domainScores = DC_DOMAINS.map((_, di) => dcCalcDomainScore(di));
  const globalScore = Math.round(domainScores.reduce((a,b) => a+b, 0) / domainScores.length);
  const col = dcGetColor(globalScore);

  document.getElementById('dc-result-score-big').textContent = globalScore;
  document.getElementById('dc-result-level-text').textContent = 'Niveau : ' + col.label;
  document.getElementById('dc-result-client-text').textContent = dcClientInfo.name + ' · ' + dcClientInfo.sector + ' · ' + dcClientInfo.date;

  const dsGrid = document.getElementById('dc-domain-scores-grid');
  dsGrid.innerHTML = '';
  DC_DOMAINS.forEach((d, di) => {
    const sc = domainScores[di];
    const c = dcGetColor(sc);
    dsGrid.innerHTML += `<div class="dc-domain-score-card">
      <div class="dc-ds-name">${d.icon} ${d.title}</div>
      <div class="dc-ds-bar-track"><div class="dc-ds-bar-fill" style="width:${sc}%;background:${c.bar}"></div></div>
      <div class="dc-ds-pct" style="color:${c.text}">${sc}% — ${c.label}</div>
    </div>`;
  });

  dcBuildRadar(domainScores);
  dcBuildRecoList();
  dcUpdateProgress();
  window.scrollTo(0, 0);
}

function dcBuildRadar(domainScores) {
  const ctx = document.getElementById('dcRadarChart').getContext('2d');
  new Chart(ctx, {
    type: 'radar',
    data: {
      labels: DC_DOMAINS.map(d => d.title),
      datasets: [{
        label: dcClientInfo.name,
        data: domainScores,
        backgroundColor: 'rgba(109,21,93,0.15)',
        borderColor: '#6D155D',
        borderWidth: 2,
        pointBackgroundColor: '#6D155D',
        pointRadius: 4
      }, {
        label: 'Cible recommandée',
        data: [75,75,75,75,75,75],
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

function dcBuildRecoList() {
  const recos = dcGetRecos();
  const list = document.getElementById('dc-reco-list');
  list.innerHTML = '';
  if (recos.length === 0) {
    list.innerHTML = '<div style="color:#1a7a4a;font-size:13px;padding:12px">Excellent niveau de maturité — aucune action critique identifiée.</div>';
    return;
  }
  recos.forEach(r => {
    const cls = r.prio === 'Priorité critique' ? 'dc-reco-critical' : 'dc-reco-important';
    const clr = r.prio === 'Priorité critique' ? '#c0392b' : '#d35400';
    list.innerHTML += `<div class="dc-reco-item ${cls}">
      <div>
        <div class="dc-reco-prio" style="color:${clr}">${r.prio} · ${r.domain}</div>
        <div class="dc-reco-text"><strong style="color:#1a1018">Situation :</strong> ${r.action}</div>
        <div class="dc-reco-text" style="margin-top:3px"><strong style="color:#1a1018">Prochaine étape :</strong> ${r.next}</div>
      </div>
    </div>`;
  });
}

// ── Gate ──────────────────────────────────────────────────────────────────────
function dcRevealForm() {
  document.getElementById('dcGateCta').style.display = 'none';
  document.getElementById('dcGateForm').style.display = 'block';
}

function dcUnlockContent() {
  const overlay = document.getElementById('dcGateOverlay');
  document.getElementById('dcGateForm').style.display = 'none';
  document.getElementById('dcGateConfirm').style.display = 'block';
  setTimeout(() => {
    overlay.style.transition = 'opacity 0.7s ease';
    overlay.style.opacity = '0';
    setTimeout(() => {
      overlay.style.display = 'none';
      const content = document.getElementById('dcRecoContent');
      if (content) content.classList.remove('dc-blurred');
    }, 700);
  }, 2000);
}

// ── Génération PDF ────────────────────────────────────────────────────────────
function hexToRgbDC(hex) {
  return [parseInt(hex.slice(1,3),16), parseInt(hex.slice(3,5),16), parseInt(hex.slice(5,7),16)];
}

function dcGeneratePDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  const domainScores = DC_DOMAINS.map((_, di) => dcCalcDomainScore(di));
  const globalScore = Math.round(domainScores.reduce((a,b) => a+b, 0) / domainScores.length);
  const col = dcGetColor(globalScore);

  let y = 20;

  // Titre
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(18);
  doc.setTextColor(109, 21, 93);
  doc.text('Diagnostic Maturite Cybersecurite', 20, y);
  y += 8;
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(10);
  doc.setTextColor(138, 112, 133);
  doc.text("Galyn'Up - CIO Advisory | NIST CSF - ISO 27001", 20, y);
  y += 10;
  doc.setDrawColor(211, 166, 37);
  doc.setLineWidth(0.8);
  doc.line(20, y, 190, y);
  y += 10;

  // Infos client
  doc.setFontSize(10);
  doc.setTextColor(26, 16, 24);
  [['Client', dcClientInfo.name || '-'],
   ['Secteur', dcClientInfo.sector || '-'],
   ['Interlocuteur', dcClientInfo.contact || '-'],
   ['Date', dcClientInfo.date || '-']
  ].forEach(([lbl, val]) => {
    doc.setFont('helvetica', 'bold'); doc.text(lbl + ' :', 20, y);
    doc.setFont('helvetica', 'normal'); doc.text(val, 60, y);
    y += 6;
  });
  y += 4;

  // Score global
  doc.setFillColor(245, 237, 243);
  doc.roundedRect(20, y, 170, 14, 2, 2, 'F');
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(12);
  doc.setTextColor(109, 21, 93);
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

  DC_DOMAINS.forEach((d, di) => {
    const sc = domainScores[di];
    const c = dcGetColor(sc);
    const [r,g,b] = hexToRgbDC(c.bar);
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
  if (y > 240) { doc.addPage(); y = 20; }
  doc.setDrawColor(232, 218, 229);
  doc.line(20, y, 190, y);
  y += 8;
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(12);
  doc.setTextColor(26, 16, 24);
  doc.text('Plan de recommandations', 20, y);
  y += 8;

  const recos = dcGetRecos();
  recos.forEach((r, i) => {
    if (y > 265) { doc.addPage(); y = 20; }
    const [r2,g2,b2] = hexToRgbDC(r.prio === 'Priorité critique' ? '#c0392b' : '#d35400');
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

function dcGenerateCSVBase64() {
  let csv = '\uFEFF'; // BOM Excel
  csv += 'Domaine,Question,Reponse selectionnee,Score (0-3)\n';
  DC_DOMAINS.forEach((d, di) => {
    d.questions.forEach((q, qi) => {
      const k = di + '_' + qi;
      const ans = dcAnswers[k] !== undefined ? dcAnswers[k] : '';
      const opt = ans !== '' ? q.opts[ans] : '';
      csv += '"' + d.title + '","' + q.text.replace(/"/g,'""') + '","' + opt.replace(/"/g,'""') + '",' + ans + '\n';
    });
  });
  return btoa(unescape(encodeURIComponent(csv)));
}

// ── Envoi formulaire ──────────────────────────────────────────────────────────
function dcSendContact() {
  const name    = document.getElementById('dcContactName').value.trim();
  const email   = document.getElementById('dcContactEmail').value.trim();
  const message = document.getElementById('dcContactMessage').value.trim();
  const msgEl   = document.getElementById('dcGateMsg');
  const btn     = document.getElementById('dcSendBtn');

  if (!name || !email) {
    msgEl.textContent = 'Merci de renseigner votre nom et votre email.';
    msgEl.className = 'dc-gate-msg error';
    msgEl.style.display = 'block';
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Envoi en cours…';
  msgEl.style.display = 'none';

  const domainScores = DC_DOMAINS.map((d, di) => ({ domain: d.title, score: dcCalcDomainScore(di) }));
  const globalScore  = Math.round(domainScores.reduce((a,b) => a + b.score, 0) / domainScores.length);
  const recos        = dcGetRecos();

  const diagData = JSON.stringify({ client: dcClientInfo, domainScores, globalScore, recos });

  let pdfB64 = '', csvB64 = '';
  try { pdfB64 = dcGeneratePDF(); } catch(e) { console.warn('PDF generation failed', e); }
  try { csvB64 = dcGenerateCSVBase64(); } catch(e) { console.warn('CSV generation failed', e); }

  const formData = new FormData();
  formData.append('name', name);
  formData.append('email', email);
  formData.append('message', message);
  formData.append('diag_data', diagData);
  formData.append('pdf_data', pdfB64);
  formData.append('csv_data', csvB64);

  fetch('php/send-diag-cyber.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        dcUnlockContent();
      } else {
        btn.disabled = false;
        btn.textContent = 'Envoyer →';
        msgEl.textContent = 'Erreur lors de l\'envoi. Veuillez réessayer.';
        msgEl.className = 'dc-gate-msg error';
        msgEl.style.display = 'block';
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.textContent = 'Envoyer →';
      msgEl.textContent = 'Erreur réseau. Veuillez réessayer.';
      msgEl.className = 'dc-gate-msg error';
      msgEl.style.display = 'block';
    });
}

function dcResetDiag() {
  dcAnswers = {}; dcClientInfo = {}; dcCurrentDomain = 0;
  document.getElementById('dc-results-section').style.display = 'none';
  document.getElementById('dc-intro-section').classList.remove('dc-hidden');
  document.getElementById('dc-nav-bar').style.display = 'none';
  document.getElementById('dcProgressFill').style.width = '0%';
  document.getElementById('dcProgressPct').textContent = '0%';
  // Reset gate
  const overlay = document.getElementById('dcGateOverlay');
  overlay.style.display = 'flex';
  overlay.style.opacity = '1';
  const content = document.getElementById('dcRecoContent');
  if (content) content.classList.add('dc-blurred');
  document.getElementById('dcGateCta').style.display = 'block';
  document.getElementById('dcGateForm').style.display = 'none';
  document.getElementById('dcGateConfirm').style.display = 'none';
}

document.getElementById('dcDiagDate').valueAsDate = new Date();
  </script>
</body>
</html>
