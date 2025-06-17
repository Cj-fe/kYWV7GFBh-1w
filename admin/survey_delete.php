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

    // Function to move survey data to deleted_survey node
    function moveSurveyDataToDeleted($firebase, $id) {
        $table = 'survey_set';
        $deletedTable = 'deleted_survey_set';

        // Retrieve the survey data
        $surveyData = $firebase->retrieve($table . '/' . $id);
        $surveyData = json_decode($surveyData, true);

        if ($surveyData) {
            // Log the data being moved
            error_log('Survey data to move: ' . print_r($surveyData, true));

            // Add deletion timestamp
            $surveyData['deleted_at'] = date('Y-m-d H:i:s');

            // Insert the data into deleted_survey node
            $result = $firebase->update($deletedTable, $id, $surveyData);
            return $result;
        } else {
            // Log if data retrieval failed
            error_log('Failed to retrieve survey data for ID: ' . $id);
        }
        return null;
    }

    // Function to delete survey data from Firebase
    function deleteSurveyData($firebase, $id) {
        $table = 'survey_set';
        return $firebase->delete($table, $id);
    }

    // Move survey data to deleted_survey node
    $moveResult = moveSurveyDataToDeleted($firebase, $id);

    // Perform delete in Firebase
    $deleteResult = deleteSurveyData($firebase, $id);

    // Check results
    if ($moveResult === null) {
        sendResponse('error', 'Failed to move survey data to Survey Archive.');
    } elseif ($deleteResult === null) {
        sendResponse('error', 'Failed to delete survey data from Firebase.');
    } else {
        sendResponse('success', 'Survey data moved to Survey Archive!');
    }
} else {
    sendResponse('error', 'Invalid request method.');
}
?>