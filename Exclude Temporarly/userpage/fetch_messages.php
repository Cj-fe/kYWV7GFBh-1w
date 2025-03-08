<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$current_user_id = $_SESSION['user']['id'];

require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);

$messages = $firebase->retrieve("messages");
$messages = json_decode($messages, true);

$all_alumni = $firebase->retrieve("alumni");
$all_alumni = json_decode($all_alumni, true);

// Initialize arrays to store processed messages
$processed_messages = [];
$latest_messages = []; // For the message menu, tracking the latest message per sender

if (is_array($messages) && !empty($messages)) {
    foreach ($messages as $message_id => $message) {
        $sender_id = $message['senderId'];
        $receiver_id = $message['receiverId'];

        // Check if the message is related to the current user
        if ($sender_id == $current_user_id || $receiver_id == $current_user_id) {
            $other_user_id = ($sender_id == $current_user_id) ? $receiver_id : $sender_id;
            $other_user = $all_alumni[$other_user_id] ?? null;

            if ($other_user) {
                // Add all messages between the current user and the other user to the chatbox
                $processed_messages[] = [
                    'messageId' => $message_id,
                    'senderId' => $sender_id,
                    'receiverId' => $receiver_id,
                    'content' => $message['content'],
                    'timestamp' => $message['timestamp'],
                    'userId' => $other_user_id,
                    'name' => $other_user['firstname'] . ' ' . $other_user['lastname'],
                    'profilePic' => isset($other_user['profile_url']) ? $other_user['profile_url'] : '../images/profile.jpg',
                    'timeAgo' => getTimeAgo($message['timestamp'])
                ];

                // For the message menu, keep only the most recent message from each sender
                // If no message from this sender exists in the latest_messages array or if the current message is newer, update it
                if (!isset($latest_messages[$other_user_id]) || strtotime($message['timestamp']) > strtotime($latest_messages[$other_user_id]['timestamp'])) {
                    $latest_messages[$other_user_id] = [
                        'messageId' => $message_id,
                        'senderId' => $sender_id,
                        'receiverId' => $receiver_id,
                        'content' => $message['content'],
                        'timestamp' => $message['timestamp'],
                        'userId' => $other_user_id,
                        'name' => $other_user['firstname'] . ' ' . $other_user['lastname'],
                        'profilePic' => isset($other_user['profile_url']) ? $other_user['profile_url'] : '../images/profile.jpg',
                        'timeAgo' => getTimeAgo($message['timestamp'])
                    ];
                }
            }
        }
    }
}

// Function to calculate time ago
function getTimeAgo($date) {
    $time_ago = strtotime($date);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;

    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);

    if ($seconds <= 60) {
        return "Just now";
    } else if ($minutes <= 60) {
        return ($minutes == 1) ? "1 minute ago" : "$minutes minutes ago";
    } else if ($hours <= 24) {
        return ($hours == 1) ? "1 hour ago" : "$hours hours ago";
    } else if ($days <= 7) {
        return ($days == 1) ? "1 day ago" : "$days days ago";
    } else if ($weeks <= 4.3) {
        return ($weeks == 1) ? "1 week ago" : "$weeks weeks ago";
    } else if ($months <= 12) {
        return ($months == 1) ? "1 month ago" : "$months months ago";
    } else {
        return ($years == 1) ? "1 year ago" : "$years years ago";
    }
}

// Output the processed messages
echo json_encode([
    'messages' => $processed_messages, // All messages for the chatbox
    'latest_messages' => array_values($latest_messages), // Only the latest message from each sender for the message menu
    'count' => count($processed_messages)
]);
?>
