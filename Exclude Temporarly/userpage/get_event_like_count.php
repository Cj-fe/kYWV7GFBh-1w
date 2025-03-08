<?php
require_once '../includes/firebaseRDB.php';

require_once '../includes/config.php';
$firebase = new firebaseRDB($databaseURL);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $event_id = $_GET['event_id'];
    $alumni_id = $_GET['alumni_id'];

    // Retrieve current event data
    $event_data = $firebase->retrieve("event/{$event_id}");
    $event_data = json_decode($event_data, true);

    // Get like count
    $like_count = isset($event_data['like_count']) ? $event_data['like_count'] : 0;

    // Check if the current user has liked the event
    $isLiked = false;
    if ($alumni_id && isset($event_data['likes']) && is_array($event_data['likes'])) {
        $isLiked = array_key_exists($alumni_id, $event_data['likes']);
    }

    // Return updated data
    echo json_encode([
        'success' => true,
        'likeCount' => $like_count,
        'liked' => $isLiked
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}