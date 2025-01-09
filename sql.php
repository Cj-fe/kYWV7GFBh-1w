<?php
// MySQL configuration
$mysqlHost = "127.0.0.1";
$mysqlUsername = "u510162695_fms_db_root";
$mysqlPassword = "1Fms_db_root";
$mysqlDatabase = "u510162695_fms_db";

// Create connection
$conn = new mysqli($mysqlHost, $mysqlUsername, $mysqlPassword, $mysqlDatabase);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch data from the applicant table
$sql = "SELECT * FROM applicant LIMIT 1"; // Limit 1 to fetch column metadata only
$result = $conn->query($sql);

if ($result) {
    echo "<h2>Detected Columns and Data Types:</h2>";
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Column Name</th><th>Data Type</th></tr>";

    // Fetch metadata for all fields
    $fields = $result->fetch_fields();
    foreach ($fields as $field) {
        // Display column name and data type
        echo "<tr>";
        echo "<td>" . htmlspecialchars($field->name) . "</td>";
        echo "<td>" . htmlspecialchars(getMySQLDataType($field->type)) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error fetching column metadata: " . $conn->error;
}

// Close the connection
$conn->close();

/**
 * Helper function to map MySQL field types to human-readable data types
 */
function getMySQLDataType($type) {
    $types = [
        1 => "TINYINT",
        2 => "SMALLINT",
        3 => "INTEGER",
        4 => "FLOAT",
        5 => "DOUBLE",
        7 => "TIMESTAMP",
        8 => "BIGINT",
        9 => "MEDIUMINT",
        10 => "DATE",
        11 => "TIME",
        12 => "DATETIME",
        13 => "YEAR",
        16 => "BIT",
        252 => "TEXT/BLOB",
        253 => "VARCHAR",
        254 => "CHAR",
    ];
    return $types[$type] ?? "UNKNOWN";
}
?>
