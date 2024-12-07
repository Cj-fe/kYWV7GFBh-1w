<?php
// Email configuration
define('EMAIL_USERNAME', 'montgomeryaurelia06@gmail.com');
define('EMAIL_PASSWORD', 'oylq mpnj adlw iuod');

// reCAPTCHA configuration
define('RECAPTCHA_SECRET_KEY', '6LfKXHwqAAAAALxzMrcFwWomBIgw4sbip0bW47Ka');

// Firebase configuration
$databaseURL = "https://mccnians-bc4f4-default-rtdb.firebaseio.com/";
$adminNodeKey = '-MyUniqueID12345ABCDEFGHIJKLMnoPQRSTUvWxYz';

//Layer for Logs
$layer_one = 'e71736c971c3451ae162fb330fada675b20a0b6b5f2091263f9f810613a0d3f7';
$layer_two = '09be13863632c8b3810c5bb1444ac1a9502110b8d89956ba069f767f4cb2afea';

// Infobip API configuration
define('INFOBIP_API_KEY', 'ebb25e48e98dc362c28ca1c31da84af6-26712938-405a-4f40-b278-056ab3495017');
define('INFOBIP_BASE_URL', 'https://jjqw2v.api.infobip.com');
define('INFOBIP_SENDER', 'AdminPanel');

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