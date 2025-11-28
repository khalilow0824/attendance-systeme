<?php
require_once 'db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$course_id = trim($input['course_id'] ?? '');
$group_id = trim($input['group_id'] ?? '');
$opened_by = trim($input['opened_by'] ?? '');

// Validation
if (empty($course_id) || empty($group_id) || empty($opened_by)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont requis']);
    exit;
}

try {
    $conn = getConnection();
    
    // Vérifier si une session est déjà ouverte pour ce cours/groupe aujourd'hui
    $check_stmt = $conn->prepare("SELECT id FROM attendance_sessions WHERE course_id = ? AND group_id = ? AND date = CURDATE() AND status = 'open'");
    $check_stmt->execute([$course_id, $group_id]);
    
    if ($check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Une session est déjà ouverte pour ce cours/groupe aujourd\'hui']);
        exit;
    }
    
    // Créer la nouvelle session
    $stmt = $conn->prepare("INSERT INTO attendance_sessions (course_id, group_id, date, opened_by, status) VALUES (?, ?, CURDATE(), ?, 'open')");
    $stmt->execute([$course_id, $group_id, $opened_by]);
    
    $session_id = $conn->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Session créée avec succès',
        'session_id' => $session_id
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?>