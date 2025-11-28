<?php
/**
 * API - Sauvegarder les présences (MODE JSON)
 * Exercice 2
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