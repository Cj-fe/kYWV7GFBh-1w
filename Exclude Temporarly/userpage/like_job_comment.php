<?php
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

// Set the default timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

$firebase = new firebaseRDB($databaseURL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $commentId = $_POST["comment_id"];
    $alumniId = $_POST["alumni_id"];
    $comment = $firebase->retrieve("job_comments/{$commentId}");
    $comment = json_decode($comment, true);

    if (!isset($comment["liked_by"])) {
        $comment["liked_by"] = [];
    }

    if (array_key_exists($alumniId, $comment["liked_by"])) {
        // Unlike
        unset($comment["liked_by"][$alumniId]);
        $comment["heart_count"] = max(0, ($comment["heart_count"] ?? 0) - 1);
        $action = "unliked";
    } else {
        // Like
        $comment["liked_by"][$alumniId] = date("F j, Y H:i:s");
        $comment["heart_count"] = ($comment["heart_count"] ?? 0) + 1;
        $action = "liked";
    }

    $result = $firebase->update($table, "job_comments/{$commentId}", $comment);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "action" => $action,
            "heart_count" => $comment["heart_count"]
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error updating like status"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>