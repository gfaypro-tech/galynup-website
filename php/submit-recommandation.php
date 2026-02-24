<?php
/**
 * Script de traitement des recommandations
 * Enregistre les données dans MySQL et envoie une notification email
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configuration de la base de données
// À MODIFIER avec vos identifiants OVH
define('DB_HOST', 'galynusgaellefay.mysql.db');
define('DB_NAME', 'galynusgaellefay');
define('DB_USER', 'galynusgaellefay');
define('DB_PASS', 'Newlife2026et253545y');

// Email de notification
define('NOTIFICATION_EMAIL', 'recommandation@galynup.fr');

// Fonction pour nettoyer les données
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fonction pour valider l'email
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    // Récupérer et valider les données
    $name = clean_input($_POST['name'] ?? '');
    $position = clean_input($_POST['position'] ?? '');
    $company = clean_input($_POST['company'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $leadership = intval($_POST['leadership'] ?? 0);
    $project_management = intval($_POST['project_management'] ?? 0);
    $strategy = intval($_POST['strategy'] ?? 0);
    $methodology = intval($_POST['methodology'] ?? 0);
    $governance = intval($_POST['governance'] ?? 0);
    $testimonial = clean_input($_POST['testimonial'] ?? '');
    $consent = isset($_POST['consent']) ? 1 : 0;
    
    // Validations
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Le nom est requis';
    }
    
    if (empty($position)) {
        $errors[] = 'Le poste est requis';
    }
    
    if (empty($company)) {
        $errors[] = 'L\'entreprise est requise';
    }
    
    if (!empty($email) && !is_valid_email($email)) {
        $errors[] = 'L\'email n\'est pas valide';
    }
    
    if ($leadership < 1 || $leadership > 5) {
        $errors[] = 'La note Leadership doit être entre 1 et 5';
    }
    
    if ($project_management < 1 || $project_management > 5) {
        $errors[] = 'La note Direction de projet doit être entre 1 et 5';
    }
    
    if ($strategy < 1 || $strategy > 5) {
        $errors[] = 'La note Stratégie doit être entre 1 et 5';
    }
    
    if ($methodology < 1 || $methodology > 5) {
        $errors[] = 'La note Méthodologie doit être entre 1 et 5';
    }
    
    if ($governance < 1 || $governance > 5) {
        $errors[] = 'La note Gouvernance doit être entre 1 et 5';
    }
    
    if (strlen($testimonial) < 50) {
        $errors[] = 'Le témoignage doit contenir au moins 50 caractères';
    }
    
    if (!$consent) {
        $errors[] = 'Vous devez accepter le traitement de vos données';
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    }
    
    // Calculer la note moyenne
    $average_rating = ($leadership + $project_management + $strategy + $methodology + $governance) / 5;
    $average_rating = round($average_rating, 1);
    
    // Gérer l'upload de photo
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2 Mo
        
        if (!in_array($_FILES['photo']['type'], $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Format de photo non autorisé (JPG ou PNG uniquement)']);
            exit;
        }
        
        if ($_FILES['photo']['size'] > $max_size) {
            echo json_encode(['success' => false, 'message' => 'La photo est trop volumineuse (max 2 Mo)']);
            exit;
        }
        
        // Créer le dossier uploads s'il n'existe pas
        $upload_dir = '../uploads/recommendations/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Générer un nom de fichier unique
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('recommendation_') . '.' . $extension;
        $photo_path = $upload_dir . $filename;
        
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload de la photo']);
            exit;
        }
        
        // Stocker le chemin relatif
        $photo_path = 'uploads/recommendations/' . $filename;
    }
    
    // Connexion à la base de données
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        error_log('Database connection failed: ' . $conn->connect_error);
        echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
        exit;
    }
    
    $conn->set_charset('utf8mb4');
    
    // Préparer la requête SQL
    $stmt = $conn->prepare("
        INSERT INTO recommendations 
        (name, position, company, email, leadership, project_management, strategy, methodology, governance, average_rating, testimonial, photo_path, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    if (!$stmt) {
        error_log('Prepare failed: ' . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la préparation de la requête']);
        exit;
    }
    
    $stmt->bind_param(
        'ssssiiiiddss',
        $name,
        $position,
        $company,
        $email,
        $leadership,
        $project_management,
        $strategy,
        $methodology,
        $governance,
        $average_rating,
        $testimonial,
        $photo_path
    );
    
    if (!$stmt->execute()) {
        error_log('Execute failed: ' . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement']);
        exit;
    }
    
    $recommendation_id = $conn->insert_id;
    
    $stmt->close();
    $conn->close();
    
    // Envoyer l'email de notification
    $email_subject = '🌟 Nouvelle recommandation en attente de validation';
    $email_body = "
Bonjour,

Une nouvelle recommandation vient d'être soumise !

👤 Auteur : {$name}
🏢 Entreprise : {$company}
💼 Poste : {$position}
📧 Email : " . ($email ?: 'Non renseigné') . "

⭐ Notes :
- Leadership : {$leadership}/5
- Direction de projet : {$project_management}/5
- Stratégie : {$strategy}/5
- Méthodologie : {$methodology}/5
- Gouvernance : {$governance}/5

📊 Note moyenne : {$average_rating}/5

💬 Témoignage :
{$testimonial}

📸 Photo : " . ($photo_path ? 'Oui' : 'Non') . "

🔗 Connectez-vous à votre interface d'administration pour valider cette recommandation :
https://www.galynup.fr/admin/recommandations.php

---
ID de la recommandation : {$recommendation_id}
Date de soumission : " . date('d/m/Y à H:i') . "
";
    
    $headers = "From: noreply@galynup.fr\r\n";
    $headers .= "Reply-To: noreply@galynup.fr\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Envoyer l'email (peut échouer sans bloquer l'enregistrement)
    @mail(NOTIFICATION_EMAIL, $email_subject, $email_body, $headers);
    
    // Réponse de succès
    echo json_encode([
        'success' => true,
        'message' => 'Votre recommandation a été envoyée avec succès ! Elle sera publiée après validation.',
        'recommendation_id' => $recommendation_id
    ]);
    
} catch (Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue. Veuillez réessayer.']);
}
?>
