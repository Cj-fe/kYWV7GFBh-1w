<?php
// Database connection parameters
$dbHost = 'localhost';    // Adjust if your host is different
$dbName = 'u510162695_mccalumni';
$dbUser = 'u510162695_mccalumni_root';
$dbPass = '1Mccalumni_root';

// Function to import SQL file
function importSQLFile($pdo, $sqlFile) {
    try {
        // Read the SQL file
        $sql = file_get_contents($sqlFile);
        
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('!/\*.*?\*/!s', '', $sql);
        
        // Split SQL file into individual queries
        $queries = preg_split('/;\s*$/m', $sql);
        
        // Execute each query
        $count = 0;
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
                $count++;
            }
        }
        
        return $count;
    } catch (Exception $e) {
        throw new Exception("Error importing SQL file: " . $e->getMessage());
    }
}

// Main execution
try {
    // Create database connection
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<h2>SQL Import Tool</h2>";
    echo "<p>Connected successfully to database: <strong>$dbName</strong></p>";

    // Handle file upload
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["sqlFile"])) {
        // Check if file was uploaded without errors
        if ($_FILES["sqlFile"]["error"] == 0) {
            $tmpName = $_FILES["sqlFile"]["tmp_name"];
            $fileName = $_FILES["sqlFile"]["name"];
            
            // Check if the file is an SQL file
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if ($fileExt == "sql") {
                // Disable foreign key checks
                $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
                
                // Import the SQL file
                $startTime = microtime(true);
                $queryCount = importSQLFile($pdo, $tmpName);
                $endTime = microtime(true);
                $executionTime = round($endTime - $startTime, 2);
                
                // Re-enable foreign key checks
                $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
                
                echo "<div style='background-color: #dff0d8; color: #3c763d; padding: 15px; border-radius: 4px; margin-top: 20px;'>";
                echo "<h3>Import Successful!</h3>";
                echo "<p>File <strong>$fileName</strong> was successfully imported.</p>";
                echo "<p>$queryCount queries executed in $executionTime seconds.</p>";
                echo "</div>";
            } else {
                echo "<div style='background-color: #f2dede; color: #a94442; padding: 15px; border-radius: 4px; margin-top: 20px;'>";
                echo "<h3>Error</h3>";
                echo "<p>Only SQL files are allowed.</p>";
                echo "</div>";
            }
        } else {
            echo "<div style='background-color: #f2dede; color: #a94442; padding: 15px; border-radius: 4px; margin-top: 20px;'>";
            echo "<h3>Error</h3>";
            echo "<p>Error uploading file. Error code: " . $_FILES["sqlFile"]["error"] . "</p>";
            echo "</div>";
        }
    }

    // Display upload form
    echo "<form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='post' enctype='multipart/form-data' style='margin-top: 20px;'>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label for='sqlFile' style='display: block; margin-bottom: 5px;'>Select SQL File:</label>";
    echo "<input type='file' name='sqlFile' id='sqlFile' required style='padding: 5px;'>";
    echo "</div>";
    echo "<input type='submit' value='Import SQL' style='background-color: #337ab7; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;'>";
    echo "</form>";
    
    echo "</div>";
} catch (PDOException $e) {
    echo "<div style='background-color: #f2dede; color: #a94442; padding: 15px; border-radius: 4px;'>";
    echo "<h3>Database Connection Error</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>