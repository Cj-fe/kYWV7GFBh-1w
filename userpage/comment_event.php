<?php
require_once '../includes/firebaseRDB.php';

// Initialize Firebase URL
require_once '../includes/config.php';
$firebase = new firebaseRDB($databaseURL);
date_default_timezone_set('Asia/Manila');

if (isset($_POST['event_id']) && isset($_POST['alumni_id']) && isset($_POST['comment'])) {
    $event_id = $_POST['event_id'];
    $alumni_id = $_POST['alumni_id'];
    $comment = $_POST['comment'];

    // Create the current date and time in ISO 8601 format with time zone offset
    $dateTime = new DateTime();
    $date_commented = $dateTime->format('Y-m-d\TH:i:sP');

    // Create the data to be inserted
    $commentData = [
        'event_id' => $event_id,
        'alumni_id' => $alumni_id,
        'comment' => $comment,
        'date_commented' => $date_commented,  // Use ISO 8601 format with timezone offset
        'heart_count' => 0
    ];

    // Insert the data into the comment rule
    $insert = $firebase->insert("event_comments", $commentData);

    if ($insert) {
        echo json_encode(['status' => 'success', 'message' => 'Comment added!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding comment!']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required!']);
}
?>
