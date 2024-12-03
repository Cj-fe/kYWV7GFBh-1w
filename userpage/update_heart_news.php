<?php
require_once "../includes/firebaseRDB.php";
require_once "../includes/config.php";

// Set the desired timezone (e.g., 'America/New_York')
date_default_timezone_set('Asia/Manila');

$firebase = new firebaseRDB($databaseURL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentId = $_POST['comment_id'];
    $alumniId = $_POST['alumni_id'];
    $isLiked = $_POST['is_liked'] === 'true';

    $commentData = $firebase->retrieve("news_comments/{$commentId}");
    $commentData = json_decode($commentData, true);

    if (!isset($commentData['liked_by'])) {
        $commentData['liked_by'] = [];
    }

    if ($isLiked) {
        $commentData['liked_by'][$alumniId] = date('Y-m-d H:i:s');
    } else {
        unset($commentData['liked_by'][$alumniId]);
    }

    $heartCount = count($commentData['liked_by']);

    $updateData = [
        'liked_by' => $commentData['liked_by'],
        'heart_count' => $heartCount
    ];

    $firebase->update("news_comments", $commentId, $updateData);

    echo json_encode([
        'success' => true,
        'is_liked' => $isLiked,
        'heart_count' => $heartCount
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
