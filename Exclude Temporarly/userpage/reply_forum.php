<?php
include '../includes/session.php';
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $forum_id = htmlspecialchars($_POST['forum_id'], ENT_QUOTES, 'UTF-8');
    $comment_id = htmlspecialchars($_POST['comment_id'], ENT_QUOTES, 'UTF-8');
    $reply_content = htmlspecialchars($_POST['reply'], ENT_QUOTES, 'UTF-8');
    $alumni_id = $_SESSION['alumni_id']; // Assuming you store logged-in alumni ID in session

    // Validate input
    if (empty($forum_id) || empty($comment_id) || empty($reply_content) || empty($alumni_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    $reply_data = [
        'alumni_id' => $alumni_id,
        'reply' => $reply_content,
        'date_replied' => date('Y-m-d H:i:s'),
        'forum_id' => $forum_id
    ];

    try {
        $response = $firebase->insert("forum_comments/$comment_id/replies", $reply_data);

        if ($response) {
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