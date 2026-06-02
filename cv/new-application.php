<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
requireLogin();

$db  = getDB();
$app = null;
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $db->prepare("SELECT * FROM cv_applications WHERE id = ?");
    $stmt->execute([$id]);
    $app = $stmt->fetch();
    if (!$app) { header('Location: dashboard.php'); exit; }
}

$step = $app ? (int)$app['step_current'] : 1;

// ── Construire le bloc "Base de connaissance" pour les prompts ─────────
function buildKnowledgeBlock(PDO $db): string {
    $entries = $db->query("SELECT type, title, content, meta_json FROM cv_knowledge WHERE is_active = 1 ORDER BY type, IFNULL(period_start, 0) DESC, created_at DESC")->fetchAll();
    if (empty($entries)) return "(Base de connaissance vide — ajoute des entrées dans la section 'Base de connaissance')";
    $text = '';
    foreach ($entries as $e) {
        $meta = $e['meta_json'] ? json_decode($e['meta_json'], true) : [];
        $header = strtoupper($e['type']);
        if (!empty($e['title']))   $header .= ' — ' . $e['title'];
        if (!empty($meta['company'])) $header .= ' | ' . $meta['company'];
        if (!empty($meta['role']))    $header .= ' · ' . $meta['role'];
        if (!empty($meta['period']))  $header .= ' (' . $meta['period'] . ')';
        $text .= "[$header]\n" . $e['content'] . "\n\n";
    }
    return trim($text);
}

// ── Prompts ────────────────────────────────────────────────────────────
function buildAnalysisPrompt(string $jobPosting): string {
    return <<<PROMPT
Analyse cette fiche de poste et retourne UNIQUEMENT un JSON valide avec exactement cette structure :

{
  "entreprise": "nom de l'entreprise si mentionné",
  "poste": "intitulé du poste",
  "competences_cles": ["compétence absolument requise 1", "compétence 2"],
  "experience_requise": ["exigence d'expérience 1", "exigence 2"],
  "mots_cles_sectoriels": ["mot-clé 1", "mot-clé 2"],
  "profil_type": "description du profil idéal en 2-3 phrases",
  "dealbreakers": ["condition sine qua non 1", "condition 2"],
  "points_valorises": ["ce que le recruteur valorise particulièrement 1", "point 2"]
}

Ne retourne que le JSON, sans explication ni texte avant ou après.

FICHE DE POSTE :
$jobPosting
PROMPT;
}

function buildMatchingPrompt(string $knowledge, string $analysisJson, string $jobPosting): string {
    return <<<PROMPT
Tu es expert en recrutement. Compare le profil d'un candidat avec une fiche de poste analysée.

Retourne UNIQUEMENT un JSON valide avec exactement cette structure :

{
  "resume_matching": "évaluation globale en 2-3 phrases",
  "correspondances": [
    {
      "competence": "compétence requise par le poste",
      "trouve_dans_profil": "ce qui correspond dans le profil du candidat (ou 'Non trouvé')",
      "force": "fort|moyen|faible|absent"
    }
  ],
  "points_forts": ["point fort 1 du candidat pour ce poste", "point fort 2"],
  "lacunes": ["lacune ou point à renforcer 1", "lacune 2"],
}

Ne retourne que le JSON, sans texte avant ou après.

---
PROFIL DU CANDIDAT :
$knowledge

---
ANALYSE DU POSTE (JSON) :
$analysisJson

---
FICHE DE POSTE ORIGINALE :
$jobPosting
PROMPT;
}

function buildCVPrompt(string $knowledge, string $analysisJson, string $matchingJson, string $jobPosting): string {
    return <<<PROMPT
Tu es expert en rédaction de CV pour des cadres dirigeants français. Génère un CV sur-mesure en français.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRUCTURE HTML OBLIGATOIRE — respecte exactement ces balises et classes CSS :
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

<section id="cv">

  <header class="cv-header">
    <h1>GAËLLE FAY</h1>
    <p class="cv-subtitle">[Titre adapté au poste ciblé — pas générique]</p>
    <p class="cv-tagline">[Domaine 1] · [Domaine 2] · [Domaine 3] | 25 ans d'expérience | [Secteur 1] · [Secteur 2]</p>
    <p class="cv-contact">+33 6 10 74 55 84 · gaelle.fay@outlook.fr · Noisy-le-Grand (93) · linkedin.com/in/gaellefay</p>
  </header>

  <section class="cv-section">
    <h3 class="cv-section-title">PROFIL</h3>
    <p>[Paragraphe 1 : synthèse du profil en lien direct avec ce poste précis — 2-3 phrases percutantes]</p>
    <p>[Paragraphe 2 optionnel : angle complémentaire, spécialité technique, certifications clés]</p>
  </section>

  <section class="cv-section">
    <h3 class="cv-section-title">CERTIFICATIONS PROFESSIONNELLES</h3>
    <ul class="cv-list-2col">
      <li>PMP, Project Management Professional, PMI</li>
      <li>ITIL 4 Foundation, Axelos</li>
      <li>ACP, Agile Certified Practitioner, PMI</li>
      <li>Stratégie@HEC, HEC Paris</li>
      <li>TOGAF 10 Foundation, The Open Group</li>
      <li>Agile Scrum Foundation (ASF), EXIN</li>
    </ul>
  </section>

  <section class="cv-section">
    <h3 class="cv-section-title">COMPÉTENCES CLÉS</h3>
    <ul class="cv-list-2col">
      <li>[6 à 8 compétences — prioriser les mots-clés exacts de la fiche de poste]</li>
    </ul>
  </section>

  <section class="cv-section">
    <h3 class="cv-section-title">EXPÉRIENCE PROFESSIONNELLE</h3>

    <div class="cv-job">
      <div class="cv-job-header">
        <span class="cv-job-title"><strong>[Intitulé du Poste]</strong> · [Nom Entreprise]</span>
        <span class="cv-job-date">[Mois AAAA – Mois AAAA]</span>
      </div>
      <p class="cv-job-context"><em>[Contexte : taille équipe, périmètre, programme, type de structure]</em></p>
      <ul class="cv-job-bullets">
        <li><strong>[Mot-clé] :</strong> [Réalisation concrète, chiffrée si possible]</li>
        <li><strong>[Mot-clé] :</strong> [Réalisation]</li>
      </ul>
      <!-- Si note contextuelle (fin de mission, raison de départ) : -->
      <!-- <p class="cv-job-note"><em>[Note]</em></p> -->
    </div>

    [Répéter div.cv-job pour chaque poste, du plus récent au plus ancien]

  </section>

  <section class="cv-section">
    <h3 class="cv-section-title">FORMATION</h3>
    <p><strong>Master 2 Management des Systèmes d'Information et de la Connaissance</strong></p>
    <p>IAE Sorbonne (Paris), 2008. Stratégie SI, architecture d'entreprise, transformation numérique.</p>
  </section>

  <section class="cv-section">
    <h3 class="cv-section-title">LEADERSHIP & RAYONNEMENT PROFESSIONNEL</h3>
    <p>[Activités de leadership, publications, interventions, bénévolat professionnel — extraits du profil]</p>
  </section>

  <section class="cv-section">
    <h3 class="cv-section-title">LANGUES</h3>
    <p>Français langue maternelle · Anglais C1 courant · Espagnol notions · Recommandations sur galynup.fr</p>
  </section>

</section>

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
RÈGLES IMPÉRATIVES — STRUCTURE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
- Commence DIRECTEMENT par <section id="cv">, sans aucun texte avant ni après
- Termine OBLIGATOIREMENT par </section> (fermeture de <section id="cv">)
- N'inclus AUCUN CSS, AUCUN <style>, AUCUN attribut style="" sur aucune balise
- N'utilise QUE les balises et classes du template ci-dessus — aucune balise supplémentaire

RÈGLES IMPÉRATIVES — CONTENU FIGÉ (copie mot pour mot, sans aucune modification)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
- La section CERTIFICATIONS : les 6 <li> sont du TEXTE BRUT, AUCUNE balise à l'intérieur des <li>
- La section FORMATION : texte fixe, Master 2 IAE Sorbonne 2008
- Le contact (cv-contact) : +33 6 10 74 55 84 · gaelle.fay@outlook.fr · Noisy-le-Grand (93) · linkedin.com/in/gaellefay
- La section LANGUES : texte fixe

RÈGLES IMPÉRATIVES — FORMATAGE
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
- cv-list-2col (CERTIFICATIONS et COMPÉTENCES) : les <li> contiennent UNIQUEMENT du texte brut — ZÉRO balise HTML à l'intérieur
- cv-job-bullets : chaque <li> commence OBLIGATOIREMENT par <strong>Mot-clé :</strong> puis texte brut
- cv-job-context : contenu en texte brut dans <em>…</em>, aucune autre balise
- Le cv-subtitle DOIT être adapté au poste ciblé (pas générique)
- Les COMPÉTENCES CLÉS : 6 à 8 items texte brut — inclure les mots-clés exacts de la fiche de poste
- Expériences récentes (< 5 ans) : 4 à 5 puces | expériences > 10 ans : 2 puces max
- Intégrer toutes les réalisations mentionnées dans le dialogue et les expériences complémentaires
- Ne jamais inventer ni exagérer — respecter strictement la réalité du parcours
- Longueur totale : 1 à 2 pages imprimées

---
PROFIL DU CANDIDAT :
$knowledge

---
ANALYSE DU POSTE :
$analysisJson

---
MATCHING ET STRATÉGIE :
$matchingJson

---
FICHE DE POSTE :
$jobPosting
PROMPT;
}

