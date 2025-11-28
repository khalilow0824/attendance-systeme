<?php
require_once 'db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$session_id = intval($input['session_id'] ?? 0);

if ($session_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de session invalide']);
    exit;
}

try {
    $conn = getConnection();
    
    // Vérifier si la session existe et est ouverte
    $check_stmt = $conn->prepare("SELECT id FROM attendance_sessions WHERE id = ? AND status = 'open'");
    $check_stmt->execute([$session_id]);
    
    if (!$check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Session non trouvée ou déjà fermée']);
        exit;
    }
    
    // Fermer la session
    $stmt = $conn->prepare("UPDATE attendance_sessions SET status = 'closed', closed_at = NOW() WHERE id = ?");
    $stmt->execute([$session_id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Session fermée avec succès'
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
}
?>