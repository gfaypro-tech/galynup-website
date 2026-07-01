<?php
// =============================================================
// CV Builder — Configuration
// NE PAS COMMITER CE FICHIER (contient les credentials)
// =============================================================

// --- Base de données ---
define('CV_DB_HOST', '');          // Ex OVH : mysql5-xx.perso.ovh.net
define('CV_DB_NAME', '');          // Nom de la base MySQL
define('CV_DB_USER', '');          // Utilisateur MySQL
define('CV_DB_PASS', '');          // Mot de passe MySQL

// --- Authentification ---
// Générer le hash via /[dossier]/setup.php après le premier upload
define('CV_USERNAME', 'admin');    // Identifiant de connexion
define('CV_PASSWORD_HASH', '');    // Hash bcrypt — généré par setup.php

// --- Session ---
define('CV_SESSION_NAME', 'cv_builder_sess');  // Nom unique si plusieurs instances sur le même domaine

// --- URL de base de l'app (sans slash final) ---
// Ex : '/cv-dupont' si le dossier s'appelle cv-dupont sur OVH
define('CV_BASE_URL', '/cv-template');

// --- App ---
define('CV_VERSION', '1.0');
define('CV_TITLE', 'CV Builder');

// --- Thème ---
// 'default' : palette Galynup (aubergine + gold), fond clair
// 'glass'   : glassmorphisme noir & blanc (fond sombre)
// 'dark'    : palette Galynup fond très sombre
define('CV_THEME', 'glass');

// --- API Claude (optionnel — pour passer à l'API directe) ---
// define('CV_ANTHROPIC_KEY', 'sk-ant-...');
// define('CV_MODEL', 'claude-sonnet-4-6');

// --- PDFShift (génération PDF côté serveur, optionnel) ---
// define('PDFSHIFT_API_KEY', 'sk_...');