function buildMessagesPrompt(string $company, string $position, string $jobPosting, string $analysisJson, string $knowledge): string {
    return <<<PROMPT
Tu es expert en rédaction de contenus de candidature pour des cadres dirigeants français.

À partir des informations ci-dessous, génère SIMULTANÉMENT les 3 formats suivants.
Retourne EXACTEMENT cette structure, sans aucun texte avant ni après, sans commentaire :

###LETTRE###
[HTML de la lettre — structure <section id="letter">...</section> à respecter exactement]
###/LETTRE###

###LINKEDIN###
[Texte de candidature LinkedIn — 400 caractères MAXIMUM, sans HTML, accroche directe dès la première phrase, sans formule de politesse d'introduction]
###/LINKEDIN###

###MALT###
[Présentation Malt — commence OBLIGATOIREMENT par "Bonjour, Je suis [métier], spécialisée en [domaine d'activité]. J'aimerais vous accompagner sur ce projet" — développe ensuite en 4 à 6 paragraphes : parcours et expertise, valeur ajoutée concrète pour ce projet, exemples de missions similaires tirés de la base de connaissance, mode de travail et disponibilité — ton professionnel, direct et humain, sans HTML, 800 à 1200 mots]
###/MALT###

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
STRUCTURE HTML OBLIGATOIRE POUR ###LETTRE### :
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

<section id="letter">
  <div class="letter-meta">
    <div class="letter-sender">Gaëlle FAY<br>Noisy-le-Grand | gaelle.fay@outlook.fr | +33 6 10 74 55 84</div>
    <div class="letter-date">[Ville], le [date du jour]</div>
  </div>
  <div class="letter-recipient">
    À l'attention du Service des Ressources Humaines<br>
    $company
  </div>
  <div class="letter-object">Objet : Candidature au poste de $position</div>
  <div class="letter-body">
    <p>[Paragraphe 1 : accroche — pourquoi ce poste, pourquoi cette entreprise]</p>
    <p>[Paragraphe 2 : parcours et compétences clés en lien direct avec l'annonce]</p>
    <p>[Paragraphe 3 : valeur ajoutée concrète et différenciante]</p>
    <p>[Paragraphe 4 : conclusion, disponibilité, appel à l'action]</p>
  </div>
  <div class="letter-signature">
    <p>Gaëlle FAY</p>
  </div>
</section>

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
CONSIGNES COMMUNES :
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
- Ton professionnel, direct, sans formules creuses
- S'appuyer UNIQUEMENT sur la base de connaissance — ne pas inventer de faits
- Chaque format doit être cohérent avec les autres mais adapté à son canal

---
ANALYSE DU POSTE :
$analysisJson

---
BASE DE CONNAISSANCE :
$knowledge

---
FICHE DE POSTE :
$jobPosting
PROMPT;
}

// ── Données pour les étapes actives ───────────────────────────────────
$knowledge     = buildKnowledgeBlock($db);
$analysisJson  = $app['analysis_result'] ?? '';
$matchingJson  = $app['matching_result'] ?? '';
$cvContent     = $app['cv_content'] ?? '';
$letterContent = $app['letter_content'] ?? '';
$lettersData   = json_decode($app['letters_json'] ?? 'null', true);

$stepLabels = ['Fiche de poste', 'Analyse', 'Matching & Enrichissement', 'Génération CV', 'Export'];

$pageTitle  = $app ? 'Candidature — ' . htmlspecialchars($app['company']) : 'Nouvelle candidature';
$activePage = 'new';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Barre de progression -->
<div class="steps-bar">
  <?php for ($i = 1; $i <= 5; $i++): ?>
    <div class="step <?= $i < $step ? 'done' : ($i === $step ? 'active' : '') ?>">
      <div class="step-circle"><?= $i < $step ? '✓' : $i ?></div>
      <span class="step-label"><?= $stepLabels[$i-1] ?></span>
    </div>
  <?php endfor; ?>
</div>

<?php /* ═══════════════════════════════════════════════════
   STEP 1 — Infos + Fiche de poste
   ═══════════════════════════════════════════════════ */
if ($step === 1): ?>
<div class="card">
  <div class="card-title">📋 Fiche de poste</div>

  <?php if (empty(trim($knowledge)) || str_starts_with($knowledge, '(Base de connaissance vide')): ?>
    <div class="alert alert-warning">
      ⚠ Ta base de connaissance est vide. <a href="knowledge-base.php">Ajoute des entrées</a> avant de candidater.
    </div>
  <?php endif; ?>

  <form id="form-step1">
    <div class="form-group">
      <label>URL de l'offre <span class="text-muted" style="font-weight:normal;">(optionnel — LinkedIn, APEC, Indeed...)</span></label>
      <div class="flex flex-gap items-center">
        <input type="url" name="source_url" id="source-url" class="form-control"
               value="<?= htmlspecialchars($app['source_url'] ?? '') ?>"
               placeholder="https://www.linkedin.com/jobs/view/..."
               oninput="updatePlatformBadge(this.value)">
        <span id="platform-badge" class="badge" style="flex-shrink:0; <?= empty($app['source_url']) ? 'display:none' : '' ?>">
          <?= !empty($app['source_url']) ? htmlspecialchars(detectPlatform($app['source_url'])) : '' ?>
        </span>
        <button type="button" class="btn btn-gold btn-sm" id="btn-fetch" onclick="fetchJobPosting()" style="flex-shrink:0; white-space:nowrap;">
          ↓ Récupérer
        </button>
      </div>
      <div id="fetch-msg" class="hidden alert" style="margin-top:8px;"></div>
    </div>
    <div class="grid-2">
      <div class="form-group">
        <label>Entreprise</label>
        <input type="text" name="company" id="field-company" class="form-control" required
               value="<?= htmlspecialchars($app['company'] ?? '') ?>"
               placeholder="Nom de l'entreprise">
      </div>
      <div class="form-group">
        <label>Poste visé</label>
        <input type="text" name="position" id="field-position" class="form-control" required
               value="<?= htmlspecialchars($app['position'] ?? '') ?>"
               placeholder="Intitulé du poste">
      </div>
    </div>
    <div class="form-group">
      <label>Fiche de poste complète</label>
      <textarea name="job_posting" id="field-job-posting" class="form-control" rows="16" required
                placeholder="Colle ici la fiche de poste intégrale, ou utilise le bouton ↑ Récupérer..."><?= htmlspecialchars($app['job_posting'] ?? '') ?></textarea>
    </div>
    <div class="flex flex-gap items-center">
      <button type="submit" class="btn btn-primary btn-lg">Générer le prompt d'analyse →</button>
      <span id="step1-status" class="text-muted hidden"></span>
    </div>
    <?php if ($id > 0): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>
  </form>
</div>

<?php /* ═══════════════════════════════════════════════════
   STEP 2 — Analyse
   ═══════════════════════════════════════════════════ */
elseif ($step === 2):
    $prompt = buildAnalysisPrompt($app['job_posting']);
?>
<div class="card">
  <div class="card-title">🔍 Étape 2 — Analyse de la fiche de poste</div>

  <div class="alert alert-info">
    Copie le prompt ci-dessous, colle-le dans Claude.ai, puis colle la réponse dans la zone verte.
  </div>

  <div class="prompt-block">
    <div class="prompt-block-header">
      <span class="prompt-block-title">Prompt à copier dans Claude.ai</span>
      <button class="btn btn-gold btn-sm" onclick="copyPrompt('prompt-analysis')">📋 Copier</button>
    </div>
    <div class="prompt-text" id="prompt-analysis"><?= htmlspecialchars($prompt) ?></div>
  </div>

  <div class="response-block">
    <div class="response-block-title">↳ Coller la réponse de Claude ici</div>
    <textarea id="analysis-response" class="form-control" rows="10"
              placeholder='Colle ici le JSON retourné par Claude...&#10;&#10;Exemple :&#10;{&#10;  "entreprise": "Estreem",&#10;  "poste": "Directeur de Programme",&#10;  ...&#10;}'></textarea>
    <div class="flex flex-gap mt-16">
      <button class="btn btn-primary" onclick="saveStep(2)">Enregistrer et continuer →</button>
      <a href="new-application.php?id=<?= $id ?>&back=1" class="btn btn-ghost">← Retour</a>
    </div>
    <div id="step2-msg" class="hidden alert" style="margin-top:12px;"></div>
  </div>
</div>

<?php /* ═══════════════════════════════════════════════════
   STEP 3 — Matching
   ═══════════════════════════════════════════════════ */
elseif ($step === 3 && ($app['status'] ?? '') === 'matching'):
    $prompt = buildMatchingPrompt($knowledge, $analysisJson, $app['job_posting']);
?>
<div class="card">
  <div class="card-title">🎯 Étape 3 — Matching & Enrichissement</div>

  <div class="alert alert-info">
    Ce prompt inclut ta base de connaissance. Claude va identifier les correspondances avec le poste.
    L'enrichissement de la base se fera ensuite compétence par compétence.
  </div>

  <div class="prompt-block">
    <div class="prompt-block-header">
      <span class="prompt-block-title">Prompt à copier dans Claude.ai</span>
      <button class="btn btn-gold btn-sm" onclick="copyPrompt('prompt-matching')">📋 Copier</button>
    </div>
    <div class="prompt-text" id="prompt-matching"><?= htmlspecialchars($prompt) ?></div>
  </div>

  <div class="response-block" id="phase-paste">
    <div class="response-block-title">↳ Coller la réponse de Claude ici</div>
    <textarea id="matching-response" class="form-control" rows="10"
              placeholder='Colle ici le JSON retourné par Claude...'></textarea>
    <div class="flex flex-gap mt-16">
      <button class="btn btn-primary" id="btn-parse-matching" onclick="parseMatchingResponse()">Analyser le matching →</button>
      <a href="new-application.php?id=<?= $id ?>&back=1" class="btn btn-ghost">← Retour</a>
    </div>
    <div id="step3-parse-msg" class="hidden alert" style="margin-top:12px;"></div>
  </div>

  <div id="phase-review" style="display:none;">
    <div id="matching-display" style="margin-top:24px;"></div>
    <div class="flex flex-gap mt-16">
      <button class="btn btn-primary btn-lg" onclick="saveStep(3)">Enregistrer et passer à l'enrichissement →</button>
    </div>
    <div id="step3-msg" class="hidden alert" style="margin-top:12px;"></div>
  </div>
</div>

<?php /* ═══════════════════════════════════════════════════
   STEP 3 (phase B) — Enrichissement de la base
   ═══════════════════════════════════════════════════ */
elseif ($step === 3):
    $enrichmentData = json_decode($app['enrichment_data'] ?? '[]', true) ?? [];
    $totalComps     = count($enrichmentData);
    $currentComp    = null;
    $currentIndex   = -1;
    $doneCount      = 0;
    foreach ($enrichmentData as $i => $comp) {
        if ($comp['status'] !== 'pending') { $doneCount++; continue; }
        if ($currentComp === null) { $currentComp = $comp; $currentIndex = $i; }
    }
    $matchingData = json_decode($matchingJson, true) ?? [];
    $experiences  = $db->query("SELECT id, title, meta_json FROM cv_knowledge WHERE type = 'experience' AND is_active = 1 ORDER BY IFNULL(period_start, 0) DESC, created_at DESC")->fetchAll();
?>
<div class="card">
  <div class="card-title">🎯 Étape 3 — Matching & Enrichissement</div>

  <!-- Résumé matching (repliable) -->
  <details style="margin-bottom:20px;">
    <summary style="font-size:13px; font-weight:600; color:#6D155D; cursor:pointer; padding:8px 0; list-style:none; display:flex; align-items:center; gap:8px;">
      <span>▶</span> Résumé du matching (<?= count($matchingData['correspondances'] ?? []) ?> compétences analysées)
    </summary>
    <div style="margin-top:12px; padding-top:12px; border-top:1px solid var(--border);">
      <?php if (!empty($matchingData['resume_matching'])): ?>
        <div style="background:#f0eaf8;border-left:4px solid #6D155D;padding:12px 16px;border-radius:6px;font-size:13px;color:#333;margin-bottom:14px;">
          <?= htmlspecialchars($matchingData['resume_matching']) ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($matchingData['points_forts'])): ?>
        <div style="margin-bottom:10px;">
          <div style="font-size:11px;font-weight:700;color:#2a7d4b;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">Points forts</div>
          <?php foreach ($matchingData['points_forts'] as $p): ?>
            <div style="font-size:13px;padding:5px 10px;margin-bottom:3px;background:#f0faf4;border-radius:5px;border-left:3px solid #2a7d4b;">✓ <?= htmlspecialchars($p) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($matchingData['lacunes'])): ?>
        <div>
          <div style="font-size:11px;font-weight:700;color:#c0392b;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;">À renforcer</div>
          <?php foreach ($matchingData['lacunes'] as $l): ?>
            <div style="font-size:13px;padding:5px 10px;margin-bottom:3px;background:#fdf2f2;border-radius:5px;border-left:3px solid #c0392b;">⚠ <?= htmlspecialchars($l) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </details>

  <!-- Barre de progression enrichissement -->
  <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#f9f9f7;border-radius:8px;border:1px solid var(--border);margin-bottom:20px;">
    <div style="font-size:13px;color:var(--text-muted);">Enrichissement :</div>
    <div style="font-size:15px;font-weight:700;color:#6D155D;"><?= $doneCount ?>/<?= $totalComps ?></div>
    <div style="flex:1;height:6px;background:var(--border);border-radius:3px;overflow:hidden;">
      <div style="height:100%;background:#6D155D;width:<?= $totalComps > 0 ? round($doneCount/$totalComps*100) : 0 ?>%;border-radius:3px;"></div>
    </div>
  </div>

  <!-- Tags compétences -->
  <div style="margin-bottom:24px;">
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);margin-bottom:8px;">Compétences requises</div>
    <div style="display:flex;flex-wrap:wrap;gap:6px;">
      <?php foreach ($enrichmentData as $i => $comp):
        $st = $comp['status'];
        if ($st === 'enriched')     $style = 'background:#e8f8ee;color:#2a7d4b;';
        elseif ($st === 'skipped')  $style = 'background:#f0f0f0;color:#999;text-decoration:line-through;';
        elseif ($comp['found'])     $style = 'background:#e8f4fd;color:#1565c0;';
        else                        $style = 'background:#fff4e0;color:#b36b00;';
        $icon = ($st === 'enriched') ? '✓ ' : (($st === 'skipped') ? '— ' : ($comp['found'] ? '● ' : '○ '));
        $outline = ($i === $currentIndex) ? 'outline:2px solid #D3A625;outline-offset:1px;' : '';
      ?>
        <span style="<?= $style . $outline ?> font-size:12px;padding:3px 10px;border-radius:20px;">
          <?= $icon . htmlspecialchars($comp['name']) ?>
        </span>
      <?php endforeach; ?>
    </div>
    <div style="font-size:11px;color:#bbb;margin-top:6px;">● dans la base · ○ à enrichir · ✓ enrichie · — passée</div>
  </div>

  <?php if ($currentComp === null): ?>
    <!-- Toutes traitées -->
    <div class="alert alert-success">✓ Toutes les compétences ont été traitées. La base de connaissance est à jour.</div>
    <div class="mt-16">
      <button class="btn btn-primary btn-lg" onclick="advanceToCV()">Générer le CV →</button>
    </div>
    <div id="advance-msg" class="hidden alert" style="margin-top:12px;"></div>

  <?php else: ?>
    <!-- Formulaire d'enrichissement pour la compétence courante -->
    <div style="border:2px solid #D3A625;border-radius:var(--radius);padding:20px;">
      <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#b36b00;margin-bottom:6px;">
        Compétence <?= $currentIndex + 1 ?> / <?= $totalComps ?>
      </div>
      <div style="font-size:18px;font-weight:700;color:var(--text);margin-bottom:10px;">
        <?= htmlspecialchars($currentComp['name']) ?>
      </div>

      <?php if ($currentComp['found'] && !empty($currentComp['matches'])): ?>
        <div style="font-size:13px;color:#1565c0;background:#e8f4fd;padding:8px 12px;border-radius:6px;margin-bottom:12px;">
          Déjà présente dans :
          <?= implode(', ', array_map(fn($m) => '<strong>' . htmlspecialchars($m['title']) . '</strong>', $currentComp['matches'])) ?>
        </div>
        <div style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">
          Tu peux enrichir une entrée avec un exemple concret, ou passer directement.
        </div>
      <?php else: ?>
        <div style="font-size:13px;color:#b36b00;background:#fff4e0;padding:8px 12px;border-radius:6px;margin-bottom:16px;">
          Non trouvée dans ta base de connaissance.
        </div>
      <?php endif; ?>

      <!-- ── Toggle mode enrichissement ── -->
      <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
        <button type="button" id="btn-mode-specific" onclick="setEnrichMode('specific')"
                style="padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;cursor:pointer;border:2px solid #6D155D;background:#6D155D;color:#fff;transition:.15s;">
          📌 Sur une expérience précise
        </button>
        <button type="button" id="btn-mode-transversal" onclick="setEnrichMode('transversal')"
                style="padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;cursor:pointer;border:2px solid #6D155D;background:#fff;color:#6D155D;transition:.15s;">
          ✦ Tout au long de mon parcours
        </button>
      </div>

      <!-- Mode : expérience précise -->
      <div id="mode-specific-content">
        <div class="form-group">
          <label style="font-size:13px;">Sur quel(s) poste(s) ? <span style="font-weight:400;color:#888;">clique pour sélectionner</span></label>
          <div id="experience-pills" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;align-items:center;">
            <?php if (!empty($currentComp['matches'])): ?>
              <span class="exp-pills-group-label">Correspondances</span>
              <?php foreach ($currentComp['matches'] as $m): ?>
                <button type="button" class="exp-pill selected" data-id="<?= $m['id'] ?>"
                        onclick="togglePill(this)">
                  <?= htmlspecialchars($m['title']) ?>
                </button>
              <?php endforeach; ?>
            <?php endif; ?>
            <?php
              $matchIds = array_column($currentComp['matches'] ?? [], 'id');
              $otherExp = array_filter($experiences, fn($e) => !in_array($e['id'], $matchIds));
              if (!empty($otherExp)):
            ?>
              <span class="exp-pills-group-label">Autres expériences</span>
              <?php foreach ($otherExp as $exp):
                $meta = json_decode($exp['meta_json'] ?? '{}', true);
                $label = htmlspecialchars($exp['title']);
                if (!empty($meta['company'])) $label .= ' · ' . htmlspecialchars($meta['company']);
              ?>
                <button type="button" class="exp-pill" data-id="<?= $exp['id'] ?>"
                        onclick="togglePill(this)">
                  <?= $label ?>
                </button>
              <?php endforeach; ?>
            <?php endif; ?>
            <span class="exp-pills-group-label" style="margin-top:4px;"></span>
            <button type="button" class="exp-pill exp-pill-new" data-id="new"
                    onclick="togglePill(this); toggleNewPostForm(this.classList.contains('selected'))">
              + Nouveau poste
            </button>
          </div>
        </div>

        <div id="new-post-form" style="display:none;">
          <div class="grid-2">
            <div class="form-group">
              <label style="font-size:13px;">Poste / titre</label>
              <input type="text" id="new-role" class="form-control" placeholder="Ex : DSI, Directeur de programme...">
            </div>
            <div class="form-group">
              <label style="font-size:13px;">Entreprise</label>
              <input type="text" id="new-company" class="form-control" placeholder="Nom de l'entreprise">
            </div>
          </div>
          <div class="form-group">
            <label style="font-size:13px;">Période</label>
            <input type="text" id="new-period" class="form-control" placeholder="Ex : 2020 – 2023">
          </div>
        </div>
      </div>

      <!-- Mode : compétence transversale -->
      <div id="mode-transversal-content" style="display:none;">
        <div style="background:#f0eaf8;border-left:3px solid #6D155D;padding:12px 16px;border-radius:6px;font-size:13px;color:#444;margin-bottom:4px;">
          Cette compétence sera enregistrée comme <strong>compétence transversale</strong> — acquise progressivement
          tout au long du parcours, non rattachée à une expérience unique. Claude pourra la valoriser librement
          dans le profil ou les compétences clés du CV.
        </div>
      </div>

      <!-- Description (commune aux deux modes) -->
      <div class="form-group" style="margin-top:16px;">
        <label id="enrich-desc-label" style="font-size:13px;">
          Décris ton expérience avec "<?= htmlspecialchars($currentComp['name']) ?>"
        </label>
        <textarea id="enrich-description" class="form-control" rows="4"
                  placeholder="Contexte, actions concrètes, résultats obtenus..."
                  oninput="updateEnrichPreview()"></textarea>
      </div>

      <div id="enrich-preview" style="display:none;font-size:13px;padding:10px 12px;background:#f0eaf8;border-radius:6px;border-left:3px solid #6D155D;margin-bottom:16px;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#6D155D;margin-bottom:4px;">Aperçu de l'ajout en base</div>
        <span id="enrich-preview-text"></span>
      </div>

      <div class="flex flex-gap mt-16">
        <button class="btn btn-primary" onclick="submitEnrichment(<?= $currentIndex ?>, <?= htmlspecialchars(json_encode($currentComp['name']), ENT_QUOTES) ?>)">
          Valider et enrichir →
        </button>
        <button class="btn btn-ghost" onclick="skipCompetency(<?= $currentIndex ?>)">
          Passer
        </button>
      </div>
      <div id="enrich-msg" class="hidden alert" style="margin-top:12px;"></div>
    </div>
  <?php endif; ?>
