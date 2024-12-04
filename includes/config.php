
<?php
define('EMAIL_USERNAME', 'montgomeryaurelia06@gmail.com');
define('EMAIL_PASSWORD', 'oylq mpnj adlw iuod');


$databaseURL = "https://mccalumniapp-default-rtdb.firebaseio.com/";

$adminNodeKey = '-MyUniqueID12345ABCDEFGHIJKLMnoPQRSTUvWxYz';

//Layer for Logs
$layer_one = 'e71736c971c3451ae162fb330fada675b20a0b6b5f2091263f9f810613a0d3f7';
$layer_two = '09be13863632c8b3810c5bb1444ac1a9502110b8d89956ba069f767f4cb2afea';

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
