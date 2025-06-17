<?php
require_once "../includes/firebaseRDB.php";
require_once "../includes/config.php";

$firebase = new firebaseRDB($databaseURL);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $commentId = $_GET['comment_id'];
    $alumniId = $_GET['alumni_id'];

    $commentData = $firebase->retrieve("news_comments/{$commentId}");
    $commentData = json_decode($commentData, true);

    $isLiked = isset($commentData['liked_by'][$alumniId]);
    $heartCount = isset($commentData['liked_by']) ? count($commentData['liked_by']) : 0;

    echo json_encode([
        'is_liked' => $isLiked,
        'heart_count' => $heartCount
    ]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}