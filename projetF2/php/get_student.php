<?php
/**
 * API - Récupérer tous les étudiants (Version Simplifiée)
 */
require_once 'db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $conn = getConnection();
    
    // Essayer d'abord avec la nouvelle structure
    try {
        $stmt = $conn->query("SELECT id, student_id, last_name as lastName, first_name as firstName, email, group_name as group FROM students ORDER BY last_name, first_name");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        // Si échec, essayer avec l'ancienne structure
        $stmt = $conn->query("SELECT * FROM students ORDER BY fullname");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Adapter les noms de colonnes
        foreach ($students as &$student) {
            if (isset($student['fullname'])) {
                $names = explode(' ', $student['fullname'], 2);
                $student['firstName'] = $names[0] ?? '';
                $student['lastName'] = $names[1] ?? '';
            }
            if (isset($student['matricule'])) {
                $student['student_id'] = $student['matricule'];
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'students' => $students,
        'count' => count($students),
        'message' => count($students) . ' étudiants chargés depuis MySQL'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur base de données: ' . $e->getMessage(),
        'students' => []
    ]);
}
?>