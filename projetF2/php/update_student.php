<?php
require_once 'db_connect.php';

$message = '';
$error = '';
$student = null;

// Récupérer l'étudiant à modifier
if (isset($_GET['id'])) {
    try {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $student = $stmt->fetch();
        
        if (!$student) {
            $error = "Étudiant non trouvé.";
        }
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}

// Traiter la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $fullname = trim($_POST['fullname'] ?? '');
    $matricule = trim($_POST['matricule'] ?? '');
    $group_id = trim($_POST['group_id'] ?? '');
    
    // Validation
    if (empty($fullname) || empty($matricule) || empty($group_id)) {
        $error = "Tous les champs sont requis.";
    } else {
        try {
            $conn = getConnection();
            
            // Vérifier les doublons de matricule (sauf pour cet étudiant)
            $check_stmt = $conn->prepare("SELECT id FROM students WHERE matricule = ? AND id != ?");
            $check_stmt->execute([$matricule, $id]);
            
            if ($check_stmt->fetch()) {
                $error = "Ce matricule est déjà utilisé par un autre étudiant.";
            } else {
                // Mettre à jour
                $stmt = $conn->prepare("UPDATE students SET fullname = ?, matricule = ?, group_id = ? WHERE id = ?");
                $stmt->execute([$fullname, $matricule, $group_id, $id]);
                
                $message = "Étudiant modifié avec succès !";
                
                // Recharger les données
                $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
                $stmt->execute([$id]);
                $student = $stmt->fetch();
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la modification : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un étudiant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .links {
            margin-top: 20px;
        }
        .links a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>Modifier un étudiant</h1>
    
    <?php if ($message): ?>
        <div class="message success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($student): ?>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
            
            <div class="form-group">
                <label for="fullname">Nom complet:</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($student['fullname']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="matricule">Matricule:</label>
                <input type="text" id="matricule" name="matricule" value="<?php echo htmlspecialchars($student['matricule']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="group_id">Groupe:</label>
                <select id="group_id" name="group_id" required>
                    <option value="G1" <?php echo $student['group_id'] === 'G1' ? 'selected' : ''; ?>>G1</option>
                    <option value="G2" <?php echo $student['group_id'] === 'G2' ? 'selected' : ''; ?>>G2</option>
                    <option value="G3" <?php echo $student['group_id'] === 'G3' ? 'selected' : ''; ?>>G3</option>
                </select>
            </div>
            
            <button type="submit">Modifier</button>
            <a href="list_students.php"><button type="button">Annuler</button></a>
        </form>
    <?php endif; ?>
    
    <div class="links">
        <a href="list_students.php">← Retour à la liste</a>
    </div>
</body>
</html>