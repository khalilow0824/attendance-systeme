<?php
/**
 * Initialisation des données
 */
$data_dir = __DIR__ . '/../data';
$students_file = $data_dir . '/students.json';

// Créer le dossier data s'il n'existe pas
if (!file_exists($data_dir)) {
    mkdir($data_dir, 0777, true);
    echo "Dossier data créé: $data_dir\n";
}

// Créer le fichier students.json s'il n'existe pas
if (!file_exists($students_file)) {
    file_put_contents($students_file, '[]');
    echo "Fichier students.json créé: $students_file\n";
}

echo "✅ Initialisation terminée";
?>