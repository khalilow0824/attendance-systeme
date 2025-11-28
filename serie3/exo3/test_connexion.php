<?php
/**
 * ============================================
 * FICHIER 3 : Page de test
 * ============================================
 */

require_once 'db_connect.php';
$result = testConnection();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Exercice 3 - Test Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 { color: #5b2c6f; }
        .result {
            padding: 30px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            border: 3px solid #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 3px solid #dc3545;
            color: #721c24;
        }
        .icon { font-size: 60px; margin-bottom: 15px; }
        .info {
            margin: 10px 0;
            padding: 10px;
            background: rgba(0,0,0,0.05);
            border-radius: 5px;
        }
        button {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Exercice 3 - Test Connexion</h1>
        
        <div class="result <?php echo $result['success'] ? 'success' : 'error'; ?>">
            <div class="icon"><?php echo $result['success'] ? '✅' : '❌'; ?></div>
            <h2><?php echo $result['success'] ? 'Connexion Réussie!' : 'Connexion Échouée'; ?></h2>
            <p><?php echo $result['message']; ?></p>
            
            <?php if ($result['success']): ?>
                <div class="info"><strong>Base:</strong> <?php echo $result['database']; ?></div>
                <div class="info"><strong>MySQL:</strong> <?php echo $result['version']; ?></div>
            <?php endif; ?>
        </div>
        
        <button onclick="location.reload()">🔄 Tester à nouveau</button>
    </div>
</body>
</html>