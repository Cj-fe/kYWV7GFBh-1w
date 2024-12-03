<?php
include "../includes/firebaseRDB.php";

require_once '../includes/config.php';
$firebase = new firebaseRDB($databaseURL);
date_default_timezone_set('Asia/Manila');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment_id = $_POST['comment_id'];
    $alumni_id = $_POST['alumni_id'];
    $is_liked = $_POST['is_liked'] === 'true';

    $comment_data = $firebase->retrieve("event_comments/{$comment_id}");
    $comment_data = json_decode($comment_data, true);

    if (!isset($comment_data['liked_by'])) {
        $comment_data['liked_by'] = [];
    }

    $current_timestamp = date('Y-m-d H:i:s');

    if ($is_liked) {
        $comment_data['liked_by'][$alumni_id] = $current_timestamp;
        $comment_data['heart_count'] = isset($comment_data['heart_count']) ? $comment_data['heart_count'] + 1 : 1;
    } else {
        unset($comment_data['liked_by'][$alumni_id]);
        $comment_data['heart_count'] = max(0, (isset($comment_data['heart_count']) ? $comment_data['heart_count'] - 1 : 0));
    }

    $result = $firebase->update("event_comments", $comment_id, $comment_data);

    if ($result) {
        echo json_encode([
            'success' => true,
            'is_liked' => $is_liked,
            'heart_count' => $comment_data['heart_count']
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>