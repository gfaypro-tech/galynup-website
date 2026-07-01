<?php
// =============================================================
// CV Builder — Configuration EXEMPLE
// Copier ce fichier : cp config.example.php config.php
// Puis remplir les valeurs ci-dessous
// =============================================================

// --- Base de données ---
define('CV_DB_HOST', 'localhost');          // OVH : mysql5-xx.perso.ovh.net / Codespace : 127.0.0.1
define('CV_DB_NAME', 'cv_builder');        // Nom de ta base
define('CV_DB_USER', 'root');              // OVH : ton user / Codespace : root
define('CV_DB_PASS', '');                  // Mot de passe

// --- Authentification ---
// Générer le hash via /cv/setup.php
define('CV_USERNAME', 'admin');
define('CV_PASSWORD_HASH', '');            // Ex: $2y$10$...

// --- Session ---
define('CV_SESSION_NAME', 'cv_builder_sess');

// --- URL de base de l'app (sans slash final) ---
// Production OVH : '/cv'
// Codespace     : '/cv' (le port forwarding gère le reste)
define('CV_BASE_URL', '/cv');

// --- App ---
define('CV_VERSION', '1.0');
define('CV_TITLE', 'CV Builder');

// --- Thème ---
// 'default' : palette Galynup (aubergine + gold)
// 'glass'   : glassmorphism noir & blanc
define('CV_THEME', 'default');

// --- API Claude (Option B — désactivée par défaut) ---
// define('CV_ANTHROPIC_KEY', 'sk-ant-...');
// define('CV_MODEL', 'claude-sonnet-4-6');
