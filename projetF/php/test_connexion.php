<?php
/**
 * Page de test de la connexion MySQL
 * Exercice 3
 */

require_once 'db_connect.php';

$result = testConnection();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Connexion MySQL</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }
        h1 {
            color: #5b2c6f;
            margin-bottom: 30px;
            font-size: 28px;
        }
        .result {
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
        }
        .success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 3px solid #28a745;
            color: #155724;
        }
        .error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border: 3px solid #dc3545;
            color: #721c24;
        }
        .icon { font-size: 60px; margin-bottom: 15px; }
        h2 { margin: 15px 0; }
        .info {
            margin: 10px 0;
            padding: 12px;
            background: rgba(0,0,0,0.05);
            border-radius: 8px;
            font-family: monospace;
        }
        button {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Test de Connexion MySQL</h1>
        
        <div class="result <?php echo $result['success'] ? 'success' : 'error'; ?>">
            <div class="icon"><?php echo $result['success'] ? '✅' : '❌'; ?></div>
            <h2><?php echo $result['success'] ? 'Connexion Réussie!' : 'Connexion Échouée'; ?></h2>
            <p><?php echo $result['message']; ?></p>
            
            <?php if ($result['success']): ?>
                <div class="info"><strong>Base de données:</strong> <?php echo $result['database']; ?></div>
                <div class="info"><strong>Hôte:</strong> <?php echo $result['host']; ?></div>
                <div class="info"><strong>Version MySQL:</strong> <?php echo $result['mysql_version']; ?></div>
            <?php endif; ?>
        </div>
        
        <button onclick="location.reload()">🔄 Tester à nouveau</button>
    </div>
</body>
</html>