</div>

<?php /* ═══════════════════════════════════════════════════
   STEP 4 — Génération du CV
   ═══════════════════════════════════════════════════ */
elseif ($step === 4):
    $prompt = buildCVPrompt($knowledge, $analysisJson, $matchingJson, $app['job_posting']);
?>
<div class="card">
  <div class="card-title">✍ Étape 4 — Génération du CV</div>

  <div class="alert alert-info">
    La base de connaissance a été enrichie à l'étape précédente. Ce prompt contient tout le contexte.
    Claude va générer ton CV sur-mesure — colle le résultat ci-dessous.
  </div>

  <div class="prompt-block">
    <div class="prompt-block-header">
      <span class="prompt-block-title">Prompt à copier dans Claude.ai</span>
      <button class="btn btn-gold btn-sm" onclick="copyPrompt('prompt-cv')">📋 Copier</button>
    </div>
    <div class="prompt-text" id="prompt-cv"><?= htmlspecialchars($prompt) ?></div>
  </div>

  <div class="response-block">
    <div class="response-block-title">↳ Coller la réponse de Claude ici</div>
    <textarea id="cv-response" class="form-control" rows="14"
              placeholder="Colle ici la réponse complète de Claude (le HTML du CV)..."></textarea>
    <div class="flex flex-gap mt-16">
      <button class="btn btn-primary" onclick="previewCV()">Prévisualiser le CV →</button>
      <a href="new-application.php?id=<?= $id ?>&back=1" class="btn btn-ghost">← Retour</a>
    </div>
    <div id="step4-msg" class="hidden alert" style="margin-top:12px;"></div>
  </div>

  <div id="cv-preview-wrap" style="display:none; margin-top:28px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid var(--border);">
      <div style="font-size:14px; font-weight:700; color:#333;">Aperçu du CV</div>
      <div class="flex flex-gap">
        <button class="btn btn-ghost btn-sm" onclick="resetPreview()">← Modifier la réponse</button>
        <button class="btn btn-primary" onclick="confirmCV()">Confirmer et enregistrer →</button>
      </div>
    </div>
    <div id="cv-preview-area" class="cv-preview"></div>
    <div class="flex flex-gap" style="margin-top:16px; justify-content:flex-end;">
      <button class="btn btn-ghost btn-sm" onclick="resetPreview()">← Modifier la réponse</button>
      <button class="btn btn-primary" onclick="confirmCV()">Confirmer et enregistrer →</button>
    </div>
    <div id="step4-confirm-msg" class="hidden alert" style="margin-top:12px;"></div>
  </div>
