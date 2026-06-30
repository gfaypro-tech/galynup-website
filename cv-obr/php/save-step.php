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
$stmt = $db->prepare("SELECT * FROM obr_applications WHERE id = ?");
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

        $db->prepare("UPDATE obr_applications SET analysis_result = ?, step_current = 3, status = 'matching', updated_at = NOW() WHERE id = ?")
           ->execute([json_encode($parsed, JSON_UNESCAPED_UNICODE), $id]);
        break;

    // ── Step 3 : matching reçu → parser JSON + construire enrichment_data + passer en phase dialogue
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

        // Récupérer les compétences clés depuis l'analyse
        $analysisData = json_decode($app['analysis_result'] ?? '{}', true) ?? [];
        $competencies = $analysisData['competences_cles'] ?? [];

        // Mots vides à ignorer dans la recherche par mots-clés
        $stopWords = ['de', 'du', 'des', 'le', 'la', 'les', 'et', 'en', 'au', 'aux',
                      'un', 'une', 'par', 'sur', 'dans', 'avec', 'pour', 'ou', 'à',
                      'the', 'of', 'and', 'in', 'for', 'with'];

        // Pour chaque compétence, chercher dans la base de connaissance (tous types)
        $enrichmentData = [];
        foreach ($competencies as $comp) {
            // 1. Recherche exacte sur la phrase complète (tous types)
            $compSearch = '%' . mb_strtolower(trim($comp)) . '%';
            $findStmt   = $db->prepare(
                "SELECT id, title, type FROM obr_knowledge
                 WHERE is_active = 1
                 AND (keywords LIKE ? OR title LIKE ? OR content LIKE ?)
                 ORDER BY FIELD(type,'experience','competence','formation','import','autre'), created_at DESC LIMIT 3"
            );
            $findStmt->execute([$compSearch, $compSearch, $compSearch]);
            $matches = $findStmt->fetchAll();

            // 2. Si rien trouvé → recherche par mots-clés significatifs (≥4 lettres, hors mots vides)
            if (empty($matches)) {
                $words = preg_split('/\s+/', mb_strtolower(trim($comp)));
                $keywords = array_filter($words, fn($w) => mb_strlen($w) >= 4 && !in_array($w, $stopWords));
                if (!empty($keywords)) {
                    $conditions = [];
                    $params = [];
                    foreach ($keywords as $kw) {
                        $like = '%' . $kw . '%';
                        $conditions[] = "(keywords LIKE ? OR title LIKE ? OR content LIKE ?)";
                        $params = array_merge($params, [$like, $like, $like]);
                    }
                    $sql = "SELECT id, title, type FROM obr_knowledge WHERE is_active = 1 AND "
                         . implode(' AND ', $conditions)
                         . " ORDER BY FIELD(type,'experience','competence','formation','import','autre'), created_at DESC LIMIT 3";
                    $findStmt = $db->prepare($sql);
                    $findStmt->execute($params);
                    $matches = $findStmt->fetchAll();
                }
            }

            $enrichmentData[] = [
                'name'    => $comp,
                'found'   => !empty($matches),
                'matches' => array_map(fn($m) => ['id' => $m['id'], 'title' => $m['title']], $matches),
                'status'  => 'pending',
            ];
        }

        $db->prepare("UPDATE obr_applications SET matching_result = ?, enrichment_data = ?, step_current = 3, status = 'dialogue', updated_at = NOW() WHERE id = ?")
           ->execute([json_encode($parsed, JSON_UNESCAPED_UNICODE), json_encode($enrichmentData, JSON_UNESCAPED_UNICODE), $id]);
        break;

    // ── Step 4 : CV reçu → extraire HTML + passer step 5
    case 4:
        $cvHtml = $content;
        if (preg_match('/<section[^>]*id=["\']cv["\'][^>]*>/i', $content, $openMatch, PREG_OFFSET_CAPTURE)) {
            $openTag   = $openMatch[0][0];
            $openPos   = $openMatch[0][1];
            $afterOpen = $openPos + strlen($openTag);
            $closePos  = strrpos($content, '</section>');
            if ($closePos !== false && $closePos > $afterOpen) {
                $cvHtml = $openTag . substr($content, $afterOpen, $closePos - $afterOpen) . '</section>';
            }
        }
        $db->prepare("UPDATE obr_applications SET cv_content = ?, validation_result = NULL, updated_at = NOW() WHERE id = ?")
           ->execute([$cvHtml, $id]);
        break;

    default:
        jsonResponse(['error' => 'Étape inconnue.'], 400);
}

jsonResponse(['success' => true]);
