<?php
/**
 * ============================================
 * FICHIER 2 : Connexion avec try/catch
 * ============================================
 */

require_once 'config.php';

/**
 * Fonction pour se connecter à la BDD
 */
function getDBConnection() {
    try {
        // DSN (Data Source Name)
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        // Options PDO
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        // Créer la connexion
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        return $pdo;
        
    } catch (PDOException $e) {
        // Créer le dossier logs
        $log_dir = __DIR__ . '/logs';
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0777, true);
        }
        
        // Logger l'erreur
        $log_file = $log_dir . '/db_errors.log';
        $log_message = date('Y-m-d H:i:s') . " - Erreur: " . $e->getMessage() . "\n";
        error_log($log_message, 3, $log_file);
        
        // Afficher l'erreur
        if (DEBUG_MODE) {
            die("❌ Erreur de connexion: " . $e->getMessage());
        } else {
            die("❌ Erreur de connexion à la base de données.");
        }
    }
}

/**
 * Tester la connexion
 */
function testConnection() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT VERSION() as version");
        $result = $stmt->fetch();
        
        return [
            'success' => true,
            'message' => 'Connexion réussie!',
            'version' => $result['version'],
            'database' => DB_NAME
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Échec: ' . $e->getMessage()
        ];
    }
}
?>