<?php
$servername = "localhost"; // Change if needed
$username = "u510162695_mccalumni_root";
$password = "1Mccalumni_root";
$database = "u510162695_mccalumni";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to display all tables
function displayTables($conn) {
    $tables_result = $conn->query("SHOW TABLES");

    if ($tables_result->num_rows > 0) {
        while ($table_row = $tables_result->fetch_array()) {
            $table_name = $table_row[0];
            echo "<h2>Table: $table_name</h2>";

            // Fetch data from each table
            $sql = "SELECT * FROM $table_name";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table border='1'><tr>";

                // Fetch column names
                while ($field = $result->fetch_field()) {
                    echo "<th>{$field->name}</th>";
                }
                echo "</tr>";

                // Fetch rows
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>{$value}</td>";
                    }
                    echo "</tr>";
                }
                echo "</table><br>";
            } else {
                echo "No data available in this table.<br><br>";
            }
        }
    } else {
        echo "No tables found in the database.";
    }
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

// Function to import a new SQL file
function importSQL($conn, $filePath) {
    $sqlContent = file_get_contents($filePath);
    
    if ($sqlContent === false) {
        echo "Error reading the SQL file.";
        return;
    }

    // Execute multiple queries
    if ($conn->multi_query($sqlContent)) {
        do {
            // Clear results to allow the next query to run
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
        
        echo "New SQL file imported successfully.<br>";
    } else {
        echo "Error importing SQL: " . $conn->error;
    }
}

// Check if actions were triggered
if (isset($_GET['action'])) {
    if ($_GET['action'] == "drop") {
        dropAllTables($conn);
    } elseif ($_GET['action'] == "import" && isset($_FILES['sql_file'])) {
        importSQL($conn, $_FILES['sql_file']['tmp_name']);
    }
}

// Display current tables
displayTables($conn);

$conn->close();
?>

<!-- HTML buttons to trigger actions -->
<form method="get">
    <button type="submit" name="action" value="drop">Drop All Tables</button>
</form>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="sql_file" required>
    <button type="submit" name="action" value="import">Import SQL</button>
</form>
