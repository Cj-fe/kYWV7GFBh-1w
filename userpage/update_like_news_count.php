<!--update_like_news_count.php-->
<?php
require_once "../includes/firebaseRDB.php";
require_once "../includes/config.php";

$firebase = new firebaseRDB($databaseURL);
date_default_timezone_set('Asia/Manila');

if (isset($_POST['news_id']) && isset($_POST['alumni_id'])) {
    $news_id = $_POST['news_id'];
    $alumni_id = $_POST['alumni_id'];

    $news_data = $firebase->retrieve("news/{$news_id}");
    $news_data = json_decode($news_data, true);

    if (!isset($news_data['likes'])) {
        $news_data['likes'] = [];
    }

    $current_timestamp = date("Y-m-d H:i:s");

    if (isset($news_data['likes'][$alumni_id])) {
        unset($news_data['likes'][$alumni_id]);
    } else {
        $news_data['likes'][$alumni_id] = $current_timestamp;
    }

    $firebase->update("news", $news_id, $news_data);

    echo json_encode([
        'success' => true,
        'is_liked' => isset($news_data['likes'][$alumni_id]),
        'like_count' => count($news_data['likes'])
    ]);
} else {
    echo json_encode(['success' => false]);
}