<?php
require_once '../includes/firebaseRDB.php';

// Initialize Firebase URL
require_once '../includes/config.php';
$firebase = new firebaseRDB($databaseURL);

// Set the default timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

if (isset($_POST['news_id']) && isset($_POST['alumni_id']) && isset($_POST['comment'])) {
    $news_id = $_POST['news_id'];
    $alumni_id = $_POST['alumni_id'];
    $comment = $_POST['comment'];

    // Create the data to be inserted
    $commentData = [
        'news_id' => $news_id,
        'alumni_id' => $alumni_id,
        'comment' => $comment,
        'date_commented' => date('F j, Y H:i:s'),  // Include current date and time
        'heart_count' => 0
    ];

    // Insert the data into the news_comments rule
    $insert = $firebase->insert("news_comments", $commentData);

    if ($insert) {
        echo json_encode(['status' => 'success', 'message' => 'Comment added!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding comment!']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required!']);
}
?>
