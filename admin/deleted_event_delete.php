<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Include FirebaseRDB class
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php'; // Include your config file

        $firebase = new firebaseRDB($databaseURL);

        // Delete the specific alumni data
        $deleteResponse = $firebase->delete("deleted_event", $id);

        // Check if the deletion was successful
        if ($deleteResponse) {
            echo json_encode(['status' => 'success', 'message' => 'Event record deleted Permanently.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete Event record.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>