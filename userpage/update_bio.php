<?php
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bio']) && isset($_POST['alumni_id'])) {
    $newBio = $_POST['bio'];
    $alumniId = $_POST['alumni_id'];

    // Update the bio in Firebase
    $result = $firebase->update($table, "alumni/" . $alumniId, [
        "bio" => $newBio
    ]);

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update bio']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>