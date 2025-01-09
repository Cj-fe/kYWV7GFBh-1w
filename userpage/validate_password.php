<?php
require_once '../includes/session.php';
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Check if user is logged in
    if (!isset($_SESSION['alumni_id']) || !isset($_SESSION['user'])) {
        throw new Exception('User not authenticated');
    }

    if (!isset($_POST['current_password']) || empty($_POST['current_password'])) {
        throw new Exception('Current password is required');
    }

    $current_password = $_POST['current_password'];
    $stored_password = $_SESSION['user']['password'];

    // Verify the password
    if (password_verify($current_password, $stored_password)) {
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
        'message' => 'An error occurred: ' . $e->getMessage(),
        'error' => true
    ]);
}