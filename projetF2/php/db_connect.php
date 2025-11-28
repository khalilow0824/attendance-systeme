<?php
/**
 * ============================================
 * FICHIER 2 : Connexion à la base de données
 * ============================================
 */

require_once 'config.php';

/**
 * Crée et retourne une connexion PDO
 * @return PDO
 * @throws PDOException
 */
function getConnection() {
    static $conn = null;
    
    if ($conn !== null) {
        return $conn;
    }
    
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        return $conn;
        
    } catch (PDOException $e) {
        // Logger l'erreur dans un fichier
        $logFile = __DIR__ . '/logs/db_errors.log';
        $logDir = dirname($logFile);
        
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $errorMessage = date('Y-m-d H:i:s') . " - ERREUR DB: " . $e->getMessage() . "\n";
        file_put_contents($logFile, $errorMessage, FILE_APPEND);
        
        // Relancer l'exception
        throw new PDOException("Erreur de connexion à la base de données");
    }
}

/**
 * Test de connexion
 * Décommentez pour tester
 */
/*
try {
    $conn = getConnection();
    echo "✅ Connexion réussie à la base de données : " . DB_NAME;
} catch (PDOException $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage();
}
*/
?>