<?php
/**
 * Liste de toutes les sessions (ouvertes et fermées)
 */
require_once 'db_connect.php';

$error = '';
$sessions = [];

try {
    $conn = getConnection();
    $stmt = $conn->query("SELECT * FROM attendance_sessions ORDER BY date DESC, created_at DESC");
    $sessions = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération : " . $e->getMessage();
}

// Statistiques
$total = count($sessions);
$open = count(array_filter($sessions, fn($s) => $s['status'] === 'open'));
$closed = $total - $open;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des sessions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }
        h1 {
            color: #5b2c6f;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            flex: 1;
            background: linear-gradient(135deg, #f7f5fb, #fff);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(91,44,111,0.08);
            border-left: 4px solid #9b59b6;
        }
        .stat-card h3 {
            margin: 0;
            color: #5b2c6f;
            font-size: 32px;
        }
        .stat-card p {
            margin: 5px 0 0 0;
            color: #6c757d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(91,44,111,0.08);
            background: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e6dff0;
        }
        th {
            background: linear-gradient(180deg, #f7f5fb, #faf7fd);
            color: #5b2c6f;
            font-weight: 600;
        }
        tr:hover {
            background-color: rgba(155,89,182,0.05);
        }
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-open {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-closed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .links {
            margin-bottom: 20px;
        }
        .links a {
            background: linear-gradient(135deg, #b32e8f, #ff77b0);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            margin-right: 10px;
            display: inline-block;
        }
        .links a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(179,46,143,0.3);
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <h1>📋 Liste des sessions de présence</h1>
    
    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="links">
        <a href="create_session.php">➕ Nouvelle session</a>
        <a href="close_session.php">🔒 Fermer une session</a>
    </div>
    
    <div class="stats">
        <div class="stat-card">
            <h3><?php echo $total; ?></h3>
            <p>Total sessions</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $open; ?></h3>
            <p>Sessions ouvertes</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $closed; ?></h3>
            <p>Sessions fermées</p>
        </div>
    </div>
    
    <?php if (empty($sessions)): ?>
        <div class="empty-state">
            <p>📭 Aucune session enregistrée.</p>
            <p><a href="create_session.php">Créer la première session</a></p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cours</th>
                    <th>Groupe</th>
                    <th>Date</th>
                    <th>Ouvert par</th>
                    <th>Statut</th>
                    <th>Créée le</th>
                    <th>Fermée le</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sessions as $session): ?>
                <tr>
                    <td><?php echo htmlspecialchars($session['id']); ?></td>
                    <td><?php echo htmlspecialchars($session['course_id']); ?></td>
                    <td><?php echo htmlspecialchars($session['group_id']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($session['date'])); ?></td>
                    <td><?php echo htmlspecialchars($session['opened_by']); ?></td>
                    <td>
                        <?php if ($session['status'] === 'open'): ?>
                            <span class="badge badge-open">Ouverte</span>
                        <?php else: ?>
                            <span class="badge badge-closed">Fermée</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($session['created_at'])); ?></td>
                    <td>
                        <?php 
                        if ($session['closed_at']) {
                            echo date('d/m/Y H:i', strtotime($session['closed_at']));
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>