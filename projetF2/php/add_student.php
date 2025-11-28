<?php
/**
 * API - Ajouter un étudiant (Version MySQL - Corrigée)
 */
require_once 'db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Attendre les données JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Aucune donnée reçue']);
    exit;
}

$student_id = trim($input['student_id'] ?? '');
$lastName = trim($input['lastName'] ?? '');
$firstName = trim($input['firstName'] ?? '');
$email = trim($input['email'] ?? '');

// Validation
$errors = [];
if (empty($student_id) || !preg_match("/^[0-9]+$/", $student_id)) {
    $errors[] = "ID invalide (chiffres seulement)";
}
if (empty($lastName) || !preg_match("/^[a-zA-ZÀ-ÿ\s\-]+$/u", $lastName)) {
    $errors[] = "Nom invalide (lettres seulement)";
}
if (empty($firstName) || !preg_match("/^[a-zA-ZÀ-ÿ\s\-]+$/u", $firstName)) {
    $errors[] = "Prénom invalide (lettres seulement)";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    $conn = getConnection();
    
    // Vérifier si l'ID existe déjà
    $check_stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
    $check_stmt->execute([$student_id]);
    
    if ($check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Cet ID étudiant existe déjà']);
        exit;
    }
    
    // Insérer le nouvel étudiant
    $stmt = $conn->prepare("INSERT INTO students (student_id, last_name, first_name, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$student_id, $lastName, $firstName, $email]);
    
    $new_id = $conn->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Étudiant ajouté avec succès en base de données',
        'student' => [
            'id' => $new_id,
            'student_id' => $student_id,
            'lastName' => $lastName,
            'firstName' => $firstName,
            'email' => $email
        ]
    ]);
    
} catch (PDOException $e) {
    // Journaliser l'erreur détaillée
    error_log("Erreur add_student: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur base de données: ' . $e->getMessage(),
        'debug' => 'Vérifiez que la table students existe avec les colonnes: student_id, last_name, first_name, email'
    ]);
}
?>