<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$current_user_id = $_SESSION['user']['id'];
$receiver_id = $_POST['receiver_id']; // Assume you're sending the receiver's ID in the request

require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);

// Retrieve all messages
$messages = $firebase->retrieve("messages");
$messages = json_decode($messages, true);

// Initialize a counter to track the number of updated messages
$updatedCount = 0;

if (is_array($messages) && !empty($messages)) {
    foreach ($messages as $message_id => $message) {
        // Check if the message is between the current user and the receiver
        if (
            ($message['senderId'] == $current_user_id && $message['receiverId'] == $receiver_id) ||
            ($message['senderId'] == $receiver_id && $message['receiverId'] == $current_user_id)
        ) {
            // Update the message_active field to 1 (or whatever value indicates "active")
            $message['message_active'] = 1;
            $firebase->update("messages", $message_id, $message); // Update message in Firebase

            // Increment the updated message counter
            $updatedCount++;
        }
    }
}

if ($updatedCount > 0) {
    echo json_encode(['success' => true, 'message' => "$updatedCount messages marked as active."]);
} else {
    echo json_encode(['error' => 'No messages were updated.']);
}
?>
