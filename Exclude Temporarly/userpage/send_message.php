<?php
include '../includes/session.php';
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$firebase = new firebaseRDB($databaseURL);

// Get the JSON data from the request body
$json_data = file_get_contents('php://input');
$message_data = json_decode($json_data, true);

if (!$message_data) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
    exit;
}

// Validate the message data
if (!isset($message_data['senderId'], $message_data['receiverId'], $message_data['content'], $message_data['timestamp'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Add message_active field with value 0
$message_data['message_active'] = 0;

// Add the message to Firebase
$new_message = $firebase->insert("messages", $message_data);

if ($new_message) {
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to send message']);
}
?>