<?php
/**
 * Initialisation de la table des tokens d'accès au Simulateur LLM.
 * À exécuter UNE SEULE FOIS via le navigateur, puis supprimer ce fichier.
 *
 * Accès : https://galynup.fr/php/init-simulateur.php?key=VOTRE_ADMIN_SECRET
 */

require_once __DIR__ . '/config.php';

if (!isset($_GET['key']) || $_GET['key'] !== ADMIN_SECRET) {
    http_response_code(403);
    die('Accès refusé.');
}

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    die('Erreur connexion DB : ' . $db->connect_error);
}
$db->set_charset('utf8mb4');

$sql = "CREATE TABLE IF NOT EXISTS simulateur_tokens (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    token        VARCHAR(64)  UNIQUE NOT NULL,
    email        VARCHAR(255) NOT NULL,
    name         VARCHAR(255) NOT NULL,
    created_at   DATETIME     NOT NULL,
    first_used_at DATETIME    NULL,
    last_used_at DATETIME     NULL,
    use_count    INT          DEFAULT 0,
    active       TINYINT(1)  DEFAULT 1,
    INDEX idx_token (token),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($db->query($sql)) {
    echo '✅ Table <code>simulateur_tokens</code> créée avec succès.<br>';
    echo '⚠️ <strong>Supprimez ce fichier maintenant.</strong>';
} else {
    echo '❌ Erreur : ' . $db->error;
}

$db->close();
