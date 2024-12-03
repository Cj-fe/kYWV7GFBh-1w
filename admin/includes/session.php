<?php
session_start();
include 'firebaseRDB.php';
require_once 'config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

// Debugging session values
if (!isset($_SESSION['admin']) || trim($_SESSION['admin']) == '') {
    header('location: index.php');
    exit();
}

// Retrieve admin user data from Firebase using the adminNodeKey
$adminData = $firebase->retrieve("admin/{$adminNodeKey}");
$adminData = json_decode($adminData, true);

// Check if the admin data exists and matches the session admin ID
$adminId = $_SESSION['admin'];
if (!isset($adminData['user']) || $adminData['user'] !== $adminId) {
    // Invalid session or admin not found
    header('location: index.php');
    exit();
}

// Skip token URL validation for POST requests (admin updates)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Check if token_url is not in URL but exists in admin data
    if (!isset($_GET['token_url']) && isset($adminData['token_url'])) {
        // Redirect to same page with token_url parameter
        $currentURL = $_SERVER['PHP_SELF'];
        if (!empty($_GET)) {
            $params = $_GET;
            $params['token_url'] = $adminData['token_url'];
            $currentURL .= '?' . http_build_query($params);
        } else {
            $currentURL .= '?token_url=' . urlencode($adminData['token_url']);
        }
        header('Location: ' . $currentURL);
        exit();
    }

    // Check if token_url parameter exists and matches for GET requests only
    if (!isset($_GET['token_url']) || !isset($adminData['token_url']) || $_GET['token_url'] !== $adminData['token_url']) {
        header('Location: includes/404.html');
        exit();
    }
}

// Admin user is authenticated, store user data in session
$user = [
    'id' => $adminId,
    'user' => $adminData['user'],
    'password' => $adminData['password'],
    'firstname' => $adminData['firstname'],
    'lastname' => $adminData['lastname'],
    'image_url' => $adminData['image_url'],
    'lockscreen' => $adminData['lockscreen'],
    'toggle_sidebar' => $adminData['toggle_sidebar'],
    'notification_timestamp' => $adminData['notification_timestamp'],
    'mfa' => $adminData['mfa'],
    'token' => $adminData['token'],
    'phone' => $adminData['phone'],
    'created_on' => $adminData['created_on'] // Ensure this field exists in your Firebase data
];

// Generate CSRF token if not already set
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32)); // Generate a random token
}
$token = $_SESSION['token'];
?>