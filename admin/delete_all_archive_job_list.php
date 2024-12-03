<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php'; // Include your config file

    $firebase = new firebaseRDB($databaseURL);

    try {
        // Retrieve all records under "deleted_job"
        $jobData = $firebase->retrieve("deleted_job");
        $job = json_decode($jobData, true);

        // Check if there are records to delete
        if ($job) {
            foreach ($job as $id => $record) {
                // Delete each record individually
                $firebase->delete("deleted_job", $id);
            }
            echo json_encode(['status' => 'success', 'message' => 'All job archive records deleted permanently.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No job records to delete.']);
        }
    } catch (Exception $e) {
        // Log the exception message
        error_log('Error deleting job records: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>