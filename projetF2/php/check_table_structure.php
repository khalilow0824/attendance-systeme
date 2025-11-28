<?php
require_once 'db_connect.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $conn = getConnection();
    
    echo "=== STRUCTURE DE LA TABLE STUDENTS ===\n\n";
    
    // Vérifier si la table existe
    $stmt = $conn->query("SHOW TABLES LIKE 'students'");
    if (!$stmt->fetch()) {
        echo "❌ La table 'students' n'existe pas\n";
        exit;
    }
    
    // Afficher la structure
    $stmt = $conn->query("DESCRIBE students");
    $structure = $stmt->fetchAll();
    
    echo "Colonnes de la table 'students':\n";
    foreach ($structure as $col) {
        echo "• {$col['Field']} - {$col['Type']} - {$col['Null']} - {$col['Key']}\n";
    }
    
    echo "\n=== DONNÉES EXISTANTES ===\n";
    $stmt = $conn->query("SELECT * FROM students LIMIT 3");
    $data = $stmt->fetchAll();
    
    if (empty($data)) {
        echo "Aucune donnée dans la table\n";
    } else {
        foreach ($data as $row) {
            print_r($row);
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>