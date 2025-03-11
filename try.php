<?php
$servername = "localhost";
$username = "u510162695_mccalumni_root";
$password = "1Mccalumni_root";
$database = "u510162695_mccalumni";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to drop all tables
function dropAllTables($conn) {
    $tables_result = $conn->query("SHOW TABLES");
    
    while ($table_row = $tables_result->fetch_array()) {
        $table_name = $table_row[0];
        $conn->query("DROP TABLE `$table_name`");
        echo "Dropped table: $table_name <br>";
    }
    echo "All tables have been dropped.<br>";
}

// Function to import a new SQL file (Improved)
function importSQL($conn, $filePath) {
    $sqlContent = file_get_contents($filePath);

    if ($sqlContent === false) {
        echo "Error reading the SQL file.";
        return;
    }

    // Split SQL file into individual queries
    $queries = explode(";", $sqlContent);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if (!$conn->query($query)) {
                echo "Error importing SQL: " . $conn->error . "<br>";
            }
        }
    }
    
    echo "New SQL file imported successfully.<br>";
}

// Handle actions
if (isset($_GET['action'])) {
    if ($_GET['action'] == "drop") {
        dropAllTables($conn);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["sql_file"])) {
    $fileTmpPath = $_FILES["sql_file"]["tmp_name"];
    
    if (file_exists($fileTmpPath)) {
        importSQL($conn, $fileTmpPath);
    } else {
        echo "Error: No file uploaded.";
    }
}

$conn->close();
?>

<!-- HTML buttons to trigger actions -->
<form method="get">
    <button type="submit" name="action" value="drop">Drop All Tables</button>
</form>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="sql_file" required>
    <button type="submit">Import SQL</button>
</form>
