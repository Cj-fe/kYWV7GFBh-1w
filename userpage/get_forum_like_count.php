<?php
session_start();
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $forum_id = $_GET['forum_id'];
    $alumni_id = $_SESSION['user']['id'];

    // Get current forum data
    $forum_data = $firebase->retrieve("forum/$forum_id");
    $forum_data = json_decode($forum_data, true);

    $reaction_counts = $forum_data['reaction_counts'] ?? [];
    $user_reaction = null;

    if (isset($forum_data['reactions'])) {
        foreach ($forum_data['reactions'] as $reaction => $users) {
            if (isset($users[$alumni_id])) {
                $user_reaction = $reaction;
                break;
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'reaction_counts' => $reaction_counts,
        'user_reaction' => $user_reaction
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}