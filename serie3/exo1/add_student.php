<?php
/**
 * ============================================
 * EXERCICE 1 - AJOUTER UN ÉTUDIANT (JSON)
 * ============================================
 */

// Fichier JSON où on va sauvegarder
$students_file = 'students.json';




// ============================================
// FONCTION DE VALIDATION
// ============================================
function validateStudent($student_id, $name, $group) {
    $errors = [];
    
    // Validation de l'ID (doit être numérique)
    if (empty($student_id)) {
        $errors[] = "L'ID étudiant est requis";
    } elseif (!is_numeric($student_id)) {
        $errors[] = "L'ID doit contenir uniquement des chiffres";
    }
    
    // Validation du nom (lettres uniquement)
    if (empty($name)) {
        $errors[] = "Le nom est requis";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/u", $name)) {
        $errors[] = "Le nom doit contenir uniquement des lettres";
    }
    
    // Validation du groupe
    if (empty($group)) {
        $errors[] = "Le groupe est requis";
    }
    
    return $errors;
}

// ============================================
// TRAITEMENT DU FORMULAIRE (POST)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Récupérer les données
    $student_id = trim($_POST['student_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $group = trim($_POST['group'] ?? '');
    
    // 2. Valider
    $errors = validateStudent($student_id, $name, $group);
    
    if (!empty($errors)) {
        $error_message = implode('<br>', $errors);
    } else {
        // 3. Charger les étudiants existants
        $students = [];
        if (file_exists($students_file)) {
            $json_content = file_get_contents($students_file);
            $students = json_decode($json_content, true) ?? [];
        }
        
        // 4. Vérifier si l'étudiant existe déjà
        $exists = false;
        foreach ($students as $student) {
            if ($student['student_id'] == $student_id) {
                $exists = true;
                break;
            }
        }
        
        if ($exists) {
            $error_message = "Un étudiant avec l'ID $student_id existe déjà";
        } else {
            // 5. Ajouter le nouvel étudiant
            $new_student = [
                'student_id' => $student_id,
                'name' => $name,
                'group' => $group,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $students[] = $new_student;
            
            // 6. Sauvegarder dans le fichier
            $json_output = json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            if (file_put_contents($students_file, $json_output)) {
                $success_message = "✅ Étudiant ajouté avec succès !";
                $student_id = $name = $group = '';
            } else {
                $error_message = "❌ Erreur lors de la sauvegarde";
            }
        }
    }
}

// Charger les étudiants pour affichage
$students = [];
if (file_exists($students_file)) {
    $students = json_decode(file_get_contents($students_file), true) ?? [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Exercice 1 - Ajouter un étudiant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
        .success { padding: 15px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px; }
        .error { padding: 15px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #5b2c6f; color: white; padding: 10px; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Exercice 1 - Ajouter un étudiant</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>ID Étudiant</label>
                <input type="text" name="student_id" value="<?php echo htmlspecialchars($student_id ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Nom complet</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Groupe</label>
                <input type="text" name="group" value="<?php echo htmlspecialchars($group ?? ''); ?>" required>
            </div>
            
            <button type="submit">Ajouter</button>
        </form>
        
        <h2>Liste des étudiants (<?php echo count($students); ?>)</h2>
        
        <?php if (empty($students)): ?>
            <p style="text-align: center; color: #999;">Aucun étudiant pour le moment</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Groupe</th>
                    <th>Ajouté le</th>
                </tr>
                <?php foreach ($students as $i => $s): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo htmlspecialchars($s['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($s['name']); ?></td>
                        <td><?php echo htmlspecialchars($s['group']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($s['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>