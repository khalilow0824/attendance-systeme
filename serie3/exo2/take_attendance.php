<?php
/**
 * ============================================
 * EXERCICE 2 - PRENDRE LES PRÉSENCES (JSON)
 * ============================================
 */

// Fichiers
$students_file = 'C:/wamp64/www/serie3/exo1/students.json'; // Utilise les étudiants de l'exo 1
$attendance_dir = 'attendance/';
$today = date('Y-m-d');
$attendance_file = $attendance_dir . "attendance_$today.json";

// Créer le dossier attendance s'il n'existe pas
if (!file_exists($attendance_dir)) {
    mkdir($attendance_dir, 0777, true);
}

// ============================================
// TRAITEMENT DU FORMULAIRE (POST)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Vérifier si les présences ont déjà été prises aujourd'hui
    if (file_exists($attendance_file)) {
        $error_message = "⚠️ Les présences pour aujourd'hui ont déjà été enregistrées.";
    } else {
        // Récupérer les données
        $attendance = [];
        
        foreach ($_POST['status'] as $student_id => $status) {
            $attendance[] = [
                'student_id' => $student_id,
                'status' => $status
            ];
        }
        
        // Préparer les données à sauvegarder
        $attendance_data = [
            'date' => $today,
            'time' => date('H:i:s'),
            'attendance' => $attendance
        ];
        
        // Sauvegarder dans le fichier
        $json_output = json_encode($attendance_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        if (file_put_contents($attendance_file, $json_output)) {
            $success_message = "✅ Présences enregistrées avec succès !";
        } else {
            $error_message = "❌ Erreur lors de l'enregistrement";
        }
    }
}

// Charger les étudiants
$students = [];
if (file_exists($students_file)) {
    $students = json_decode(file_get_contents($students_file), true) ?? [];
}

// Vérifier si déjà enregistré aujourd'hui
$already_taken = file_exists($attendance_file);
$attendance_records = [];

if ($already_taken) {
    $data = json_decode(file_get_contents($attendance_file), true);
    $attendance_records = $data['attendance'] ?? [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Exercice 2 - Prendre les présences</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 { color: #5b2c6f; text-align: center; }
        .date-info { background: #f0f0f0; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold; }
        .success { padding: 15px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px; }
        .error { padding: 15px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 15px; }
        .warning { padding: 15px; background: #fff3cd; color: #856404; border-radius: 5px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #5b2c6f; color: white; padding: 12px; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f5f5f5; }
        .radio-group { display: flex; gap: 20px; }
        .radio-group label { cursor: pointer; }
        button { width: 100%; padding: 15px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Exercice 2 - Prendre les présences</h1>
        
        <div class="date-info">
            📅 Date: <?php echo date('d/m/Y'); ?> | 🕐 Heure: <?php echo date('H:i'); ?>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <?php if (empty($students)): ?>
            <div class="warning">
                ⚠️ Aucun étudiant trouvé. Veuillez d'abord ajouter des étudiants dans l'exercice 1.
            </div>
        <?php elseif ($already_taken): ?>
            <div class="warning">
                ⚠️ Les présences pour aujourd'hui ont déjà été enregistrées.
            </div>
            
            <h3>Présences enregistrées :</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Statut</th>
                </tr>
                <?php foreach ($attendance_records as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['student_id']); ?></td>
                        <td style="color: <?php echo $record['status'] === 'present' ? 'green' : 'red'; ?>; font-weight: bold;">
                            <?php echo $record['status'] === 'present' ? '✓ Présent' : '✗ Absent'; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <form method="POST">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Groupe</th>
                        <th>Statut</th>
                    </tr>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['group']); ?></td>
                            <td>
                                <div class="radio-group">
                                    <label>
                                        <input type="radio" name="status[<?php echo $student['student_id']; ?>]" value="present" checked>
                                        ✓ Présent
                                    </label>
                                    <label>
                                        <input type="radio" name="status[<?php echo $student['student_id']; ?>]" value="absent">
                                        ✗ Absent
                                    </label>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                
                <button type="submit">💾 Enregistrer les présences</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>