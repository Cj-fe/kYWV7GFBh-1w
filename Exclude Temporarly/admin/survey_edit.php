<?php
session_start(); // Start the session

header('Content-Type: application/json'); // Set the content type to JSON

$response = array('status' => 'error', 'message' => 'An unknown error occurred');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all necessary data is provided and not empty
    $required_fields = ['id', 'edit_survey_title', 'edit_survey_desc', 'edit_survey_start', 'edit_survey_end', 'edit_survey_batch', 'edit_survey_courses'];

    $valid = true;
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $valid = false;
            break;
        }
    }

    if ($valid) {
        // Include FirebaseRDB class and initialize
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php'; // Include your config file
        $firebase = new firebaseRDB($databaseURL);
        
        // Extract ID and data to update
        $id = $_POST['id'];
        $updateData = [
            "survey_title" => $_POST['edit_survey_title'],
            "survey_desc" => $_POST['edit_survey_desc'],
            "survey_start" => $_POST['edit_survey_start'],
            "survey_end" => $_POST['edit_survey_end'],
            "survey_batch" => is_array($_POST['edit_survey_batch']) ? $_POST['edit_survey_batch'] : [$_POST['edit_survey_batch']],
            "survey_courses" => is_array($_POST['edit_survey_courses']) ? $_POST['edit_survey_courses'] : [$_POST['edit_survey_courses']]
        ];

        // Function to get existing survey data
        function getSurveyData($firebase, $id) {
            $table = 'survey_set';
            $result = $firebase->retrieve("$table/$id");
            return json_decode($result, true);
        }

        // Get existing survey data
        $existingData = getSurveyData($firebase, $id);

        // Check if the data has changed
        $dataChanged = false;
        foreach ($updateData as $key => $value) {
            if ($key === 'survey_batch' || $key === 'survey_courses') {
                // For arrays, we need to compare contents
                if (!isset($existingData[$key]) || count(array_diff($value, $existingData[$key])) > 0 || count(array_diff($existingData[$key], $value)) > 0) {
                    $dataChanged = true;
                    break;
                }
            } else if (!isset($existingData[$key]) || $existingData[$key] !== $value) {
                $dataChanged = true;
                break;
            }
        }

        if (!$dataChanged) {
            $response['status'] = 'info';
            $response['message'] = 'You have not made any changes';
        } else {
            // Function to update survey data
            function updateSurveyData($firebase, $id, $updateData) {
                $table = 'survey_set';
                $result = $firebase->update($table, $id, $updateData);
                return $result;
            }

            // Perform update
            $result = updateSurveyData($firebase, $id, $updateData);

            // Check result
            if ($result === null) {
                $response['message'] = 'Failed to update survey data in the database.';
                error_log('Firebase error: Failed to update survey data.');
            } else {
                $response['status'] = 'success';
                $response['message'] = 'Survey data updated successfully!';
            }
        }
    } else {
        $response['message'] = 'All fields are required.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response); // Return JSON response
?>