<?php
/**
 * API - Sauvegarder les présences (Version MySQL)
 */
require_once 'db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$input = json_decode(file_get_contents('php://input'), true);
$attendanceData = $input['attendance'] ?? [];
$sessionDate = date('Y-m-d');

if (empty($attendanceData)) {
    echo json_encode(['success' => false, 'message' => 'Aucune donnée fournie']);
    exit;
}

try {
    $conn = getConnection();
    $conn->beginTransaction();
    
    // Pour chaque étudiant
    foreach ($attendanceData as $studentData) {
        $student_id = ''; // Nous devons trouver l'ID étudiant
        
        // Trouver l'ID étudiant par nom/prénom (temporaire - à améliorer)
        $find_stmt = $conn->prepare("SELECT student_id FROM students WHERE last_name = ? AND first_name = ? LIMIT 1");
        $find_stmt->execute([$studentData['lastName'], $studentData['firstName']]);
        $student = $find_stmt->fetch();
        
        if ($student) {
            $student_id = $student['student_id'];
            
            // Pour chaque session (1 à 6)
            foreach ($studentData['sessions'] as $sessionIndex => $session) {
                $sessionNumber = $sessionIndex + 1;
                
                // Vérifier si l'entrée existe déjà
                $check_stmt = $conn->prepare("SELECT id FROM attendance WHERE student_id = ? AND session_date = ? AND session_number = ?");
                $check_stmt->execute([$student_id, $sessionDate, $sessionNumber]);
                
                if ($check_stmt->fetch()) {
                    // Mettre à jour
                    $update_stmt = $conn->prepare("UPDATE attendance SET presence = ?, participation = ? WHERE student_id = ? AND session_date = ? AND session_number = ?");
                    $update_stmt->execute([$session['presence'], $session['participation'], $student_id, $sessionDate, $sessionNumber]);
                } else {
                    // Insérer
                    $insert_stmt = $conn->prepare("INSERT INTO attendance (student_id, session_date, session_number, presence, participation) VALUES (?, ?, ?, ?, ?)");
                    $insert_stmt->execute([$student_id, $sessionDate, $sessionNumber, $session['presence'], $session['participation']]);
                }
            }
        }
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Présences sauvegardées en base de données',
        'date' => $sessionDate,
        'count' => count($attendanceData)
    ]);
    
} catch (PDOException $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()]);
}
?>