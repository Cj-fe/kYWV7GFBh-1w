<?php
$servername = "localhost"; // Change if needed
$username = "u510162695_mccalumni_root";
$password = "1Mccalumni_root";
$database = "u510162695_mccalumni";

// Create connection to MySQL (without selecting a database)
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Create Database request
if (isset($_POST['create_db'])) {
    $create_query = "CREATE DATABASE $database";
    if ($conn->query($create_query) === TRUE) {
        echo "Database <strong>$database</strong> created successfully!";
    } else {
        echo "Error creating database: " . $conn->error;
    }
    exit;
}

// Select the database
$conn->select_db($database);

// Handle Drop Database request
if (isset($_POST['drop_db'])) {
    $drop_query = "DROP DATABASE $database";
    if ($conn->query($drop_query) === TRUE) {
        echo "Database <strong>$database</strong> dropped successfully!";
    } else {
        echo "Error dropping database: " . $conn->error;
    }
    exit;
}

// Handle Import Database request
if (isset($_POST['import_db'])) {
    if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] == 0) {
        $sql_file = $_FILES['sql_file']['tmp_name'];
        $sql_content = file_get_contents($sql_file);

        // Execute SQL queries
        $queries = explode(";", $sql_content);
        foreach ($queries as $query) {
            if (trim($query) != "") {
                $conn->query($query);
            }
        }
        echo "Database imported successfully!";
    } else {
        echo "Error uploading SQL file.";
    }
    exit;
}

// Display all tables
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

$conn->close();
?>

<!-- Buttons for Create, Drop & Import -->
<form method="post">
    <button type="submit" name="create_db">
        Create Database
    </button>
</form>

<form method="post">
    <button type="submit" name="drop_db" onclick="return confirm('Are you sure you want to drop the database?');">
        Drop Database
    </button>
</form>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="sql_file" accept=".sql" required>
    <button type="submit" name="import_db">Import Database</button>
</form>
