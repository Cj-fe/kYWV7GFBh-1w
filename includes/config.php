
<?php
define('EMAIL_USERNAME', 'montgomeryaurelia06@gmail.com');
define('EMAIL_PASSWORD', 'oylq mpnj adlw iuod');


$databaseURL = "https://mccalumniapp-cdac5-default-rtdb.firebaseio.com";

// MySQL configuration
$mysqlHost = "127.0.0.1";
$mysqlUsername = "u510162695_fms_db_root";
$mysqlPassword = "1Fms_db_root";
$mysqlDatabase = "u510162695_fms_db";

// Function to get MySQL connection
function getMySQLConnection() {
    global $mysqlHost, $mysqlUsername, $mysqlPassword, $mysqlDatabase;
    $conn = new mysqli($mysqlHost, $mysqlUsername, $mysqlPassword, $mysqlDatabase);
    if ($conn->connect_error) {
        error_log("MySQL Connection failed: " . $conn->connect_error);
        return false;
    }
    return $conn;
}
?>
