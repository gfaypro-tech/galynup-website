<?php
/**
 * Interface d'administration des recommandations
 * Permet de voir, approuver, rejeter et générer le HTML des recommandations
 */

session_start();

// Configuration simple d'authentification
// À MODIFIER : Changez ces identifiants !
define('ADMIN_USERNAME', 'gfay_admin');
define('ADMIN_PASSWORD', 'Newlife@2026&18$'); // 

// Configuration de la base de données
define('DB_HOST', 'galynusgaellefay.mysql.db');
define('DB_NAME', 'galynusgaellefay');
define('DB_USER', 'galynusgaellefay');
define('DB_PASS', 'Newlife2026et253545y');

// Vérifier l'authentification
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Afficher le formulaire de connexion
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $login_error = 'Identifiants incorrects';
        }
    }
    
    // Formulaire de connexion
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Connexion - Administration GALYN'UP</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-container {
                background: white;
                padding: 3rem;
                border-radius: 1rem;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                width: 100%;
                max-width: 400px;
            }
            h1 {
                text-align: center;
                color: #333;
                margin-bottom: 2rem;
            }
            .form-group {
                margin-bottom: 1.5rem;
            }
            label {
                display: block;
                margin-bottom: 0.5rem;
                color: #555;
                font-weight: 500;
            }
            input[type="text"],
            input[type="password"] {
                width: 100%;
                padding: 0.75rem;
                border: 2px solid #e0e0e0;
                border-radius: 0.5rem;
                font-size: 1rem;
            }
            input[type="text"]:focus,
            input[type="password"]:focus {
                outline: none;
                border-color: #667eea;
            }
            button {
                width: 100%;
                padding: 1rem;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 0.5rem;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
            }
            button:hover {
                opacity: 0.9;
            }
            .error {
                background: #fee;
                color: #c00;
                padding: 0.75rem;
                border-radius: 0.5rem;
                margin-bottom: 1rem;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1>🔐 Administration</h1>
            <?php if (isset($login_error)): ?>
                <div class="error"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Identifiant</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="login">Se connecter</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Connexion à la base de données
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die('Erreur de connexion à la base de données');
}
$conn->set_charset('utf8mb4');

// Actions (approuver, rejeter, supprimer)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    if ($id > 0) {
        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE recommendations SET status = 'approved', approved_at = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $message = '✅ Recommandation approuvée';
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("UPDATE recommendations SET status = 'rejected', updated_at = NOW() WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $message = '❌ Recommandation rejetée';
        } elseif ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM recommendations WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $message = '🗑️ Recommandation supprimée';
        }
    }
}

// Récupérer les recommandations
$filter = $_GET['filter'] ?? 'pending';
$sql = "SELECT * FROM recommendations";
if ($filter !== 'all') {
    $sql .= " WHERE status = ?";
}
$sql .= " ORDER BY created_at DESC";

