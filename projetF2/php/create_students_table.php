<?php
require_once 'db_connect.php';

try {
    $conn = getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(50) UNIQUE NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL,
        group_name VARCHAR(20) DEFAULT 'G1',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "✅ Table 'students' créée avec succès";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
?>