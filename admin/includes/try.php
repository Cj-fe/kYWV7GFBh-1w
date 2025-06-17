<?php
//session.php
session_start();
include 'firebaseRDB.php';

require_once 'config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

// Debugging session values
if (!isset($_SESSION['admin']) || trim($_SESSION['admin']) == '') {
    header('location: index.php');
    exit();
}

// Retrieve admin user data from Firebase
$adminId = $_SESSION['admin'];

// Generate a unique key for temporary access
$tempUniqueKey = uniqid('temp_', true);

// Prepare the data to be inserted
$tempAccessData = [
    'key1' => 'value1',
    'key2' => 'value2',
    // Add any additional keys you want
];

// Update Firebase with the temporary unique key and its data
$firebase->insert("admin/{$adminId}/{$tempUniqueKey}", $tempAccessData);

// Rest of your existing authentication and session logic remains the same
// ...
?>