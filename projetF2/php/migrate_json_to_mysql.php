<?php
require_once 'db_connect.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== MIGRATION JSON → MySQL ===\n\n";

$json_file = __DIR__ . '/../data/students.json';

if (!file_exists($json_file)) {
    echo "❌ Fichier students.json non trouvé\n";
    exit;
}

$content = file_get_contents($json_file);
$students = json_decode($content, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "❌ Erreur JSON: " . json_last_error_msg() . "\n";
    exit;
}

echo "📊 " . count($students) . " étudiants trouvés dans le JSON\n\n";

try {
    $conn = getConnection();
    $count = 0;
    
    foreach ($students as $student) {
        // Vérifier si l'étudiant existe déjà
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
        $check_stmt->execute([$student['student_id']]);
        
        if (!$check_stmt->fetch()) {
            // Insérer l'étudiant
            $stmt = $conn->prepare("INSERT INTO students (student_id, last_name, first_name, email, group_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $student['student_id'],
                $student['lastName'],
                $student['firstName'],
                $student['email'],
                $student['group'] ?? 'G1'
            ]);
            $count++;
            echo "✅ Migré: {$student['firstName']} {$student['lastName']}\n";
        } else {
            echo "⚠️ Déjà existant: {$student['firstName']} {$student['lastName']}\n";
        }
    }
    
    echo "\n🎉 Migration terminée: {$count} nouveaux étudiants ajoutés à MySQL\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur MySQL: " . $e->getMessage() . "\n";
}
?>