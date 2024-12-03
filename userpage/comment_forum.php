<?php

require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);
date_default_timezone_set('Asia/Manila');

function addForumToAlumniComments($firebase, $alumniId, $forumId) {
    $alumniCommentsPath = "alumni_forum_comments/$alumniId/$forumId";
    $firebase->update($alumniCommentsPath, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $forumId = $_POST["forum_id"];
    $alumniId = $_POST["alumni_id"];
    $comment = $_POST["comment"];
    $dateCommented = date("Y-m-d H:i:s");
    $timestamp = round(microtime(true) * 1000); // Current timestamp in milliseconds

    $newComment = [
        "alumni_id" => $alumniId,
        "comment" => $comment,
        "date_commented" => $dateCommented,
        "timestamp" => $timestamp, // Added timestamp field
        "forum_id" => $forumId,
        "heart_count" => 0,
        "read_mark" => 0
    ];

    $result = $firebase->insert("forum_comments", $newComment);

    if ($result) {
        // Add this forum to the list of forums the alumni has commented on
        addForumToAlumniComments($firebase, $alumniId, $forumId);

        // Comment added successfully
        $response = [
            'status' => 'success',
            'message' => 'Comment added successfully'
        ];
    } else {
        // Error adding comment
        $response = [
            'status' => 'error',
            'message' => 'Error adding comment. Please try again.'
        ];
    }

    echo json_encode($response);
    exit();
}
?>