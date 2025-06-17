<?php 
include '../includes/session.php';
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

// Set proper headers
header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Unknown error occurred'
];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forum_id'])) {
        $forum_id = $_POST['forum_id'];
        $firebase = new firebaseRDB($databaseURL);

        // Get the forum post to check ownership
        $forum_data = $firebase->retrieve("forum/$forum_id");
        $forum_post = json_decode($forum_data, true);

        // Verify that the current user is the owner of the post
        if ($forum_post && isset($forum_post['alumniId']) && $forum_post['alumniId'] === $_SESSION['user']['id']) {
            // Delete the forum post
            $result = $firebase->delete("forum", $forum_id);
            
            if ($result) {
                $response = [
                    'status' => 'success',
                    'message' => 'Forum post deleted successfully'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Failed to delete forum post'
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'You do not have permission to delete this post'
            ];
        }
    }
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => 'An error occurred: ' . $e->getMessage()
    ];
}

// Ensure proper JSON response
echo json_encode($response);
exit;