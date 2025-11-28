<?php
/**
 * Test de connexion à la base de données
 */

require_once 'config.php';
require_once 'db_connect.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test de Connexion DB</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .container { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { 
            color: #155724; 
            background: #d4edda; 
            padding: 15px; 
            border-radius: 5px; 
            border: 1px solid #c3e6cb;
            margin: 10px 0;
        }
        .error { 
            color: #721c24; 
            background: #f8d7da; 
            padding: 15px; 
            border-radius: 5px; 
            border: 1px solid #f5c6cb;
            margin: 10px 0;
        }
        .info { 
            background: #d1ecf1; 
            padding: 15px; 
            border-radius: 5px; 
            border: 1px solid #bee5eb;
            margin: 10px 0;
        }
        pre { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 5px; 
            border: 1px solid #e9ecef;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🧪 Test de Connexion Base de Données</h1>
";

try {
    // Test 1: Connexion simple
    $conn = getConnection();
    echo "<div class='success'>✅ Connexion réussie à la base de données : <strong>" . DB_NAME . "</strong></div>";
    
    // Test 2: Test de requête
    $stmt = $conn->query("SELECT VERSION() as mysql_version");
    $version = $stmt->fetch();
    echo "<div class='info'>📊 Version MySQL : <strong>" . $version['mysql_version'] . "</strong></div>";
    
    // Test 3: Vérifier les tables
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<div class='info'>📋 Tables dans la base :</div>";
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li><strong>$table</strong></li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Aucune table trouvée</p>";
    }
    
    // Test 4: Structure de la table students (si elle existe)
    if (in_array('students', $tables)) {
        $stmt = $conn->query("DESCRIBE students");
        $structure = $stmt->fetchAll();
        
        echo "<div class='info'>🏗️ Structure de la table 'students' :</div>";
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Default</th></tr>";
        foreach ($structure as $col) {
            echo "<tr>
                <td><strong>{$col['Field']}</strong></td>
                <td>{$col['Type']}</td>
                <td>{$col['Null']}</td>
                <td>{$col['Key']}</td>
                <td>{$col['Default']}</td>
            </tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ Erreur de connexion : " . $e->getMessage() . "</div>";
    
    // Informations de débogage
    echo "<div class='info'>🐛 Informations de débogage :</div>";
    echo "<pre>";
    echo "DB_HOST: " . DB_HOST . "\n";
    echo "DB_USER: " . DB_USER . "\n";
    echo "DB_NAME: " . DB_NAME . "\n";
    echo "DB_PASS: " . (DB_PASS ? '***' : '(vide)') . "\n";
    echo "</pre>";
}

echo "
    <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
        <h3>🔗 Liens utiles :</h3>
        <a href='../index.html' class='btn'>🏠 Retour à l'application</a>
        <a href='list_students.php' class='btn'>👥 Liste des étudiants</a>
        <a href='list_sessions.php' class='btn'>📋 Liste des sessions</a>
    </div>
    </div>
</body>
</html>";
?>