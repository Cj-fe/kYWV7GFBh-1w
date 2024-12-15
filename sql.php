<?php
// MySQL configuration
$mysqlHost = "127.0.0.1";
$mysqlUsername = "u510162695_fms_db_root";
$mysqlPassword = "1Fms_db_root";
$mysqlDatabase = "u510162695_fms_db";

// Create a connection to the database
$conn = new mysqli($mysqlHost, $mysqlUsername, $mysqlPassword, $mysqlDatabase);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to retrieve data from the applicant table
$sql = "SELECT * FROM applicant";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellspacing='0' cellpadding='10'>";

    // Fetch and display column names
    echo "<tr>";
    while ($field = $result->fetch_field()) {
        echo "<th>" . htmlspecialchars($field->name) . "</th>";
    }
    echo "</tr>";

    // Fetch and display rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "No data found in the applicant table.";
}

// Close the database connection
$conn->close();
?>
