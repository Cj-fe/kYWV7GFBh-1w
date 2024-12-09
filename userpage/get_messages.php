<?php
session_start();
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

if (!isset($_GET['receiverId'])) {
    echo json_encode(['error' => 'Receiver ID not provided']);
    exit;
}

$firebase = new firebaseRDB($databaseURL);
$currentUserId = $_SESSION['user']['id'];
$receiverId = $_GET['receiverId'];

// Fetch the timestamp of the last message the user has seen
$lastSeenTimestamp = isset($_GET['lastSeen']) ? $_GET['lastSeen'] : 0;

// Fetch all messages
$allMessages = $firebase->retrieve("messages");
$allMessages = json_decode($allMessages, true);

$newMessages = [];

foreach ($allMessages as $messageId => $message) {
    // Check if the message is part of the current conversation and newer than the last seen message
    if (($message['senderId'] == $currentUserId && $message['receiverId'] == $receiverId ||
         $message['senderId'] == $receiverId && $message['receiverId'] == $currentUserId) &&
        strtotime($message['timestamp']) > $lastSeenTimestamp) {
        
        $newMessages[] = [
            'id' => $messageId,
            'senderId' => $message['senderId'],
            'receiverId' => $message['receiverId'],
            'content' => $message['content'],
            'timestamp' => $message['timestamp']
        ];
    }
}

// Sort messages by timestamp
usort($newMessages, function($a, $b) {
    return strtotime($a['timestamp']) - strtotime($b['timestamp']);
});

echo json_encode($newMessages);
?>