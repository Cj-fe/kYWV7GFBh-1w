<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/firebaseRDB.php';

// Set header for JSON response
header('Content-Type: application/json');

// Enable error logging
error_log("Starting password validation process");

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_SESSION['alumni_id'])) {
        throw new Exception('User not authenticated');
    }

    if (!isset($_POST['current_password']) || empty($_POST['current_password'])) {
        throw new Exception('Current password is required');
    }

    $user_id = $_SESSION['alumni_id'];
    $current_password = $_POST['current_password'];

    // Initialize Firebase
    $firebase = new firebaseRDB($databaseURL);
    
    // Log the attempt to retrieve user data
    error_log("Attempting to retrieve data for user ID: " . $user_id);

    // Retrieve user data from Firebase
    $user_data = $firebase->retrieve("alumni/" . $user_id);
    
    // Debug: Log the raw response
    error_log("Firebase response: " . print_r($user_data, true));

    if (!$user_data) {
        throw new Exception('Failed to retrieve user data');
    }

    $user_data = json_decode($user_data, true);

    if (!$user_data || !isset($user_data['password'])) {
        throw new Exception('Invalid user data structure');
    }

    // Debug: Log password verification attempt (don't log actual passwords)
    error_log("Attempting to verify password for user: " . $user_id);

    // Verify password
    if (password_verify($current_password, $user_data['password'])) {
        $_SESSION['password_verified'] = true;
        error_log("Password verification successful for user: " . $user_id);
        
        echo json_encode([
            'success' => true,
            'message' => 'Password verified successfully'
        ]);
    } else {
        error_log("Password verification failed for user: " . $user_id);
        echo json_encode([
            'success' => false,
            'message' => 'Incorrect password'
        ]);
    }

} catch (Exception $e) {
    error_log("Password validation error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}