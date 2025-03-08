<?php

require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);
date_default_timezone_set('Asia/Manila');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jobId = $_POST["job_id"];
    $alumniId = $_POST["alumni_id"];
    $comment = $_POST["comment"];
    $dateCommented = date("Y-m-d H:i:s");

    $newComment = [
        "job_id" => $jobId,
        "alumni_id" => $alumniId,
        "comment" => $comment,
        "date_commented" => $dateCommented,
        "liked_by" => [],
        "heart_count" => 0,
        "read_mark" => 0
    ];

    $result = $firebase->insert("job_comments", $newComment);

    if ($result) {
        // Comment added successfully
        header("Location: visit_job.php?id=" . $jobId);
        exit();
    } else {
        // Error adding comment
        echo "Error adding comment. Please try again.";
    }
}
?>  