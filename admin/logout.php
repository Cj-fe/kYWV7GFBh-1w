<?php
session_start();

// Include necessary files
include 'includes/firebaseRDB.php';
require_once 'includes/config.php';

// Initialize Firebase connection
$firebase = new firebaseRDB($databaseURL);

// Retrieve admin data
$adminData = $firebase->retrieve("admin");
$adminData = json_decode($adminData, true);

// Check if admin data contains a token
if (isset($adminData['token'])) {
    $token = $adminData['token'];
} else {
    // Handle the case where the token is not found
    $token = 'defaultToken'; // or handle the error appropriately
}

// Unset admin-specific session variables
unset($_SESSION['admin']);

// Redirect to the desired page with the token as a query parameter
header('Location: index.php?token=' . urlencode($token));
exit();
?>