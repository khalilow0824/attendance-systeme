<?php
/**
 * ============================================
 * FICHIER 1 : Configuration
 * ============================================
 */

// Configuration de la connexion
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Vide sur WAMP par défaut
define('DB_NAME', 'test_connexion_db');

// Timezone
date_default_timezone_set('Africa/Algiers');

// Mode debug
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
?>