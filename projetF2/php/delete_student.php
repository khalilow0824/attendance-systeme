<?php
require_once 'db_connect.php';

$message = '';
$error = '';
$student = null;

// Récupérer l'étudiant à supprimer
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

// Traiter la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $id = $_POST['id'] ?? '';
    
    try {
        $conn = getConnection();
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        
        $message = "Étudiant supprimé avec succès !";
        $student = null;
        
        // Redirection après 2 secondes
        header("Refresh: 2; URL=list_students.php");
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un étudiant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .warning-box {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .student-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .student-info p {
            margin: 8px 0;
        }
        button {
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            margin-right: 10px;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
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
    <h1>Supprimer un étudiant</h1>
    
    <?php if ($message): ?>
        <div class="message success">
            <?php echo htmlspecialchars($message); ?>
            <br>Redirection vers la liste...
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($student && !$message): ?>
        <div class="warning-box">
            <strong>⚠️ Attention !</strong>
            <p>Vous êtes sur le point de supprimer cet étudiant de manière permanente.</p>
            <p>Cette action est irréversible.</p>
        </div>
        
        <div class="student-info">
            <h3>Informations de l'étudiant :</h3>
            <p><strong>ID :</strong> <?php echo htmlspecialchars($student['id']); ?></p>
            <p><strong>Nom complet :</strong> <?php echo htmlspecialchars($student['fullname']); ?></p>
            <p><strong>Matricule :</strong> <?php echo htmlspecialchars($student['matricule']); ?></p>
            <p><strong>Groupe :</strong> <?php echo htmlspecialchars($student['group_id']); ?></p>
        </div>
        
        <form method="POST" action="" onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer cet étudiant ?');">
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
            <input type="hidden" name="confirm" value="1">
            
            <button type="submit" class="btn-danger">Confirmer la suppression</button>
            <a href="list_students.php"><button type="button" class="btn-secondary">Annuler</button></a>
        </form>
    <?php endif; ?>
    
    <div class="links">
        <a href="list_students.php">← Retour à la liste</a>
    </div>
</body>
</html>