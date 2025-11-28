<?php
/**
 * Gestion des sessions de présence avec MySQL
 * Exercice 5
 */

require_once 'config.php';
require_once 'db_connect.php';

header('Content-Type: application/json');

$pdo = getDBConnection();
$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        
        // Créer une session
        case 'create':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $course_id = intval($input['course_id'] ?? 0);
            $group_id = intval($input['group_id'] ?? 0);
            $professor_id = intval($input['professor_id'] ?? 0);
            $date = $input['date'] ?? date('Y-m-d');
            
            $stmt = $pdo->prepare("
                INSERT INTO attendance_sessions 
                (course_id, group_id, date, opened_by, status) 
                VALUES (?, ?, ?, ?, 'open')
            ");
            $stmt->execute([$course_id, $group_id, $date, $professor_id]);
            
            echo json_encode([
                'success' => true,
                'session_id' => $pdo->lastInsertId()
            ]);
            break;
        
        // Fermer une session
        case 'close':
            $session_id = intval($_GET['session_id'] ?? 0);
            
            $stmt = $pdo->prepare("
                UPDATE attendance_sessions 
                SET status = 'closed', closed_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$session_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Session fermée'
            ]);
            break;
        
        // Lister les sessions
        case 'list':
            $stmt = $pdo->query("
                SELECT * FROM attendance_sessions 
                ORDER BY date DESC
            ");
            $sessions = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'sessions' => $sessions
            ]);
            break;
        
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Action inconnue'
            ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>