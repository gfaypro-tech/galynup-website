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
    $entries = $db->query("SELECT type, title, content, meta_json FROM cv_knowledge WHERE is_active = 1 ORDER BY type, created_at")->fetchAll();
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
  "questions": [
    {
      "id": 1,
      "question": "Question précise sur une expérience ou réalisation concrète ?",
      "pourquoi": "En quoi la réponse renforcera la candidature pour ce poste",
      "reponse_suggeree": "Réponse rédigée en première personne, basée sur les éléments trouvés dans le profil — ex : J'ai piloté..."
    }
  ]
}

Génère EXACTEMENT 3 questions — pas plus, pas moins.
Choisis les 3 questions les plus stratégiques pour mettre en évidence les compétences
attendues par le recruteur au travers des expériences concrètes du candidat.
Pour chaque question, rédige une reponse_suggeree en PREMIÈRE PERSONNE (je, j'ai, mon, ma...)
basée sur les informations déjà disponibles dans le profil du candidat.
La reponse_suggeree doit être une ébauche concrète et directement utilisable que le candidat
pourra compléter ou corriger — pas une invitation à répondre, mais une réponse rédigée.
Si le profil ne contient pas d'éléments pertinents pour cette question, écris une trame vide
avec les points clés à renseigner entre [crochets].
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

function buildCVPrompt(string $knowledge, string $analysisJson, string $matchingJson, array $dialogue, string $jobPosting): string {
    $qa = '';
    foreach ($dialogue as $d) {
        if (!empty($d['answer'])) {
            if ((int)$d['question_order'] === 0) {
                $qa .= "EXPÉRIENCES COMPLÉMENTAIRES HORS BASE :\n" . $d['answer'] . "\n\n";
            } else {
                $qa .= "Q : " . $d['question'] . "\nR : " . $d['answer'] . "\n\n";
            }
        }
    }
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
      <li>PMI-ACP, Agile Certified Practitioner, PMI</li>
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
INFORMATIONS COMPLÉMENTAIRES (dialogue + expériences hors base) :
$qa
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

$dialogue = [];
if ($app && $step >= 4) {
    $stmt = $db->prepare("SELECT * FROM cv_dialogue WHERE application_id = ? ORDER BY question_order");
    $stmt->execute([$app['id']]);
    $dialogue = $stmt->fetchAll();
}

$currentQuestion = null;
$allAnswered     = true;
if ($step === 4 && !empty($dialogue)) {
    foreach ($dialogue as $q) {
        if (empty($q['answer'])) {
            $currentQuestion = $q;
            $allAnswered = false;
            break;
        }
    }
}

$stepLabels = ['Fiche de poste', 'Analyse', 'Matching', 'Dialogue', 'Génération CV', 'Export'];

$pageTitle  = $app ? 'Candidature — ' . htmlspecialchars($app['company']) : 'Nouvelle candidature';
$activePage = 'new';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Barre de progression -->
<div class="steps-bar">
  <?php for ($i = 1; $i <= 6; $i++): ?>
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

  <?php if (empty(trim($knowledge)) || strpos($knowledge, 'vide') !== false): ?>
    <div class="alert alert-warning">
      ⚠ Ta base de connaissance est vide. <a href="knowledge-base.php">Ajoute des entrées</a> avant de candidater.
    </div>
  <?php endif; ?>

  <form id="form-step1">
    <div class="grid-2">
      <div class="form-group">
        <label>Entreprise</label>
        <input type="text" name="company" class="form-control" required
               value="<?= htmlspecialchars($app['company'] ?? '') ?>"
               placeholder="Nom de l'entreprise">
      </div>
      <div class="form-group">
        <label>Poste visé</label>
        <input type="text" name="position" class="form-control" required
               value="<?= htmlspecialchars($app['position'] ?? '') ?>"
               placeholder="Intitulé du poste">
      </div>
    </div>
    <div class="form-group">
      <label>Fiche de poste complète</label>
      <textarea name="job_posting" class="form-control" rows="16" required
                placeholder="Colle ici la fiche de poste intégrale..."><?= htmlspecialchars($app['job_posting'] ?? '') ?></textarea>
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
elseif ($step === 3):
    $prompt = buildMatchingPrompt($knowledge, $analysisJson, $app['job_posting']);
?>
<div class="card">
  <div class="card-title">🎯 Étape 3 — Matching avec ta base de connaissance</div>

  <div class="alert alert-info">
    Ce prompt inclut l'intégralité de ta base de connaissance. Claude va identifier ce qui matche
    et générer des questions ciblées pour enrichir le CV.
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

    <div class="response-block" style="margin-top:24px;">
      <div class="response-block-title">As-tu des expériences non listées dans ta base de connaissance ?</div>
      <p style="font-size:13px; color:#6b6b65; margin:8px 0 10px;">
        Regarde les lacunes identifiées ci-dessus. Si tu as des projets ou réalisations non encore documentés dans ta base, décris-les ici — ils seront intégrés comme contexte supplémentaire pour la génération du CV.
      </p>
      <textarea id="extra-experiences" class="form-control" rows="4"
                placeholder="Ex : Lors de mon passage chez X en 2022, j'ai aussi piloté une migration SI — non encore documentée dans ma base..."></textarea>
      <div class="flex flex-gap mt-16">
        <button class="btn btn-primary btn-lg" onclick="saveStep(3)">Enregistrer et continuer →</button>
      </div>
      <div id="step3-msg" class="hidden alert" style="margin-top:12px;"></div>
    </div>
  </div>
</div>

<?php /* ═══════════════════════════════════════════════════
   STEP 4 — Dialogue Q&A
   ═══════════════════════════════════════════════════ */
elseif ($step === 4):
    // Séparer l'entrée "expériences complémentaires" (question_order=0) des vraies questions
    $gapEntry       = null;
    $actualDialogue = [];
    foreach ($dialogue as $q) {
        if ((int)$q['question_order'] === 0) {
            $gapEntry = $q;
        } else {
            $actualDialogue[] = $q;
        }
    }
    $answered = array_filter($actualDialogue, fn($q) => !empty($q['answer']));
    $total    = count($actualDialogue);
    $done     = count($answered);
?>
<div class="card">
  <div class="card-title">💬 Étape 4 — Dialogue</div>

  <?php if ($gapEntry && !empty($gapEntry['answer'])): ?>
    <div style="margin-bottom:20px; padding:14px 16px; background:#f8f4fc; border-left:4px solid #6D155D; border-radius:6px;">
      <div style="font-size:11px; font-weight:700; color:#6D155D; text-transform:uppercase; letter-spacing:.6px; margin-bottom:6px;">Expériences complémentaires (hors base de connaissance)</div>
      <div style="font-size:13px; color:#333; white-space:pre-wrap;"><?= htmlspecialchars($gapEntry['answer']) ?></div>
    </div>
  <?php endif; ?>

  <?php if (empty($actualDialogue)): ?>
    <div class="alert alert-warning">
      Aucune question trouvée. <a href="php/reparse-questions.php?id=<?= $id ?>">Réanalyser le matching</a>
      ou <a href="#" onclick="addManualQuestion()">ajouter une question manuellement</a>.
    </div>
  <?php elseif ($allAnswered): ?>
    <div class="alert alert-success">
      ✓ Toutes les questions ont été répondues. Tu peux passer à la génération du CV.
    </div>
    <div class="mt-16">
      <h3 style="font-size:15px; font-weight:600; margin-bottom:12px;">Récapitulatif du dialogue</h3>
      <?php foreach ($actualDialogue as $i => $q): ?>
        <div style="margin-bottom:16px; padding:14px; background:#f9f9f7; border-radius:8px; border:1px solid #e4e0db;">
          <div style="font-weight:600; font-size:13px; color:#6D155D; margin-bottom:4px;">Question <?= $i+1 ?></div>
          <div style="font-size:14px; margin-bottom:8px;"><?= htmlspecialchars($q['question']) ?></div>
          <div style="font-size:13px; color:#444; border-left:3px solid #D3A625; padding-left:10px;"><?= nl2br(htmlspecialchars($q['answer'])) ?></div>
        </div>
      <?php endforeach; ?>
      <button class="btn btn-primary btn-lg" onclick="goToStep5()">Générer le CV →</button>
    </div>
  <?php else: ?>
    <div class="alert alert-info">
      Question <?= $done + 1 ?> sur <?= $total ?>. Réponds directement ici, dans l'application.
    </div>

    <div class="question-card">
      <div class="question-number">Question <?= $done + 1 ?> / <?= $total ?></div>
      <div class="question-text"><?= htmlspecialchars($currentQuestion['question']) ?></div>
      <?php
        $matchingData = json_decode($matchingJson, true);
        $pourquoi     = '';
        $suggestion   = '';
        if ($matchingData && isset($matchingData['questions'])) {
            foreach ($matchingData['questions'] as $mq) {
                if ((int)$mq['id'] === (int)$currentQuestion['question_order']) {
                    $pourquoi   = $mq['pourquoi'] ?? '';
                    $suggestion = $mq['reponse_suggeree'] ?? '';
                    break;
                }
            }
        }
      ?>
      <?php if ($pourquoi): ?>
        <div class="question-why">Pourquoi cette question : <?= htmlspecialchars($pourquoi) ?></div>
      <?php endif; ?>

      <?php if ($suggestion): ?>
        <div style="font-size:12px; color:#1565c0; background:#e8f4fd; border:1px solid #b3d4f0; border-radius:6px; padding:8px 12px; margin-bottom:8px;">
          💡 Suggestion de Claude basée sur ton profil — à compléter ou corriger :
        </div>
      <?php endif; ?>
      <textarea id="answer-text" class="form-control" rows="7"
                placeholder="Décris ton expérience concrète sur ce sujet..."><?= htmlspecialchars($suggestion) ?></textarea>
      <div style="font-size:11px; color:#888; margin-top:6px;">
        Les réponses validées seront automatiquement enregistrées dans ta base de connaissance à la fin du processus.
      </div>

      <div class="flex flex-gap mt-16">
        <button class="btn btn-primary" onclick="saveAnswer(<?= $currentQuestion['id'] ?>)">
          Répondre →
        </button>
        <button class="btn btn-ghost" onclick="skipQuestion(<?= $currentQuestion['id'] ?>)">
          Passer cette question
        </button>
      </div>
      <div id="answer-msg" class="hidden alert" style="margin-top:12px;"></div>
    </div>

    <!-- Questions précédentes -->
    <?php if ($done > 0): ?>
      <div style="margin-top:20px;">
        <div style="font-size:13px; font-weight:600; color:#6b6b65; margin-bottom:10px;">Réponses précédentes :</div>
        <?php foreach ($answered as $q): ?>
          <div style="margin-bottom:10px; padding:12px; background:#f9f9f7; border-radius:8px; border:1px solid #e4e0db; font-size:13px;">
            <strong><?= htmlspecialchars($q['question']) ?></strong><br>
            <span style="color:#444;"><?= nl2br(htmlspecialchars($q['answer'])) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php /* ═══════════════════════════════════════════════════
   STEP 5 — Génération du CV
   ═══════════════════════════════════════════════════ */
elseif ($step === 5):
    $prompt = buildCVPrompt($knowledge, $analysisJson, $matchingJson, $dialogue, $app['job_posting']);
?>
<div class="card">
  <div class="card-title">✍ Étape 5 — Génération du CV</div>

  <div class="alert alert-info">
    Ce prompt contient l'ensemble des informations collectées. Claude va générer ton CV sur-mesure.
    Colle le résultat ci-dessous — l'app extraira automatiquement le HTML du CV.
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
    <div id="step5-msg" class="hidden alert" style="margin-top:12px;"></div>
  </div>

  <!-- Zone de prévisualisation (masquée par défaut) -->
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
    <div id="step5-confirm-msg" class="hidden alert" style="margin-top:12px;"></div>
  </div>
</div>

<?php /* ═══════════════════════════════════════════════════
   STEP 6 — Export
   ═══════════════════════════════════════════════════ */
elseif ($step === 6): ?>
<div class="alert alert-success">
  ✓ CV généré avec succès pour <strong><?= htmlspecialchars($app['company']) ?></strong> — <?= htmlspecialchars($app['position']) ?>
</div>

<div class="flex flex-gap mb-16">
  <a href="php/export-word.php?id=<?= $id ?>" class="btn btn-primary btn-lg">⬇ Télécharger Word (.doc)</a>
  <button onclick="window.print()" class="btn btn-outline btn-lg">🖨 Imprimer / PDF</button>
  <a href="new-application.php" class="btn btn-gold">+ Nouvelle candidature</a>
</div>

<div class="cv-preview" id="cv-output">
  <?= $cvContent ?>
</div>

<div style="margin-top:16px; font-size:12px; color:#888;">
  ✓ Les réponses du dialogue ont été automatiquement intégrées à ta base de connaissance.
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

  if (parsed.questions?.length) {
    html += `<div style="font-size:13px;color:#6b6b65;padding:10px 14px;background:#f9f9f7;border-radius:6px;border:1px solid #e4e0db;">
      💬 <strong>${parsed.questions.length} question(s)</strong> générées pour approfondir ton profil lors de l'étape de dialogue.
    </div>`;
  }

  document.getElementById('matching-display').innerHTML = html;
  document.getElementById('phase-review').style.display = 'block';
  document.getElementById('btn-parse-matching').style.display = 'none';
  document.getElementById('matching-display').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Step 4 — Enregistrer une réponse au dialogue ──
function saveAnswer(questionId) {
  const answer = document.getElementById('answer-text')?.value?.trim();
  const msgEl  = document.getElementById('answer-msg');
  if (!answer) {
    showMsg(msgEl, 'Saisis ta réponse avant de continuer.', 'error');
    return;
  }
  fetch('php/save-answer.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id: appId, question_id: questionId, answer })
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) window.location.reload();
    else showMsg(msgEl, d.error || 'Erreur.', 'error');
  });
}

