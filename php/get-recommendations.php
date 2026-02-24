<?php
/**
 * Script de chargement automatique des recommandations approuvées
 * Retourne les recommandations depuis la base de données MySQL
 */

// Configuration de la base de données
define('DB_HOST', 'galynusgaellefay.mysql.db');
define('DB_NAME', 'galynusgaellefay');
define('DB_USER', 'galynusgaellefay');
define('DB_PASS', 'Newlife2026et253545y');

/**
 * Récupère les recommandations approuvées depuis la base de données
 * @param int $limit Nombre maximum de recommandations à retourner (0 = toutes)
 * @return array Liste des recommandations
 */
function getApprovedRecommendations($limit = 0) {
    try {
        // Connexion à la base de données
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            error_log('Database connection failed: ' . $conn->connect_error);
            return [];
        }
        
        $conn->set_charset('utf8mb4');
        
        // Préparer la requête SQL
        $sql = "SELECT 
                    id,
                    name,
                    position,
                    company,
                    email,
                    leadership,
                    project_management,
                    strategy,
                    methodology,
                    governance,
                    average_rating,
                    testimonial,
                    photo_path,
                    approved_at
                FROM recommendations 
                WHERE status = 'approved'
                ORDER BY approved_at DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        $result = $conn->query($sql);
        
        if (!$result) {
            error_log('Query failed: ' . $conn->error);
            $conn->close();
            return [];
        }
        
        $recommendations = [];
        while ($row = $result->fetch_assoc()) {
            $recommendations[] = $row;
        }
        
        $conn->close();
        
        return $recommendations;
        
    } catch (Exception $e) {
        error_log('Exception in getApprovedRecommendations: ' . $e->getMessage());
        return [];
    }
}

/**
 * Formate un texte avec la première lettre en majuscule et le reste en minuscule
 * @param string $text Texte à formater
 * @return string Texte formaté
 */
function formatName($text) {
    // Convertir en minuscules puis mettre la première lettre en majuscule
    return mb_convert_case(mb_strtolower($text, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
}

/**
 * Génère le HTML pour une carte de recommandation
 * @param array $rec Données de la recommandation
 * @return string HTML de la carte
 */
function generateRecommendationCard($rec) {
    $name = htmlspecialchars(formatName($rec['name']), ENT_QUOTES, 'UTF-8');
    $position = htmlspecialchars(formatName($rec['position']), ENT_QUOTES, 'UTF-8');
    $company = htmlspecialchars(formatName($rec['company']), ENT_QUOTES, 'UTF-8');
    // Décoder d'abord les entités HTML existantes, puis ré-encoder proprement
    $testimonial = nl2br(htmlspecialchars(html_entity_decode($rec['testimonial'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
    $average_rating = number_format($rec['average_rating'], 1);
    
    // Générer les étoiles pour chaque critère
    $generateStars = function($rating) {
        $filled = str_repeat('<span class="star filled">★</span>', $rating);
        $empty = str_repeat('<span class="star">☆</span>', 5 - $rating);
        return $filled . $empty;
    };
    
    // Générer les étoiles pour la note moyenne (arrondie)
    $avgStars = $generateStars(round($rec['average_rating']));
    
    $html = '<div class="recommandation-card">
    <div class="recommandation-header">
        <div class="recommandation-avatar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </div>
        <div class="recommandation-info">
        <div class="recommandation-info">
    <div class="recommandation-name">' . $name . '</div>
    <p class="recommandation-title">' . $position . '</p>
    <p class="recommandation-company">' . $company . '</p>
</div>
        </div>
        <div class="recommandation-rating">
            <div class="rating-number">' . $average_rating . '</div>
            <div class="rating-stars">' . $avgStars . '</div>
        </div>
    </div>
    <div class="recommandation-ratings">
        <div class="rating-item">
            <span class="rating-label">Leadership</span>
            <div class="stars">' . $generateStars($rec['leadership']) . '</div>
        </div>
        <div class="rating-item">
            <span class="rating-label">Direction de projet</span>
            <div class="stars">' . $generateStars($rec['project_management']) . '</div>
        </div>
        <div class="rating-item">
            <span class="rating-label">Stratégie</span>
            <div class="stars">' . $generateStars($rec['strategy']) . '</div>
        </div>
        <div class="rating-item">
            <span class="rating-label">Méthodologie</span>
            <div class="stars">' . $generateStars($rec['methodology']) . '</div>
        </div>
        <div class="rating-item">
            <span class="rating-label">Gouvernance</span>
            <div class="stars">' . $generateStars($rec['governance']) . '</div>
        </div>
    </div>
    <div class="recommandation-text">
        <p>"' . $testimonial . '"</p>
    </div>
</div>';
    
    return $html;
}

/**
 * Affiche toutes les recommandations approuvées
 * @param int $limit Nombre maximum de recommandations (0 = toutes)
 */
function displayRecommendations($limit = 0) {
    $recommendations = getApprovedRecommendations($limit);
    
    if (empty($recommendations)) {
        // Si aucune recommandation, afficher les exemples par défaut
        echo '<!-- Aucune recommandation approuvée pour le moment -->';
        return;
    }
    
    foreach ($recommendations as $rec) {
        echo generateRecommendationCard($rec);
    }
}

// Si le script est appelé directement (pour debug)
if (basename($_SERVER['PHP_SELF']) === 'get-recommendations.php') {
    header('Content-Type: text/html; charset=UTF-8');
    displayRecommendations();
}
?>
