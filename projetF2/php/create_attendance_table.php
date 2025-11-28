<?php
require_once 'db_connect.php';

try {
    $conn = getConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(50) NOT NULL,
        session_date DATE NOT NULL,
        session_number INT NOT NULL,
        presence BOOLEAN DEFAULT FALSE,
        participation BOOLEAN DEFAULT FALSE,
        recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(student_id),
        INDEX idx_student_date (student_id, session_date)
    )";
    
    $conn->exec($sql);
    echo "✅ Table 'attendance' créée avec succès";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage();
}
?>