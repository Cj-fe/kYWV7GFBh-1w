<?php
require_once '../includes/firebaseRDB.php';

// Initialize Firebase URL
require_once '../includes/config.php';
$firebase = new firebaseRDB($databaseURL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $news_id = htmlspecialchars($_POST['news_id'], ENT_QUOTES, 'UTF-8');
    $alumni_id = htmlspecialchars($_POST['alumni_id'], ENT_QUOTES, 'UTF-8');
    $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
    $parent_comment_id = htmlspecialchars($_POST['parent_comment_id'], ENT_QUOTES, 'UTF-8');

    // Validate input
    if (empty($news_id) || empty($alumni_id) || empty($comment) || empty($parent_comment_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    // Get the current time in Asia/Manila timezone and format it
    $timezone = new DateTimeZone('Asia/Manila');
    $date = new DateTime('now', $timezone);
    $formattedDate = $date->format('F j, Y H:i:s');  // e.g., September 18, 2024 10:57:25

    // Create reply data
    $replyData = [
        'alumni_id' => $alumni_id,
        'comment' => $comment,
        'date_replied' => $formattedDate  // Use formatted date with Asia timezone
    ];

    // Insert reply into Firebase as a sub-comment
    try {
        $result = $firebase->insert("news_comments/{$parent_comment_id}/replies", $replyData);

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
