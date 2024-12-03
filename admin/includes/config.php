<?php
// Email configuration
define('EMAIL_USERNAME', 'montgomeryaurelia06@gmail.com');
define('EMAIL_PASSWORD', 'oylq mpnj adlw iuod');

// reCAPTCHA configuration
define('RECAPTCHA_SECRET_KEY', '6LfKXHwqAAAAALxzMrcFwWomBIgw4sbip0bW47Ka');

// Firebase configuration
$databaseURL = "https://mccalumniapp-default-rtdb.firebaseio.com/";
$adminNodeKey = '-MyUniqueID12345ABCDEFGHIJKLMnoPQRSTUvWxYz';

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