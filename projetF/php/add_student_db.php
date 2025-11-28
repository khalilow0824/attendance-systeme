<?php
/**
 * CRUD complet pour les étudiants avec MySQL
 * Exercice 4
 */

require_once 'config.php';
require_once 'db_connect.php';

header('Content-Type: application/json');

$pdo = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        
        // CREATE - Ajouter un étudiant
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $fullname = trim($input['fullname'] ?? '');
            $matricule = trim($input['matricule'] ?? '');
            $group_id = intval($input['group_id'] ?? 1);
            $email = trim($input['email'] ?? '');
            
            if (empty($fullname) || empty($matricule)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Nom et matricule requis'
                ]);
                exit;
            }
            
            // Vérifier doublon
            $stmt = $pdo->prepare("SELECT id FROM students WHERE matricule = ?");
            $stmt->execute([$matricule]);
            if ($stmt->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Ce matricule existe déjà'
                ]);
                exit;
            }
            
            // Insérer
            $stmt = $pdo->prepare("
                INSERT INTO students (fullname, matricule, group_id, email) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$fullname, $matricule, $group_id, $email]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Étudiant ajouté',
                'student_id' => $pdo->lastInsertId()
            ]);
            break;
        
        // READ - Lire les étudiants
        case 'GET':
            $stmt = $pdo->query("
                SELECT s.*, g.group_name 
                FROM students s
                LEFT JOIN `groups` g ON s.group_id = g.id
                ORDER BY s.fullname
            ");
            $students = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'students' => $students,
                'count' => count($students)
            ]);
            break;
        
        // UPDATE - Modifier un étudiant
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $id = intval($input['id'] ?? 0);
            $fullname = trim($input['fullname'] ?? '');
            $matricule = trim($input['matricule'] ?? '');
            $group_id = intval($input['group_id'] ?? 1);
            $email = trim($input['email'] ?? '');
            
            $stmt = $pdo->prepare("
                UPDATE students 
                SET fullname = ?, matricule = ?, group_id = ?, email = ?
                WHERE id = ?
            ");
            $stmt->execute([$fullname, $matricule, $group_id, $email, $id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Étudiant modifié'
            ]);
            break;
        
        // DELETE - Supprimer un étudiant
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = intval($input['id'] ?? 0);
            
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Étudiant supprimé'
            ]);
            break;
        
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Méthode non supportée'
            ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}
?>