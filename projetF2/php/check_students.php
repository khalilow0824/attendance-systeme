<?php
header('Content-Type: text/plain');
$students_file = __DIR__ . '/../data/students.json';

if (!file_exists($students_file)) {
    echo "❌ Fichier students.json n'existe pas\n";
    exit;
}

$content = file_get_contents($students_file);
$students = json_decode($content, true);

echo "📊 Contenu du fichier students.json:\n";
echo "Taille du fichier: " . strlen($content) . " bytes\n";
echo "Nombre d'étudiants: " . count($students) . "\n\n";

print_r($students);
?>