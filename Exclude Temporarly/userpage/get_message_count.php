<?php
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

session_start();

$firebase = new firebaseRDB($databaseURL);
$current_user_id = $_SESSION['user']['id'];
$messages = json_decode($firebase->retrieve("messages"), true);

// Function to count unread messages
function countUnreadMessages($messages, $current_user_id) {
    $unread_count = 0;

    if (is_array($messages)) {
        foreach ($messages as $message_id => $message) {
            if (isset($message['message_active']) && $message['message_active'] === 0 && $message['receiverId'] === $current_user_id) {
                $unread_count++;
            }
        }
    }

    return $unread_count;
}

$message_count = countUnreadMessages($messages, $current_user_id);

// Return the count as JSON
header('Content-Type: application/json');
echo json_encode(['message_count' => $message_count]);
