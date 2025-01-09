<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/firebaseRDB.php';

// Set header for JSON response
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the incoming request
error_log('Validate password request received: ' . print_r($_POST, true));

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Check if user is logged in
    if (!isset($_SESSION['alumni_id'])) {
        throw new Exception('User not authenticated');
    }

    $user_id = $_SESSION['alumni_id'];
    
    // Validate current password input
    if (!isset($_POST['current_password']) || empty($_POST['current_password'])) {
        throw new Exception('Current password is required');
    }

    $current_password = $_POST['current_password'];

    // Initialize Firebase
    $firebase = new firebaseRDB($databaseURL);
    
    // Log Firebase retrieval attempt
    error_log("Attempting to retrieve user data for ID: $user_id");

    // Retrieve user data from Firebase
    $user_data = $firebase->retrieve("alumni/$user_id");
    
    // Log the retrieved data (be careful with sensitive information in production)
    error_log("Retrieved user data: " . substr(print_r($user_data, true), 0, 100) . "...");

    if (!$user_data) {
        throw new Exception('Failed to retrieve user data');
    }

    $user_data = json_decode($user_data, true);

    if (!$user_data || !isset($user_data['password'])) {
        throw new Exception('Invalid user data structure');
    }

    // Verify password
    if (password_verify($current_password, $user_data['password'])) {
        // Set a session variable to indicate successful verification
        $_SESSION['password_verified'] = true;
        
        echo json_encode([
            'success' => true,
            'message' => 'Password verified successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Incorrect password'
        ]);
    }

} catch (Exception $e) {
    error_log('Password validation error: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error' => true
    ]);
}

// Log the completion of the script
error_log('Password validation process completed');