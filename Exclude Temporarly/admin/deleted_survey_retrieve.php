<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Include FirebaseRDB class and config file
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php';

        $firebase = new firebaseRDB($databaseURL);

        // Retrieve the specific survey data
        $surveyData = $firebase->retrieve("deleted_survey_set/$id");
        $survey = json_decode($surveyData, true);

        if ($survey) {
            try {
                // Insert the survey data into the survey_set node using the same unique ID
                $insertResponse = $firebase->update("survey_set", $id, $survey);

                // Delete the survey data from the deleted_survey_set node
                $deleteResponse = $firebase->delete("deleted_survey_set", $id);

                // Check if both operations were successful
                if ($insertResponse && $deleteResponse) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Survey successfully restored.'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to restore the survey. Please try again.'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error occurred while restoring the survey: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Survey not found in the deleted surveys list.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Survey ID is required for restoration.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Please use POST.'
    ]);
}
?>