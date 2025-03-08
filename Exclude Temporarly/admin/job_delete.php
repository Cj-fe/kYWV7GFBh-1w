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

    // Function to move job data to deleted_job node
    function moveJobDataToDeleted($firebase, $id) {
        $table = 'job';
        $deletedTable = 'deleted_job';

        // Retrieve the job data
        $jobData = $firebase->retrieve($table . '/' . $id);
        $jobData = json_decode($jobData, true);

        if ($jobData) {
            // Log the data being moved
            error_log('Job data to move: ' . print_r($jobData, true));

            // Insert the data into deleted_job node
            $jobData['deleted_at'] = date('Y-m-d H:i:s'); // Add deletion timestamp
            $result = $firebase->update($deletedTable, $id, $jobData);
            return $result;
        } else {
            // Log if data retrieval failed
            error_log('Failed to retrieve job data for ID: ' . $id);
        }
        return null;
    }

    // Function to delete job data from Firebase
    function deleteJobData($firebase, $id) {
        $table = 'job';
        return $firebase->delete($table, $id);
    }

    // Move job data to deleted_job node
    $moveResult = moveJobDataToDeleted($firebase, $id);

    // Perform delete in Firebase
    $deleteResult = deleteJobData($firebase, $id);

    // Check results
    if ($moveResult === null) {
        sendResponse('error', 'Failed to move job data to Job Archive.');
    } elseif ($deleteResult === null) {
        sendResponse('error', 'Failed to delete job data from Firebase.');
    } else {
        sendResponse('success', 'Job data moved to Job Archive!');
    }
} else {
    sendResponse('error', 'Invalid request method.');
}
?>