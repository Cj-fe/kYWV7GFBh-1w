<?php
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';
$firebase = new firebaseRDB($databaseURL);

date_default_timezone_set('Asia/Manila');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $alumni_id = $_POST['alumni_id'];
    
    // Retrieve current event data
    $event_data = $firebase->retrieve("event/{$event_id}");
    $event_data = json_decode($event_data, true);
    
    // Initialize likes array if it doesn't exist
    if (!isset($event_data['likes'])) {
        $event_data['likes'] = [];
    }
    
    // Check if the alumni has already liked the event
    $liked = false;
    foreach ($event_data['likes'] as $id => $timestamp) {
        if ($id === $alumni_id) {
            $liked = true;
            break;
        }
    }
    
    if ($liked) {
        // Remove like
        unset($event_data['likes'][$alumni_id]);
    } else {
        // Add like with timestamp
        $event_data['likes'][$alumni_id] = date('Y-m-d H:i:s');
    }
    
    // Update like count
    $event_data['like_count'] = count($event_data['likes']);
    
    // Update event data in Firebase
    $result = $firebase->update("event", $event_id, $event_data);
    
    if ($result) {
        // Return updated data
        echo json_encode([
            'success' => true,
            'likeCount' => $event_data['like_count'],
            'liked' => !$liked
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}