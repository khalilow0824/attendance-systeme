<?php
require_once 'db_connect.php';

$message = '';
$error = '';

try {
    $conn = getConnection();
    $stmt = $conn->query("SELECT * FROM students ORDER BY fullname");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération : " . $e->getMessage();
    $students = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des étudiants</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .edit {
            background-color: #ffc107;
            color: white;
        }
        .delete {
            background-color: #dc3545;
            color: white;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .links {
            margin-bottom: 20px;
        }
        .links a {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
        }
        .links a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Liste des étudiants</h1>
    
    <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="links">
        <a href="add_student.php">Ajouter un étudiant</a>
    </div>
    
    <?php if (empty($students)): ?>
        <p>Aucun étudiant enregistré.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom complet</th>
                    <th>Matricule</th>
                    <th>Groupe</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                    <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($student['matricule']); ?></td>
                    <td><?php echo htmlspecialchars($student['group_id']); ?></td>
                    <td class="actions">
                        <a href="update_student.php?id=<?php echo $student['id']; ?>" class="edit">Modifier</a>
                        <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet étudiant ?');">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>