</div>

<?php /* ═══════════════════════════════════════════════════
   STEP 5 — Export
   ═══════════════════════════════════════════════════ */
elseif ($step === 5):
  $messagesPrompt = buildMessagesPrompt($app['company'], $app['position'], $app['job_posting'], $analysisJson, $knowledge);
  $activeTab  = (($_GET['tab'] ?? 'cv') === 'ecrits') ? 'ecrits' : 'cv';
  $hasMessages = !empty($lettersData) || !empty($letterContent);
?>

<div class="alert alert-success">
  ✓ CV généré avec succès pour <strong><?= htmlspecialchars($app['company']) ?></strong> — <?= htmlspecialchars($app['position']) ?>
</div>

<!-- Barre d'onglets principales -->
<div class="tabs">
  <button class="tab-btn <?= $activeTab === 'cv' ? 'active' : '' ?>"
          onclick="switchTab('cv', this)">📄 CV</button>
  <button class="tab-btn <?= $activeTab === 'ecrits' ? 'active' : '' ?>"
          onclick="switchTab('ecrits', this)">✦ Écrits<?= !$hasMessages ? ' <span class="tab-badge">à générer</span>' : '' ?></button>
  <a href="new-application.php" class="btn btn-gold btn-sm"
     style="margin-left:auto; align-self:center;">+ Nouvelle</a>
