<?php
// Database connection parameters
$dbHost = 'localhost';    // Adjust if your host is different
$dbName = 'u510162695_mccalumni';
$dbUser = 'u510162695_mccalumni_root';
$dbPass = '1Mccalumni_root';

// Create connection
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully to the database. <br>";
    
    // Disable foreign key checks to avoid constraint issues
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    // Get all tables in the database
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "No tables found in the database.";
    } else {
        echo "Found " . count($tables) . " tables. Dropping all tables...<br>";
        
        // Drop each table
        foreach ($tables as $table) {
            $sql = "DROP TABLE `$table`";
            $pdo->exec($sql);
            echo "Dropped table: $table <br>";
        }
        
        echo "All tables have been dropped successfully.";
    }
    
    // Re-enable foreign key checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>