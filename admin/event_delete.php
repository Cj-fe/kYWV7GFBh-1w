<?php
session_start();
header('Content-Type: application/json');

// Function to send a JSON response
function sendResponse($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure ID is provided
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        sendResponse('error', 'ID is required.');
    }

    // Include FirebaseRDB class and initialize
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php'; // Include your config file
    $firebase = new firebaseRDB($databaseURL);

    // Extract ID to delete
    $id = $_POST['id'];

    // Function to move event data to deleted_event node
    function moveEventDataToDeleted($firebase, $id) {
        $table = 'event';
        $deletedTable = 'deleted_event';

        // Retrieve the event data
        $eventData = $firebase->retrieve($table . '/' . $id);
        $eventData = json_decode($eventData, true);

        if ($eventData) {
            // Log the data being moved
            error_log('Event data to move: ' . print_r($eventData, true));

            // Insert the data into deleted_event node
            $eventData['deleted_at'] = date('Y-m-d H:i:s'); // Add deletion timestamp
            $result = $firebase->update($deletedTable, $id, $eventData);
            return $result;
        } else {
            // Log if data retrieval failed
            error_log('Failed to retrieve event data for ID: ' . $id);
        }
        return null;
    }

    // Function to delete event data from Firebase
    function deleteEventData($firebase, $id) {
        $table = 'event';
        return $firebase->delete($table, $id);
    }

    // Move event data to deleted_event node
    $moveResult = moveEventDataToDeleted($firebase, $id);

    // Perform delete in Firebase
    $deleteResult = deleteEventData($firebase, $id);

    // Check results
    if ($moveResult === null) {
        sendResponse('error', 'Failed to move event data to Event Archive.');
    } elseif ($deleteResult === null) {
        sendResponse('error', 'Failed to delete event data from Firebase.');
    } else {
        sendResponse('success', 'Event data moved to Event Archive!');
    }
} else {
    sendResponse('error', 'Invalid request method.');
}
?>