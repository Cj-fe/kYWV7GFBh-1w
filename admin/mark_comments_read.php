// mark_comments_read.php
<?php
require_once 'includes/config.php';
require_once 'includes/firebaseRDB.php';

$firebase = new firebaseRDB($databaseURL);

function markCommentsRead($path, $firebase) {
    $commentsData = $firebase->retrieve($path);
    $comments = json_decode($commentsData, true) ?: [];
    
    foreach ($comments as $id => $comment) {
        $comment['read'] = 1;
        $firebase->update("$path/$id", $comment);
    }
}

markCommentsRead("news_comments", $firebase);
markCommentsRead("event_comments", $firebase);
markCommentsRead("job_comments", $firebase);

echo json_encode(['status' => 'success']);
?>