</div>

<!-- Onglet CV -->
<div class="tab-content <?= $activeTab === 'cv' ? 'active' : '' ?>" id="tab-cv">
  <div class="flex flex-gap mb-16">
    <a href="php/export-word.php?id=<?= $id ?>" class="btn btn-primary">⬇ Télécharger Word (.doc)</a>
    <a href="php/export-pdf-server.php?id=<?= $id ?>" class="btn btn-outline">⬇ Télécharger PDF</a>
    <button onclick="window.print()" class="btn btn-outline">🖨 Imprimer</button>
  </div>
  <div class="cv-preview" id="cv-output"><?= $cvContent ?></div>
</div>

<!-- Onglet Écrits -->
<div class="tab-content <?= $activeTab === 'ecrits' ? 'active' : '' ?>" id="tab-ecrits">

  <?php if (!$hasMessages): ?>
  <!-- ── Génération initiale ── -->
  <div class="alert alert-info" style="margin-bottom:16px;">
    Un seul prompt génère les 3 formats en une fois. Copie dans Claude.ai, colle la réponse complète ci-dessous.
  </div>
  <div class="prompt-block">
    <div class="prompt-block-header">
      <span class="prompt-block-title">Prompt à copier dans Claude.ai</span>
      <button class="btn btn-gold btn-sm" onclick="copyPrompt('prompt-messages')">📋 Copier</button>
    </div>
    <div class="prompt-text" id="prompt-messages"><?= htmlspecialchars($messagesPrompt) ?></div>
  </div>
  <div class="response-block" id="messages-response-block" style="margin-top:16px;">
    <div class="response-block-title">↳ Coller la réponse complète de Claude ici</div>
    <textarea id="messages-response" class="form-control" rows="14"
              placeholder="Colle ici la réponse complète avec les 3 formats..."></textarea>
    <div class="flex flex-gap mt-16">
      <button class="btn btn-primary" onclick="previewMessages()">Prévisualiser les 3 formats →</button>
    </div>
    <div id="messages-msg" class="hidden alert" style="margin-top:12px;"></div>
  </div>
  <!-- Aperçu avant sauvegarde -->
  <div id="messages-preview-wrap" style="display:none; margin-top:24px;">
    <div class="sub-tabs-container">
      <div class="sub-tabs">
        <button class="sub-tab-btn active" onclick="switchSubTab('prev-lettre', this)">✦ Lettre</button>
        <button class="sub-tab-btn" onclick="switchSubTab('prev-linkedin', this)">in LinkedIn</button>
        <button class="sub-tab-btn" onclick="switchSubTab('prev-malt', this)">◎ Malt</button>
      </div>
      <div class="sub-tab-content active" id="sub-prev-lettre">
        <div id="prev-lettre-content" class="letter-preview"></div>
      </div>
      <div class="sub-tab-content" id="sub-prev-linkedin">
        <div id="prev-linkedin-counter" class="linkedin-counter" style="margin-bottom:10px;"></div>
        <div id="prev-linkedin-content" class="card" style="font-size:14px;line-height:1.8;white-space:pre-wrap;"></div>
      </div>
      <div class="sub-tab-content" id="sub-prev-malt">
        <div id="prev-malt-content" class="card" style="font-size:14px;line-height:1.8;white-space:pre-wrap;"></div>
      </div>
    </div>
    <div class="flex flex-gap mt-16">
      <button class="btn btn-primary" onclick="saveMessages()">Enregistrer les 3 formats →</button>
      <button class="btn btn-ghost" onclick="document.getElementById('messages-preview-wrap').style.display='none';document.getElementById('messages-response-block').style.display='block';">← Modifier</button>
    </div>
    <div id="messages-save-msg" class="hidden alert" style="margin-top:12px;"></div>
  </div>

  <?php else: ?>
  <!-- ── Contenu sauvegardé — sous-onglets ── -->
  <div class="sub-tabs-container">
    <div class="sub-tabs">
      <button class="sub-tab-btn active" onclick="switchSubTab('lettre', this)">✦ Lettre</button>
      <button class="sub-tab-btn" onclick="switchSubTab('linkedin', this)">in LinkedIn</button>
      <button class="sub-tab-btn" onclick="switchSubTab('malt', this)">◎ Malt</button>
    </div>

    <!-- Lettre traditionnelle -->
    <div class="sub-tab-content active" id="sub-lettre">
      <?php if (!empty($letterContent)): ?>
        <div class="flex flex-gap mb-16" style="flex-wrap:wrap;">
          <a href="php/export-letter-pdf.php?id=<?= $id ?>" class="btn btn-outline">⬇ PDF</a>
          <a href="php/export-letter-word.php?id=<?= $id ?>" class="btn btn-outline">⬇ Word</a>
        </div>
        <div class="letter-preview"><?= $letterContent ?></div>
      <?php else: ?>
        <div class="alert alert-warning">Lettre non générée — régénère les écrits ci-dessous.</div>
      <?php endif; ?>
    </div>

    <!-- LinkedIn -->
    <div class="sub-tab-content" id="sub-linkedin">
      <?php if (!empty($lettersData['linkedin'])): ?>
        <?php $llen = mb_strlen($lettersData['linkedin']); ?>
        <div class="flex items-center flex-gap mb-16">
          <span class="linkedin-counter <?= $llen > 400 ? 'over' : '' ?>"><?= $llen ?> / 400 caractères</span>
          <button class="btn btn-gold btn-sm" onclick="copyText('linkedin-text')">📋 Copier</button>
        </div>
        <div id="linkedin-text" class="card" style="font-size:14px;line-height:1.8;white-space:pre-wrap;"><?= htmlspecialchars($lettersData['linkedin']) ?></div>
      <?php else: ?>
        <div class="alert alert-warning">Texte LinkedIn non généré — régénère les écrits ci-dessous.</div>
      <?php endif; ?>
    </div>

    <!-- Malt -->
    <div class="sub-tab-content" id="sub-malt">
      <?php if (!empty($lettersData['malt'])): ?>
        <div class="flex flex-gap mb-16">
          <button class="btn btn-gold btn-sm" onclick="copyText('malt-text')">📋 Copier</button>
        </div>
        <div id="malt-text" class="card" style="font-size:14px;line-height:1.8;white-space:pre-wrap;"><?= htmlspecialchars($lettersData['malt']) ?></div>
      <?php else: ?>
        <div class="alert alert-warning">Texte Malt non généré — régénère les écrits ci-dessous.</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Regénérer tous les formats -->
  <div style="margin-top:28px;padding-top:20px;border-top:1px solid var(--border);">
    <button class="btn btn-ghost btn-sm"
            onclick="this.style.display='none';document.getElementById('regen-messages').style.display='block'">
      ↺ Regénérer les 3 formats
    </button>
    <div id="regen-messages" style="display:none;margin-top:16px;">
      <div class="prompt-block">
        <div class="prompt-block-header">
          <span class="prompt-block-title">Prompt à copier dans Claude.ai</span>
          <button class="btn btn-gold btn-sm" onclick="copyPrompt('prompt-messages-regen')">📋 Copier</button>
        </div>
        <div class="prompt-text" id="prompt-messages-regen"><?= htmlspecialchars($messagesPrompt) ?></div>
      </div>
      <div class="response-block" id="messages-response-block" style="margin-top:12px;">
        <div class="response-block-title">↳ Coller la nouvelle réponse</div>
        <textarea id="messages-response" class="form-control" rows="12"
                  placeholder="Colle ici la réponse complète avec les 3 formats..."></textarea>
        <div class="flex flex-gap mt-16">
          <button class="btn btn-primary" onclick="previewMessages()">Prévisualiser →</button>
        </div>
        <div id="messages-msg" class="hidden alert" style="margin-top:12px;"></div>
      </div>
      <div id="messages-preview-wrap" style="display:none;margin-top:24px;">
        <div class="sub-tabs-container">
          <div class="sub-tabs">
            <button class="sub-tab-btn active" onclick="switchSubTab('prev-lettre', this)">✦ Lettre</button>
            <button class="sub-tab-btn" onclick="switchSubTab('prev-linkedin', this)">in LinkedIn</button>
            <button class="sub-tab-btn" onclick="switchSubTab('prev-malt', this)">◎ Malt</button>
          </div>
          <div class="sub-tab-content active" id="sub-prev-lettre">
            <div id="prev-lettre-content" class="letter-preview"></div>
          </div>
          <div class="sub-tab-content" id="sub-prev-linkedin">
            <div id="prev-linkedin-counter" class="linkedin-counter" style="margin-bottom:10px;"></div>
            <div id="prev-linkedin-content" class="card" style="font-size:14px;line-height:1.8;white-space:pre-wrap;"></div>
          </div>
          <div class="sub-tab-content" id="sub-prev-malt">
            <div id="prev-malt-content" class="card" style="font-size:14px;line-height:1.8;white-space:pre-wrap;"></div>
          </div>
        </div>
        <div class="flex flex-gap mt-16">
          <button class="btn btn-primary" onclick="saveMessages()">Remplacer les 3 formats →</button>
          <button class="btn btn-ghost" onclick="document.getElementById('messages-preview-wrap').style.display='none';document.getElementById('messages-response-block').style.display='block';">← Modifier</button>
        </div>
        <div id="messages-save-msg" class="hidden alert" style="margin-top:12px;"></div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php endif; ?>

