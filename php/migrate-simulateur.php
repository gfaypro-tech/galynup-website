<?php
/**
 * Migration — Ajout du champ level à simulateur_tokens.
 * À exécuter UNE SEULE FOIS puis supprimer.
 *
 * Accès : https://galynup.fr/php/migrate-simulateur.php?key=VOTRE_ADMIN_SECRET
 */

require_once __DIR__ . '/config.php';

if (!isset($_GET['key']) || $_GET['key'] !== ADMIN_SECRET) {
    http_response_code(403);
    die('Accès refusé.');
}

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) die('Erreur DB : ' . $db->connect_error);
$db->set_charset('utf8mb4');

// Vérifier si la colonne existe déjà
$check = $db->query("SHOW COLUMNS FROM simulateur_tokens LIKE 'level'");
if ($check && $check->num_rows > 0) {
    echo '✅ Colonne <code>level</code> déjà présente — rien à faire.<br>';
    echo '⚠️ <strong>Supprimez ce fichier maintenant.</strong>';
} else {
    $sql = "ALTER TABLE simulateur_tokens ADD COLUMN level VARCHAR(10) NOT NULL DEFAULT 'free'";
    if ($db->query($sql)) {
        echo '✅ Migration réussie — colonne <code>level</code> ajoutée.<br>';
        echo '⚠️ <strong>Supprimez ce fichier maintenant.</strong>';
    } else {
        echo '❌ Erreur : ' . $db->error;
    }
}
$db->close();
