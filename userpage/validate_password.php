<?php
require_once '../includes/config.php';
require_once '../includes/session.php';

$firebase = new firebaseRDB($databaseURL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['alumni_id'] ?? null;
    $current_password = $_POST['current_password'] ?? '';

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'User ID is missing']);
        exit;
    }

    // Retrieve the user's data from Firebase
    $user_data = $firebase->retrieve("alumni/$user_id");
    $user_data = json_decode($user_data, true);

    if (!$user_data) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Verify the current password
    if (password_verify($current_password, $user_data['password'])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}