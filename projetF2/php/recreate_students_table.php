<?php
require_once 'db_connect.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $conn = getConnection();
    
    echo "=== RECRÉATION DE LA TABLE STUDENTS ===\n\n";
    
    // Sauvegarder les données existantes si nécessaire
    $conn->query("CREATE TABLE IF NOT EXISTS students_backup AS SELECT * FROM students");
    echo "✅ Sauvegarde créée (students_backup)\n";
    
    // Supprimer l'ancienne table
    $conn->query("DROP TABLE IF EXISTS students");
    echo "✅ Ancienne table supprimée\n";
    
    // Créer la nouvelle table avec la bonne structure
    $sql = "CREATE TABLE students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(50) UNIQUE NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL,
        group_name VARCHAR(20) DEFAULT 'G1',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "✅ Nouvelle table 'students' créée avec succès\n";
    
    // Vérifier la structure
    $stmt = $conn->query("DESCRIBE students");
    $structure = $stmt->fetchAll();
    
    echo "\nNouvelle structure:\n";
    foreach ($structure as $col) {
        echo "• {$col['Field']} - {$col['Type']}\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?>