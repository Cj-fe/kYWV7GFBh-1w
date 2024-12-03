<?php
require_once '../includes/firebaseRDB.php';

// Initialize Firebase URL
require_once '../includes/config.php';
$firebase = new firebaseRDB($databaseURL);
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = htmlspecialchars($_POST['job_id'], ENT_QUOTES, 'UTF-8');
    $alumni_id = htmlspecialchars($_POST['alumni_id'], ENT_QUOTES, 'UTF-8');
    $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
    $parent_comment_id = htmlspecialchars($_POST['parent_comment_id'], ENT_QUOTES, 'UTF-8');

    // Validate input
    if (empty($job_id) || empty($alumni_id) || empty($comment) || empty($parent_comment_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    // Create reply data
    $replyData = [
        'alumni_id' => $alumni_id,
        'comment' => $comment,
        'date_replied' => date('Y-m-d H:i:s'),
        'job_id' => $job_id
    ];

    // Insert reply into Firebase as a new entry in the 'replies' object
    try {
        $result = $firebase->insert("job_comments/{$parent_comment_id}/replies", $replyData);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Reply submitted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error submitting reply']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error submitting reply: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>