function skipQuestion(questionId) {
  if (!confirm('Passer cette question sans répondre ?')) return;
  fetch('php/save-answer.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id: appId, question_id: questionId, answer: '' })
  })
  .then(r => r.json())
  .then(d => { if (d.success) window.location.reload(); });
}

function goToStep5() {
  fetch('php/save-step.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id: appId, step: 4, content: '__dialogue_complete__' })
  })
  .then(r => r.json())
  .then(d => { if (d.success) window.location = 'new-application.php?id=' + appId; });
}

// ── Step 6 — Ajouter dialogue à la base ───────────
function addDialogueToKnowledge() {
  const msgEl = document.getElementById('knowledge-save-msg');
  fetch('php/add-to-knowledge.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id: appId })
  })
  .then(r => r.json())
  .then(d => {
    const type = d.success ? 'success' : 'error';
    showMsg(msgEl, d.success ? 'Réponses ajoutées à la base de connaissance.' : (d.error || 'Erreur.'), type);
  });
}

// ── Step 5 — Prévisualiser / confirmer le CV ───────
function previewCV() {
  const raw   = document.getElementById('cv-response')?.value?.trim();
  const msgEl = document.getElementById('step5-msg');
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
    body: JSON.stringify({ id: appId, step: 5, content: raw })
  })
  .then(r => r.json())
  .then(d => {
    if (d.success) window.location = 'new-application.php?id=' + appId;
    else showMsg(msgEl, d.error || 'Erreur lors de l\'enregistrement.', 'error');
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