<script>
const appId = <?= $id > 0 ? $id : 'null' ?>;

// ── Copier un prompt ──────────────────────────────
function copyPrompt(id) {
  const text = document.getElementById(id).textContent;
  navigator.clipboard.writeText(text).then(() => {
    const btn = event.target;
    btn.textContent = '✓ Copié !';
    btn.style.background = '#2a7d4b';
    btn.style.color = 'white';
    setTimeout(() => { btn.textContent = '📋 Copier'; btn.style = ''; }, 2000);
  });
}

// ── Détection plateforme depuis URL ───────────────
const platformMap = {
  'linkedin.com': 'LinkedIn', 'apec.fr': 'APEC', 'hellowork.com': 'HelloWork',
  'cadremploi.fr': 'Cadremploi', 'michaelpage.fr': 'Michael Page',
  'robertwalters.fr': 'Robert Walters', 'roberthalffrance.fr': 'Robert Half',
  'indeed.com': 'Indeed', 'welcometothejungle.com': 'WTTJ',
  'monster.fr': 'Monster', 'jobteaser.com': 'JobTeaser', 'regionsjob.com': 'RégionsJob'
};
function updatePlatformBadge(url) {
  const badge = document.getElementById('platform-badge');
  if (!badge) return;
  if (!url) { badge.style.display = 'none'; badge.textContent = ''; return; }
  let label = '';
  for (const [domain, name] of Object.entries(platformMap)) {
    if (url.includes(domain)) { label = name; break; }
  }
  if (!label) {
    try { label = new URL(url).hostname.replace(/^www\./, ''); } catch(e) { label = 'Lien'; }
  }
  badge.textContent = label;
  badge.style.display = '';
}

// ── Step 1 — Récupérer l'annonce depuis une URL ───
function fetchJobPosting() {
  const url    = document.getElementById('source-url')?.value?.trim();
  const btn    = document.getElementById('btn-fetch');
  const msgEl  = document.getElementById('fetch-msg');

  if (!url) { showMsg(msgEl, 'Colle d\'abord une URL.', 'error'); return; }

  btn.disabled    = true;
  btn.textContent = '…';
  msgEl.className = 'hidden';

  fetch('php/fetch-job-posting.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ url })
  })
  .then(r => r.json())
  .then(d => {
    btn.disabled    = false;
    btn.textContent = '↓ Récupérer';
    if (d.success) {
      if (d.company)     document.getElementById('field-company').value     = d.company;
      if (d.position)    document.getElementById('field-position').value    = d.position;
      if (d.job_posting) document.getElementById('field-job-posting').value = d.job_posting;
      showMsg(msgEl, '✓ Annonce récupérée — vérifie et corrige si besoin.', 'success');
    } else {
      showMsg(msgEl, d.error || 'Erreur lors de la récupération.', 'error');
    }
  })
  .catch(() => {
    btn.disabled    = false;
    btn.textContent = '↓ Récupérer';
    showMsg(msgEl, 'Erreur réseau. Colle l\'annonce manuellement.', 'error');
  });
}

// ── Step 1 — Auto-détection "Entreprise recrute Poste" ─
(function() {
  const textarea = document.getElementById('field-job-posting');
  if (!textarea) return;
  textarea.addEventListener('blur', function() {
    const companyField  = document.getElementById('field-company');
    const positionField = document.getElementById('field-position');
    // Ne pas écraser des valeurs déjà saisies
    if (companyField.value.trim() && positionField.value.trim()) return;
    const text = this.value.trim();
    if (!text) return;
    // Chercher le pattern dans les 15 premières lignes
    const firstLines = text.split('\n').slice(0, 15).join('\n');
    const m = firstLines.match(
      /([\w\s\-&'.À-ÿ]{2,60}?)\s+recrute(?:\s*:)?\s+(?:un(?:e)?\s+)?([A-Za-zÀ-ÿ][^\n|–\-]{4,120}?)(?:\n|[|–\-]|$)/i
    );
    if (m) {
      if (!companyField.value.trim())
        companyField.value = m[1].trim();
      if (!positionField.value.trim())
        positionField.value = m[2].trim().replace(/^un(?:e)?\s+/i, '').replace(/\s*[,.].*$/, '').trim();
    }
  });
})();

// ── Step 1 — Créer / mettre à jour la candidature ─
const form1 = document.getElementById('form-step1');
if (form1) {
  form1.addEventListener('submit', function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    const btn  = this.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.textContent = 'Enregistrement...';

    fetch('php/save-application.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
      if (d.success) {
        window.location = 'new-application.php?id=' + d.id;
      } else {
        alert(d.error || 'Erreur lors de l\'enregistrement.');
        btn.disabled = false;
        btn.textContent = 'Générer le prompt d\'analyse →';
      }
    });
  });
}

