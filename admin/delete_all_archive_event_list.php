<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php'; // Include your config file

    $firebase = new firebaseRDB($databaseURL);

    try {
        // Retrieve all records under "deleted_event"
        $eventData = $firebase->retrieve("deleted_event");
        $event = json_decode($eventData, true);

        // Check if there are records to delete
        if ($event) {
            foreach ($event as $id => $record) {
                // Delete each record individually
                $firebase->delete("deleted_event", $id);
            }
            echo json_encode(['status' => 'success', 'message' => 'All event archive records deleted permanently.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No event records to delete.']);
        }
    } catch (Exception $e) {
        // Log the exception message
        error_log('Error deleting event records: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>