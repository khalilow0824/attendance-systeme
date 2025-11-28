<?php
/**
 * API - Ajouter un étudiant (MODE JSON)
 * Exercice 1
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$students_file = '../data/students.json';

// Récupérer les données JSON
$input = json_decode(file_get_contents('php://input'), true);

$student_id = trim($input['student_id'] ?? '');
$lastName = trim($input['lastName'] ?? '');
$firstName = trim($input['firstName'] ?? '');
$email = trim($input['email'] ?? '');

// Validation
$errors = [];

if (empty($student_id) || !is_numeric($student_id)) {
    $errors[] = "ID invalide (doit être numérique)";
}

if (empty($lastName) || !preg_match("/^[a-zA-ZÀ-ÿ\s]+$/u", $lastName)) {
    $errors[] = "Nom invalide (lettres uniquement)";
}

if (empty($firstName) || !preg_match("/^[a-zA-ZÀ-ÿ\s]+$/u", $firstName)) {
    $errors[] = "Prénom invalide (lettres uniquement)";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide";
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreurs de validation',
        'errors' => $errors
    ]);
    exit;
}

// Charger les étudiants existants
$students = [];
if (file_exists($students_file)) {
    $json_content = file_get_contents($students_file);
    $students = json_decode($json_content, true) ?? [];
}

// Vérifier doublon
foreach ($students as $student) {
    if ($student['student_id'] == $student_id) {
        echo json_encode([
            'success' => false,
            'message' => "Un étudiant avec l'ID $student_id existe déjà"
        ]);
        exit;
    }
}

// Ajouter le nouvel étudiant
$newStudent = [
    'student_id' => $student_id,
    'lastName' => $lastName,
    'firstName' => $firstName,
    'email' => $email,
    'created_at' => date('Y-m-d H:i:s')
];

$students[] = $newStudent;

// Sauvegarder
$json_output = json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if (file_put_contents($students_file, $json_output)) {
    echo json_encode([
        'success' => true,
        'message' => 'Étudiant ajouté avec succès',
        'student' => $newStudent
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la sauvegarde'
    ]);
}
?>