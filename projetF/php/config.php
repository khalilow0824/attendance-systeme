<?php
/**
 * ============================================
 * FICHIER 1 : php/config.php
 * ============================================
 */

// Configuration (non utilisée dans cette version JSON, mais prête pour MySQL)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'attendance_db');

date_default_timezone_set('Africa/Algiers');
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
?>


<?php
/**
 * ============================================
 * FICHIER 2 : php/add_student.php
 * ============================================
 * API - Ajouter un étudiant
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


<?php
/**
 * ============================================
 * FICHIER 3 : php/get_students.php
 * ============================================
 * API - Récupérer tous les étudiants
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$students_file = '../data/students.json';

// Vérifier si le fichier existe
if (!file_exists($students_file)) {
    // Créer un fichier vide si inexistant
    file_put_contents($students_file, '[]');
    echo json_encode([
        'success' => true,
        'students' => [],
        'count' => 0
    ]);
    exit;
}

// Lire le fichier
$json_content = file_get_contents($students_file);
$students = json_decode($json_content, true);

// Vérifier si le JSON est valide
if ($students === null) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur de lecture du fichier JSON'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'students' => $students,
    'count' => count($students)
]);
?>


<?php
/**
 * ============================================
 * FICHIER 4 : php/save_attendance.php
 * ============================================
 * API - Sauvegarder les présences
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$attendance_dir = '../data/attendance/';
$today = date('Y-m-d');
$attendance_file = $attendance_dir . "attendance_$today.json";

// Créer le dossier si nécessaire
if (!file_exists($attendance_dir)) {
    mkdir($attendance_dir, 0777, true);
}

// Vérifier si déjà enregistré aujourd'hui
if (file_exists($attendance_file)) {
    echo json_encode([
        'success' => false,
        'message' => 'Les présences pour aujourd\'hui ont déjà été enregistrées'
    ]);
    exit;
}

// Récupérer les données
$input = json_decode(file_get_contents('php://input'), true);
$attendanceData = $input['attendance'] ?? [];

if (empty($attendanceData)) {
    echo json_encode([
        'success' => false,
        'message' => 'Aucune donnée fournie'
    ]);
    exit;
}

// Préparer les données à sauvegarder
$data = [
    'date' => $today,
    'time' => date('H:i:s'),
    'attendance' => $attendanceData
];

// Sauvegarder
$json_output = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if (file_put_contents($attendance_file, $json_output)) {
    echo json_encode([
        'success' => true,
        'message' => 'Présences enregistrées avec succès',
        'date' => $today,
        'file' => basename($attendance_file)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la sauvegarde'
    ]);
}
?>