// ── Steps 2, 3, 5 — Enregistrer la réponse collée ─
function saveStep(stepNum) {
  const fieldMap = { 2: 'analysis-response', 3: 'matching-response', 5: 'cv-response' };
  const msgMap   = { 2: 'step2-msg', 3: 'step3-msg', 5: 'step5-msg' };
  const value    = document.getElementById(fieldMap[stepNum])?.value?.trim();
  const msgEl    = document.getElementById(msgMap[stepNum]);

  if (!value) {
    showMsg(msgEl, 'Colle la réponse de Claude avant de continuer.', 'error');
    return;
  }

  const payload = { id: appId, step: stepNum, content: value };

  if (stepNum === 3) {
    const extra = document.getElementById('extra-experiences')?.value?.trim();
    if (extra) payload.extra_experiences = extra;
  }

  fetch('php/save-step.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(payload)
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) {
      window.location = 'new-application.php?id=' + appId;
    } else {
      showMsg(msgEl, d.error || 'Erreur lors de l\'enregistrement.', 'error');
    }
  });
}

// ── Step 3 — Parser et afficher le matching ────────
function parseMatchingResponse() {
  const raw   = document.getElementById('matching-response')?.value?.trim();
  const msgEl = document.getElementById('step3-parse-msg');

  if (!raw) {
    showMsg(msgEl, 'Colle la réponse de Claude avant de continuer.', 'error');
    return;
  }

  let parsed = null;
  try { parsed = JSON.parse(raw); } catch(e) {}
  if (!parsed) {
    const m = raw.match(/\{[\s\S]*\}/);
    if (m) { try { parsed = JSON.parse(m[0]); } catch(e) {} }
  }
  if (!parsed) {
    showMsg(msgEl, 'Le JSON semble invalide. Vérifie ce que Claude a retourné.', 'error');
    return;
  }

  const forceStyle = {
    'fort':   { bg: '#d4edda', color: '#155724', label: '● Fort' },
    'moyen':  { bg: '#fff3cd', color: '#856404', label: '◑ Moyen' },
    'faible': { bg: '#ffe5d0', color: '#7a3f00', label: '◔ Faible' },
    'absent': { bg: '#f8d7da', color: '#721c24', label: '○ Absent' }
  };

  let html = '';

  if (parsed.resume_matching) {
    html += `<div style="background:#f0eaf8;border-left:4px solid #6D155D;padding:14px 16px;border-radius:6px;font-size:14px;color:#333;margin-bottom:20px;">${parsed.resume_matching}</div>`;
  }

  if (parsed.points_forts?.length) {
    html += '<h3 style="font-size:13px;font-weight:700;color:#2a7d4b;margin-bottom:8px;text-transform:uppercase;letter-spacing:.4px;">Points forts</h3><ul style="list-style:none;padding:0;margin-bottom:20px;">';
    parsed.points_forts.forEach(p => {
      html += `<li style="font-size:13px;padding:8px 12px;margin-bottom:5px;background:#f0faf4;border-radius:6px;border-left:3px solid #2a7d4b;color:#333;">✓ ${p}</li>`;
    });
    html += '</ul>';
  }

  if (parsed.lacunes?.length) {
    html += '<h3 style="font-size:13px;font-weight:700;color:#c0392b;margin-bottom:8px;text-transform:uppercase;letter-spacing:.4px;">Lacunes / points à renforcer</h3><ul style="list-style:none;padding:0;margin-bottom:20px;">';
    parsed.lacunes.forEach(l => {
      html += `<li style="font-size:13px;padding:8px 12px;margin-bottom:5px;background:#fdf2f2;border-radius:6px;border-left:3px solid #c0392b;color:#333;">⚠ ${l}</li>`;
    });
    html += '</ul>';
  }

  if (parsed.correspondances?.length) {
    html += '<h3 style="font-size:13px;font-weight:700;color:#333;margin-bottom:10px;text-transform:uppercase;letter-spacing:.4px;">Correspondances détaillées</h3>';
    html += '<div style="border:1px solid #e4e0db;border-radius:8px;overflow:hidden;margin-bottom:20px;">';
    parsed.correspondances.forEach((c, i) => {
      const f  = forceStyle[c.force] || forceStyle['absent'];
      const bg = i % 2 === 0 ? '#fff' : '#fafaf8';
      html += `<div style="display:grid;grid-template-columns:1fr 2fr auto;gap:12px;padding:11px 14px;background:${bg};border-bottom:1px solid #e4e0db;align-items:start;">
        <div style="font-size:13px;font-weight:600;color:#333;">${c.competence}</div>
        <div style="font-size:13px;color:#555;">${c.trouve_dans_profil}</div>
        <div style="font-size:11px;font-weight:700;padding:3px 8px;border-radius:20px;background:${f.bg};color:${f.color};white-space:nowrap;">${f.label}</div>
      </div>`;
    });
    html += '</div>';
  }

  document.getElementById('matching-display').innerHTML = html;
  document.getElementById('phase-review').style.display = 'block';
  document.getElementById('btn-parse-matching').style.display = 'none';
  document.getElementById('matching-display').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Step 3 — Enrichissement compétence par compétence
const currentCompName = <?= json_encode(isset($currentComp) && $currentComp ? $currentComp['name'] : '') ?>;

// ── Enrichissement — toggle mode spécifique / transversal ─
function setEnrichMode(mode) {
  const isTransversal = (mode === 'transversal');
  document.getElementById('mode-specific-content').style.display  = isTransversal ? 'none' : '';
  document.getElementById('mode-transversal-content').style.display = isTransversal ? '' : 'none';

  const btnSpec  = document.getElementById('btn-mode-specific');
  const btnTrans = document.getElementById('btn-mode-transversal');
  btnSpec.style.background  = isTransversal ? '#fff'     : '#6D155D';
  btnSpec.style.color       = isTransversal ? '#6D155D'  : '#fff';
  btnTrans.style.background = isTransversal ? '#6D155D'  : '#fff';
  btnTrans.style.color      = isTransversal ? '#fff'     : '#6D155D';

  const label = document.getElementById('enrich-desc-label');
  if (label) label.innerHTML = isTransversal
    ? 'Décris en quelques mots en quoi cette compétence est transversale <span style="font-weight:400;color:#888;">(optionnel)</span>'
    : label.getAttribute('data-orig') || label.innerHTML;
  if (label && !label.getAttribute('data-orig')) label.setAttribute('data-orig', label.innerHTML);

  const ta = document.getElementById('enrich-description');
  if (ta) ta.placeholder = isTransversal
    ? 'Ex : pratiquée dans tous mes rôles de management, intégrée à ma posture professionnelle depuis 20 ans...'
    : 'Contexte, actions concrètes, résultats obtenus...';
}

function togglePill(btn) {
  btn.classList.toggle('selected');
}

function toggleNewPostForm(checked) {
  const f = document.getElementById('new-post-form');
  if (f) f.style.display = checked ? 'block' : 'none';
}

function updateEnrichPreview() {
  const desc = document.getElementById('enrich-description')?.value?.trim();
  const wrap = document.getElementById('enrich-preview');
  const text = document.getElementById('enrich-preview-text');
  if (!wrap || !text) return;
  if (desc) {
    text.textContent = currentCompName + ' : ' + desc;
    wrap.style.display = 'block';
  } else {
    wrap.style.display = 'none';
  }
}

function submitEnrichment(compIndex, compName) {
  const description   = document.getElementById('enrich-description')?.value?.trim();
  const msgEl         = document.getElementById('enrich-msg');
  const isTransversal = document.getElementById('btn-mode-transversal')?.style.background === 'rgb(109, 21, 93)';

  const selectedPills = document.querySelectorAll('.exp-pill.selected:not(.exp-pill-new)');
  const knowledgeIds  = Array.from(selectedPills).map(btn => parseInt(btn.dataset.id));
  const createNew     = document.querySelector('.exp-pill-new.selected') !== null;

  if (!isTransversal) {
    if (knowledgeIds.length === 0 && !createNew) {
      showMsg(msgEl, 'Coche au moins une expérience ou crée un nouveau poste.', 'error'); return;
    }
    if (!description) { showMsg(msgEl, 'Décris ton expérience avant de valider.', 'error'); return; }
  }

  const payload = {
    app_id: appId, comp_index: compIndex,
    knowledge_ids: knowledgeIds, competency: compName,
    description: description || '',
    transversal: isTransversal
  };
  if (createNew && !isTransversal) {
    payload.new_role    = document.getElementById('new-role')?.value?.trim()    || '';
    payload.new_company = document.getElementById('new-company')?.value?.trim() || '';
    payload.new_period  = document.getElementById('new-period')?.value?.trim()  || '';
  }

  fetch('php/enrich-knowledge.php', {
    method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(payload)
  })
  .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
  .then(d => {
    if (d.success) window.location.reload();
    else showMsg(msgEl, d.error || 'Erreur.', 'error');
  })
  .catch(e => showMsg(msgEl, 'Erreur : ' + e.message + '. Vérifie que enrich-knowledge.php est bien uploadé.', 'error'));
}

function saveLetter() {
  const raw   = document.getElementById('letter-response')?.value?.trim();
  const msgEl = document.getElementById('letter-msg');
  if (!raw) { showMsg(msgEl, 'Colle la lettre générée par Claude avant de sauvegarder.', 'error'); return; }
  fetch('php/save-letter.php', {
    method: 'POST', headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id: appId, content: raw })
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) window.location = 'new-application.php?id=' + appId + '&tab=lettre';
    else showMsg(msgEl, d.error || 'Erreur.', 'error');
  })
  .catch(e => showMsg(msgEl, 'Erreur réseau : ' + e.message, 'error'));
}