if ($filter !== 'all') {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $filter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$recommendations = [];
while ($row = $result->fetch_assoc()) {
    $recommendations[] = $row;
}

// Compter les recommandations par statut
$counts = [
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0
];

$count_result = $conn->query("SELECT status, COUNT(*) as count FROM recommendations GROUP BY status");
while ($row = $count_result->fetch_assoc()) {
    $counts[$row['status']] = $row['count'];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des recommandations - GALYN'UP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 2rem;
        }
        .header {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #333; }
        .logout-btn {
            padding: 0.5rem 1rem;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 0.25rem;
        }
        .filters {
            background: white;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .filter-btn {
            padding: 0.5rem 1rem;
            border: 2px solid #ddd;
            background: white;
            border-radius: 0.25rem;
            cursor: pointer;
            text-decoration: none;
            color: #333;
        }
        .filter-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .recommendation-card {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .recommendation-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        .recommendation-info h3 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        .recommendation-info p {
            color: #666;
            margin: 0.25rem 0;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .ratings {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
            padding: 1rem;
            background: #f9f9f9;
            border-radius: 0.5rem;
        }
        .rating-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stars {
            color: #d97706;
            font-size: 1.25rem;
        }
        .testimonial {
            background: #f9f9f9;
            padding: 1.5rem;
            border-radius: 0.5rem;
            margin: 1.5rem 0;
            line-height: 1.6;
            color: #333;
        }
        .actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-approve { background: #28a745; color: white; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-delete { background: #6c757d; color: white; }
        .btn-html { background: #007bff; color: white; }
        .html-output {
            background: #f4f4f4;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
            font-family: monospace;
            font-size: 0.875rem;
            max-height: 300px;
            overflow-y: auto;
            display: none;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🌟 Gestion des recommandations</h1>
        <a href="?logout" class="logout-btn">Déconnexion</a>
    </div>
    
    <div class="filters">
        <a href="?filter=pending" class="filter-btn <?= $filter === 'pending' ? 'active' : '' ?>">
            En attente (<?= $counts['pending'] ?>)
        </a>
        <a href="?filter=approved" class="filter-btn <?= $filter === 'approved' ? 'active' : '' ?>">
            Approuvées (<?= $counts['approved'] ?>)
        </a>
        <a href="?filter=rejected" class="filter-btn <?= $filter === 'rejected' ? 'active' : '' ?>">
            Rejetées (<?= $counts['rejected'] ?>)
        </a>
        <a href="?filter=all" class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>">
            Toutes
        </a>
    </div>
    
    <?php if (isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <?php if (empty($recommendations)): ?>
        <div class="empty-state">
            <h2>Aucune recommandation</h2>
            <p>Il n'y a aucune recommandation dans cette catégorie.</p>
        </div>
    <?php else: ?>
        <?php foreach ($recommendations as $rec): ?>
            <div class="recommendation-card">
                <div class="recommendation-header">
                    <div class="recommendation-info">
                        <h3><?= htmlspecialchars($rec['name']) ?></h3>
                        <p><strong><?= htmlspecialchars($rec['position']) ?></strong> chez <?= htmlspecialchars($rec['company']) ?></p>
                        <?php if ($rec['email']): ?>
                            <p>📧 <?= htmlspecialchars($rec['email']) ?></p>
                        <?php endif; ?>
                        <p style="font-size: 0.875rem; color: #999;">
                            Soumis le <?= date('d/m/Y à H:i', strtotime($rec['created_at'])) ?>
                        </p>
                    </div>
                    <span class="status-badge status-<?= $rec['status'] ?>">
                        <?= $rec['status'] === 'pending' ? 'En attente' : ($rec['status'] === 'approved' ? 'Approuvée' : 'Rejetée') ?>
                    </span>
                </div>
                
                <div class="ratings">
                    <div class="rating-item">
                        <span>Leadership</span>
                        <span class="stars"><?= str_repeat('★', $rec['leadership']) ?><?= str_repeat('☆', 5 - $rec['leadership']) ?></span>
                    </div>
                    <div class="rating-item">
                        <span>Direction de projet</span>
                        <span class="stars"><?= str_repeat('★', $rec['project_management']) ?><?= str_repeat('☆', 5 - $rec['project_management']) ?></span>
                    </div>
                    <div class="rating-item">
                        <span>Stratégie</span>
                        <span class="stars"><?= str_repeat('★', $rec['strategy']) ?><?= str_repeat('☆', 5 - $rec['strategy']) ?></span>
                    </div>
                    <div class="rating-item">
                        <span>Méthodologie</span>
                        <span class="stars"><?= str_repeat('★', $rec['methodology']) ?><?= str_repeat('☆', 5 - $rec['methodology']) ?></span>
                    </div>
                    <div class="rating-item">
                        <span>Gouvernance</span>
                        <span class="stars"><?= str_repeat('★', $rec['governance']) ?><?= str_repeat('☆', 5 - $rec['governance']) ?></span>
                    </div>
                    <div class="rating-item" style="grid-column: 1 / -1; border-top: 2px solid #ddd; padding-top: 1rem; margin-top: 0.5rem;">
                        <span><strong>Note moyenne</strong></span>
                        <span style="font-size: 1.5rem; font-weight: 600; color: #d97706;"><?= number_format($rec['average_rating'], 1) ?>/5</span>
                    </div>
                </div>
                
                <div class="testimonial">
                    <p><?= nl2br(htmlspecialchars($rec['testimonial'])) ?></p>
                </div>
                
                <?php if ($rec['photo_path']): ?>
                    <p><strong>📸 Photo :</strong> <a href="../<?= htmlspecialchars($rec['photo_path']) ?>" target="_blank">Voir la photo</a></p>
                <?php endif; ?>
                
                <div class="actions">
                    <?php if ($rec['status'] === 'pending'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $rec['id'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-approve">✅ Approuver</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $rec['id'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-reject">❌ Rejeter</button>
                        </form>
                    <?php endif; ?>
                    
                    <?php if ($rec['status'] === 'approved'): ?>
                        <button onclick="toggleHTML(<?= $rec['id'] ?>)" class="btn btn-html">📋 Générer HTML</button>
                    <?php endif; ?>
                    
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette recommandation ?');">
                        <input type="hidden" name="id" value="<?= $rec['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn btn-delete">🗑️ Supprimer</button>
                    </form>
                </div>
                
                <?php if ($rec['status'] === 'approved'): ?>
                    <div id="html-<?= $rec['id'] ?>" class="html-output">
                        <pre><?= htmlspecialchars(generateHTML($rec)) ?></pre>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <script>
        function toggleHTML(id) {
            const element = document.getElementById('html-' + id);
            element.style.display = element.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>

<?php
$conn->close();

function generateHTML($rec) {
    $stars = function($rating) {
        $filled = str_repeat('<span class="star filled">★</span>', $rating);
        $empty = str_repeat('<span class="star">☆</span>', 5 - $rating);
        return $filled . $empty;
    };
    
    $html = '<div class="recommandation-card">
    <div class="recommandation-header">
        <div class="recommandation-avatar">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </div>
        <div class="recommandation-info">
            <h3>' . htmlspecialchars($rec['name']) . '</h3>
            <p class="recommandation-position">' . htmlspecialchars($rec['position']) . '</p>
            <p class="recommandation-company">' . htmlspecialchars($rec['company']) . '</p>
        </div>
        <div class="recommandation-rating">
            <div class="rating-number">' . number_format($rec['average_rating'], 1) . '</div>
            <div class="rating-stars">
                ' . $stars(round($rec['average_rating'])) . '
            </div>
        </div>
    </div>
    <div class="recommandation-ratings">
        <div class="rating-item">
            <span class="rating-label">Leadership</span>
            <div class="stars">' . $stars($rec['leadership']) . '</div>
        </div>
        <div class="rating-item">
            <span class="rating-label">Direction de projet</span>
            <div class="stars">' . $stars($rec['project_management']) . '</div>
        </div>
        <div class="rating-item">
            <span class="rating-label">Stratégie</span>
            <div class="stars">' . $stars($rec['strategy']) . '</div>
        </div>
        <div class="rating-item">
            <span class="rating-label">Méthodologie</span>
            <div class="stars">' . $stars($rec['methodology']) . '</div>
        </div>
        <div class="rating-item">
            <span class="rating-label">Gouvernance</span>
            <div class="stars">' . $stars($rec['governance']) . '</div>
        </div>
    </div>
    <div class="recommandation-text">
        <p>"' . nl2br(htmlspecialchars($rec['testimonial'])) . '"</p>
    </div>
</div>';
    
    return $html;
}
?>
