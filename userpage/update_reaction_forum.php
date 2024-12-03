<?php
include '../includes/session.php';
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $forum_id = $_POST['forum_id'];
    $reaction_type = $_POST['reaction_type'];
    $alumni_id = $_SESSION['user']['id'];

    // Get current forum data
    $forum_data = $firebase->retrieve("forum/$forum_id");
    $forum_data = json_decode($forum_data, true);

    // Initialize reaction array if it doesn't exist
    if (!isset($forum_data['reactions'])) {
        $forum_data['reactions'] = [];
    }

    // Check if the user has already reacted
    $user_previous_reaction = null;
    foreach ($forum_data['reactions'] as $reaction => $users) {
        if (isset($users[$alumni_id])) {
            $user_previous_reaction = $reaction;
            break;
        }
    }

    // Remove previous reaction if exists
    if ($user_previous_reaction) {
        unset($forum_data['reactions'][$user_previous_reaction][$alumni_id]);
        // Remove the reaction type if no users left
        if (empty($forum_data['reactions'][$user_previous_reaction])) {
            unset($forum_data['reactions'][$user_previous_reaction]);
        }
    }

    // Add new reaction or remove if clicking the same reaction
    $new_reaction = ($user_previous_reaction !== $reaction_type);
    if ($new_reaction) {
        if (!isset($forum_data['reactions'][$reaction_type])) {
            $forum_data['reactions'][$reaction_type] = [];
        }
        $forum_data['reactions'][$reaction_type][$alumni_id] = date('Y-m-d H:i:s');
    }

    // Update reaction counts
    $forum_data['reaction_counts'] = [];
    foreach ($forum_data['reactions'] as $reaction => $users) {
        $forum_data['reaction_counts'][$reaction] = count($users);
    }

    // Update the database
    $result = $firebase->update("forum", $forum_id, $forum_data);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'reaction_counts' => $forum_data['reaction_counts'],
            'user_reaction' => $new_reaction ? $reaction_type : null
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update reaction']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}