<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include FirebaseRDB class and config file
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php';

    $firebase = new firebaseRDB($databaseURL);

    try {
        // Retrieve all records under "deleted_alumni"
        $deletedAlumniData = $firebase->retrieve("deleted_survey_set");
        $deletedAlumni = json_decode($deletedAlumniData, true);

        // Check if there are records to restore
        if ($deletedAlumni) {
            foreach ($deletedAlumni as $id => $alumni) {
                // Insert the alumni data into the "alumni" node using the same unique ID
                $insertResponse = $firebase->update("survey_set", $id, $alumni);

                // Delete the alumni data from the "deleted_alumni" node
                $deleteResponse = $firebase->delete("deleted_survey_set", $id);

                // Check if both operations were successful for each record
                if (!$insertResponse || !$deleteResponse) {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve some survey records.']);
                    exit;
                }
            }
            echo json_encode(['status' => 'success', 'message' => 'All survey records retrieved successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No survey records to retrieve.']);
        }
    } catch (Exception $e) {
        // Log the exception message
        error_log('Error retrieving survey records: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>