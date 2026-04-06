<?php
/**
 * Modèle de configuration — copier ce fichier en config.php
 * et renseigner les vraies valeurs (ne jamais committer config.php).
 */

define('DB_HOST', 'votre-host.mysql.db');
define('DB_NAME', 'votre_base');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');

define('NOTIFICATION_EMAIL', 'votre@email.fr');

// Simulateur LLM — gestion des accès
define('ADMIN_SECRET', 'changez-cette-clé-secrète-longue-et-aléatoire');
define('SITE_URL', 'https://galynup.fr'); // sans slash final

// Audit DSI — analyse IA premium (clé API Anthropic)
define('ANTHROPIC_API_KEY', 'sk-ant-api...');
