<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php'; // Include your config file

    $firebase = new firebaseRDB($databaseURL);

    try {
        // Retrieve all records under "deleted_alumni"
        $alumniData = $firebase->retrieve("deleted_alumni");
        $alumni = json_decode($alumniData, true);

        // Check if there are records to delete
        if ($alumni) {
            foreach ($alumni as $id => $record) {
                // Delete each record individually
                $firebase->delete("deleted_alumni", $id);
            }
            echo json_encode(['status' => 'success', 'message' => 'All alumni archive records deleted permanently.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No alumni records to delete.']);
        }
    } catch (Exception $e) {
        // Log the exception message
        error_log('Error deleting alumni records: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>