// ── Step 5 — Onglets CV / Écrits ──────────────────
function switchTab(name, btn) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-' + name).classList.add('active');
}

function switchSubTab(name, btn) {
  const container = btn.closest('.sub-tabs-container');
  container.querySelectorAll('.sub-tab-btn').forEach(b => b.classList.remove('active'));
  container.querySelectorAll('.sub-tab-content').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  const panel = container.querySelector('#sub-' + name);
  if (panel) panel.classList.add('active');
}

function parseMessages(raw) {
  const extract = (tag) => {
    const re = new RegExp('###' + tag + '###([\\s\\S]*?)###\\/' + tag + '###', 'i');
    const m = raw.match(re);
    return m ? m[1].trim() : '';
  };
  return { lettre: extract('LETTRE'), linkedin: extract('LINKEDIN'), malt: extract('MALT') };
}

function previewMessages() {
  const raw   = document.getElementById('messages-response')?.value?.trim();
  const msgEl = document.getElementById('messages-msg');
  if (!raw) { showMsg(msgEl, 'Colle la réponse de Claude avant de prévisualiser.', 'error'); return; }

  const parsed = parseMessages(raw);
  if (!parsed.lettre && !parsed.linkedin && !parsed.malt) {
    showMsg(msgEl, 'Format non reconnu — assure-toi de coller la réponse complète avec les balises ###LETTRE###, ###LINKEDIN### et ###MALT###.', 'error');
    return;
  }

  document.getElementById('prev-lettre-content').innerHTML  = parsed.lettre;
  document.getElementById('prev-linkedin-content').textContent = parsed.linkedin;
  document.getElementById('prev-malt-content').textContent  = parsed.malt;

  const llen    = parsed.linkedin.length;
  const counter = document.getElementById('prev-linkedin-counter');
  counter.textContent = llen + ' / 400 caractères';
  counter.className   = 'linkedin-counter' + (llen > 400 ? ' over' : '');

  window._parsedMessages = parsed;

  document.getElementById('messages-response-block').style.display = 'none';
  document.getElementById('messages-preview-wrap').style.display   = 'block';
  document.getElementById('messages-preview-wrap')
    .querySelector('.sub-tab-btn')?.click();
}

function saveMessages() {
  const data  = window._parsedMessages;
  const msgEl = document.getElementById('messages-save-msg');
  if (!data) { showMsg(msgEl, 'Prévisualise d\'abord les messages.', 'error'); return; }
  fetch('php/save-messages.php', {
    method: 'POST', headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id: appId, lettre: data.lettre, linkedin: data.linkedin, malt: data.malt })
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) window.location = 'new-application.php?id=' + appId + '&tab=ecrits';
    else showMsg(msgEl, d.error || 'Erreur.', 'error');
  })
  .catch(e => showMsg(msgEl, 'Erreur réseau : ' + e.message, 'error'));
}

function copyText(id) {
  const el = document.getElementById(id);
  if (!el) return;
  navigator.clipboard.writeText(el.textContent.trim()).then(() => {
    document.querySelectorAll('[onclick*="copyText(\'' + id + '\')"]').forEach(btn => {
      const orig = btn.textContent;
      btn.textContent = '✓ Copié !';
      btn.style.background = '#2a7d4b'; btn.style.color = 'white';
      setTimeout(() => { btn.textContent = orig; btn.style = ''; }, 2000);
    });
  });
}

function skipCompetency(compIndex) {
  fetch('php/skip-competency.php', {
    method: 'POST', headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ app_id: appId, comp_index: compIndex })
  }).then(r => r.json()).then(d => { if (d.success) window.location.reload(); });
}

function advanceToCV() {
  const msgEl = document.getElementById('advance-msg');
  fetch('php/advance-to-cv.php', {
    method: 'POST', headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ app_id: appId })
  }).then(r => r.json()).then(d => {
    if (d.success) window.location = 'new-application.php?id=' + appId;
    else showMsg(msgEl, d.error || 'Erreur.', 'error');
  });
}

// ── Step 4 — Prévisualiser / confirmer le CV ───────
function previewCV() {
  const raw   = document.getElementById('cv-response')?.value?.trim();
  const msgEl = document.getElementById('step4-msg');
  if (!raw) { showMsg(msgEl, 'Colle la réponse de Claude avant de prévisualiser.', 'error'); return; }

  // Extraire <section id="cv"> via DOMParser (gère correctement les sections imbriquées)
  const parser  = new DOMParser();
  const doc     = parser.parseFromString(raw, 'text/html');
  const cvEl    = doc.getElementById('cv');

  const previewArea = document.getElementById('cv-preview-area');
  previewArea.innerHTML = cvEl ? cvEl.outerHTML : raw;

  document.getElementById('cv-preview-wrap').style.display = 'block';
  document.getElementById('cv-preview-wrap').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetPreview() {
  document.getElementById('cv-preview-wrap').style.display = 'none';
  document.getElementById('cv-preview-area').innerHTML = '';
}

function confirmCV() {
  const raw   = document.getElementById('cv-response')?.value?.trim();
  const msgEl = document.getElementById('step5-confirm-msg');
  if (!raw) return;
  fetch('php/save-step.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id: appId, step: 4, content: raw })
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) window.location = 'new-application.php?id=' + appId;
    else showMsg(document.getElementById('step4-confirm-msg'), d.error || 'Erreur lors de l\'enregistrement.', 'error');
  });
}

// ── Utilitaires ────────────────────────────────────
function showMsg(el, text, type) {
  if (!el) return;
  el.textContent = text;
  el.className = 'alert alert-' + type;
  el.classList.remove('hidden');
  if (type === 'success') setTimeout(() => el.classList.add('hidden'), 3000);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
