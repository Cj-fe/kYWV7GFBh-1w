<?php
require_once "../includes/firebaseRDB.php";
require_once "../includes/config.php";

$firebase = new firebaseRDB($databaseURL);

if (isset($_GET['news_id']) && isset($_GET['alumni_id'])) {
    $news_id = $_GET['news_id'];
    $alumni_id = $_GET['alumni_id'];

    $news_data = $firebase->retrieve("news/{$news_id}");
    $news_data = json_decode($news_data, true);

    $like_count = isset($news_data['likes']) ? count($news_data['likes']) : 0;
    
    // Check if the alumni_id exists as a key in the likes array
    $is_liked = isset($news_data['likes']) && array_key_exists($alumni_id, $news_data['likes']);

    $commentData = $firebase->retrieve("news_comments");
    $commentData = json_decode($commentData, true);
    $comment_count = 0;
    if (is_array($commentData)) {
        foreach ($commentData as $comment) {
            if ($comment["news_id"] === $news_id) {
                $comment_count++;
            }
        }
    }

    echo json_encode([
        'like_count' => $like_count,
        'comment_count' => $comment_count,
        'is_liked' => $is_liked
    ]);
} else {
    echo json_encode(['success' => false]);
}