<?php
session_start();
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment_id = $_POST['comment_id'];
    $action = $_POST['action'];
    $alumni_id = $_SESSION['user']['id'];

    // Get the current comment data
    $comment = $firebase->retrieve("forum_comments/$comment_id");
    $comment = json_decode($comment, true);

    // Set timezone to Asia/Manila or any other timezone in Asia
    $timezone = new DateTimeZone('Asia/Manila');
    $current_time = new DateTime('now', $timezone);
    $formatted_time = $current_time->format('Y-m-d H:i:s');

    if ($action === 'like') {
        if (!isset($comment['liked_by'][$alumni_id])) {
            // Add like
            $comment['liked_by'][$alumni_id] = $formatted_time;
            $comment['heart_count'] = isset($comment['heart_count']) ? $comment['heart_count'] + 1 : 1;
            
            // Remove dislike if exists
            if (isset($comment['disliked_by'][$alumni_id])) {
                unset($comment['disliked_by'][$alumni_id]);
                $comment['dislike_count'] = max(0, ($comment['dislike_count'] ?? 0) - 1);
            }
        } else {
            // Remove like
            unset($comment['liked_by'][$alumni_id]);
            $comment['heart_count'] = max(0, ($comment['heart_count'] ?? 0) - 1);
        }
    } elseif ($action === 'dislike') {
        if (!isset($comment['disliked_by'][$alumni_id])) {
            // Add dislike
            $comment['disliked_by'][$alumni_id] = $formatted_time;
            $comment['dislike_count'] = isset($comment['dislike_count']) ? $comment['dislike_count'] + 1 : 1;
            
            // Remove like if exists
            if (isset($comment['liked_by'][$alumni_id])) {
                unset($comment['liked_by'][$alumni_id]);
                $comment['heart_count'] = max(0, ($comment['heart_count'] ?? 0) - 1);
            }
        } else {
            // Remove dislike
            unset($comment['disliked_by'][$alumni_id]);
            $comment['dislike_count'] = max(0, ($comment['dislike_count'] ?? 0) - 1);
        }
    }

    // Update the comment in the database
    $result = $firebase->update("forum_comments", $comment_id, $comment);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'heart_count' => $comment['heart_count'] ?? 0,
            'dislike_count' => $comment['dislike_count'] ?? 0,
            'liked' => isset($comment['liked_by'][$alumni_id]),
            'disliked' => isset($comment['disliked_by'][$alumni_id])
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update comment']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
