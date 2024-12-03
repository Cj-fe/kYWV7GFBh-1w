<?php
include "../includes/firebaseRDB.php";

include '../includes/config.php';
$firebase = new firebaseRDB($databaseURL);

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $comment_id = $_GET['comment_id'];
    $alumni_id = $_GET['alumni_id'];

    $comment_data = $firebase->retrieve("event_comments/{$comment_id}");
    $comment_data = json_decode($comment_data, true);

    $is_liked = isset($comment_data['liked_by'][$alumni_id]);
    $heart_count = isset($comment_data['heart_count']) ? $comment_data['heart_count'] : 0;

    echo json_encode([
        'is_liked' => $is_liked,
        'heart_count' => $heart_count
    ]);
}
?>