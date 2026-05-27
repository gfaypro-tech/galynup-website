<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$data    = json_decode(file_get_contents('php://input'), true);
$id      = (int)($data['id'] ?? 0);
$step    = (int)($data['step'] ?? 0);
$content = trim($data['content'] ?? '');

if ($id <= 0 || $step <= 0) jsonResponse(['error' => 'Paramètres invalides.'], 400);

$db   = getDB();
$stmt = $db->prepare("SELECT * FROM cv_applications WHERE id = ?");
$stmt->execute([$id]);
$app  = $stmt->fetch();
if (!$app) jsonResponse(['error' => 'Candidature introuvable.'], 404);

switch ($step) {
    // ── Step 2 : analyse reçue → parse JSON + passer step 3
    case 2:
        // Valider que c'est du JSON
        $parsed = json_decode($content, true);
        if (!$parsed) {
            // Essayer d'extraire le JSON si du texte l'entoure
            preg_match('/\{[\s\S]*\}/u', $content, $matches);
            if (!empty($matches[0])) {
                $parsed  = json_decode($matches[0], true);
                $content = $matches[0];
            }
        }
        if (!$parsed) jsonResponse(['error' => 'La réponse ne semble pas être du JSON valide. Vérifie ce que Claude a retourné.'], 400);

        $db->prepare("UPDATE cv_applications SET analysis_result = ?, step_current = 3, status = 'matching', updated_at = NOW() WHERE id = ?")
           ->execute([json_encode($parsed, JSON_UNESCAPED_UNICODE), $id]);
        break;

    // ── Step 3 : matching reçu → parse JSON + extraire questions + passer step 4
    case 3:
        $parsed = json_decode($content, true);
        if (!$parsed) {
            preg_match('/\{[\s\S]*\}/u', $content, $matches);
            if (!empty($matches[0])) {
                $parsed  = json_decode($matches[0], true);
                $content = $matches[0];
            }
        }
        if (!$parsed) jsonResponse(['error' => 'La réponse ne semble pas être du JSON valide.'], 400);

        // Supprimer les anciennes questions si on recommence
        $db->prepare("DELETE FROM cv_dialogue WHERE application_id = ?")->execute([$id]);

        // Sauvegarder les expériences complémentaires non listées (question_order = 0)
        $extraExperiences = trim($data['extra_experiences'] ?? '');
        if ($extraExperiences !== '') {
            $db->prepare("INSERT INTO cv_dialogue (application_id, question_order, question, answer) VALUES (?, 0, ?, ?)")
               ->execute([$id, 'Expériences complémentaires non listées dans la base de connaissance', $extraExperiences]);
        }

        // Insérer les questions extraites (question_order >= 1)
        $questions = $parsed['questions'] ?? [];
        if (!empty($questions)) {
            $qStmt = $db->prepare("INSERT INTO cv_dialogue (application_id, question_order, question) VALUES (?, ?, ?)");
            foreach ($questions as $q) {
                $qText = trim($q['question'] ?? '');
                $qId   = (int)($q['id'] ?? 0);
                if ($qText && $qId >= 1) $qStmt->execute([$id, $qId, $qText]);
            }
        }

        $db->prepare("UPDATE cv_applications SET matching_result = ?, step_current = 4, status = 'dialogue', updated_at = NOW() WHERE id = ?")
           ->execute([json_encode($parsed, JSON_UNESCAPED_UNICODE), $id]);
        break;

    // ── Step 4 : dialogue terminé → passer step 5
    case 4:
        $db->prepare("UPDATE cv_applications SET step_current = 5, status = 'generating', updated_at = NOW() WHERE id = ?")
           ->execute([$id]);
        break;

    // ── Step 5 : CV reçu → extraire HTML + passer step 6 + capitaliser le dialogue
    case 5:
        // Extraire <section id="cv">…</section> — strrpos pour gérer les sections imbriquées
        $cvHtml = $content;
        if (preg_match('/<section[^>]*id=["\']cv["\'][^>]*>/i', $content, $openMatch, PREG_OFFSET_CAPTURE)) {
            $openTag  = $openMatch[0][0];
            $openPos  = $openMatch[0][1];
            $afterOpen = $openPos + strlen($openTag);
            $closePos = strrpos($content, '</section>');
            if ($closePos !== false && $closePos > $afterOpen) {
                $cvHtml = $openTag . substr($content, $afterOpen, $closePos - $afterOpen) . '</section>';
            }
        }
        $db->prepare("UPDATE cv_applications SET cv_content = ?, step_current = 6, status = 'completed', updated_at = NOW() WHERE id = ?")
           ->execute([$cvHtml, $id]);

        // Enrichir la base de connaissance avec les réponses validées (première génération seulement)
        if ((int)$app['step_current'] === 5) {
            $dStmt = $db->prepare(
                "SELECT question, answer FROM cv_dialogue
                 WHERE application_id = ? AND question_order >= 1 AND answer IS NOT NULL AND answer != ''"
            );
            $dStmt->execute([$id]);
            $dialogueAnswers = $dStmt->fetchAll();

            if (!empty($dialogueAnswers)) {
                $stopWords = ['cette','votre','avez','vous','dans','avec','pour','quels',
                    'quelles','pouvez','décris','décrivez','quand','comment','quelle','quel',
                    'avoir','être','faire','plus','comme','mais','dont','aussi','entre',
                    'après','avant','depuis','pendant','lors','selon','suite','parle','parlez'];

                foreach ($dialogueAnswers as $d) {
                    // Extraire les mots-clés significatifs de la question
                    $words    = preg_split('/[\s\W]+/u', mb_strtolower($d['question']));
                    $keywords = array_values(array_filter($words,
                        fn($w) => mb_strlen($w) > 4 && !in_array($w, $stopWords)));
                    $keywords = array_slice($keywords, 0, 4);

                    if (empty($keywords)) continue;

                    // Trouver l'entrée existante la plus pertinente
                    $conditions = [];
                    $params     = [];
                    foreach ($keywords as $kw) {
                        $conditions[] = "(title LIKE ? OR content LIKE ?)";
                        $params[]     = "%$kw%";
                        $params[]     = "%$kw%";
                    }
                    $params[] = 1;

                    $findStmt = $db->prepare(
                        "SELECT id, content FROM cv_knowledge
                         WHERE (" . implode(' OR ', $conditions) . ") AND is_active = ?
                         ORDER BY type = 'experience' DESC, created_at DESC LIMIT 1"
                    );
                    $findStmt->execute($params);
                    $entry = $findStmt->fetch();

                    if ($entry) {
                        // Enrichir l'entrée existante
                        $enriched = $entry['content'] . "\n\n---\n" . $d['answer'];
                        $db->prepare("UPDATE cv_knowledge SET content = ? WHERE id = ?")
                           ->execute([$enriched, $entry['id']]);
                    } else {
                        // Aucune entrée trouvée → créer une nouvelle (info vraiment absente de la base)
                        $db->prepare("INSERT INTO cv_knowledge (type, title, content) VALUES ('experience', ?, ?)")
                           ->execute([mb_substr($d['question'], 0, 100), $d['answer']]);
                    }
                }
            }
        }
        break;

    default:
        jsonResponse(['error' => 'Étape inconnue.'], 400);
}

jsonResponse(['success' => true]);
