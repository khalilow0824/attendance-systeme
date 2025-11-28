<?php
/**
 * API - Récupérer tous les étudiants (MODE JSON)
 * Exercice 1-2
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