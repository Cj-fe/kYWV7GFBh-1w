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

// Get all table names from the database
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
