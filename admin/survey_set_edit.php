<?php
session_start(); // Start the session
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);
$response = array('status' => 'error', 'message' => 'An unexpected error occurred.');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $question_id = isset($_POST['question_id']) ? $_POST['question_id'] : '';
    $survey_set_unique_id = isset($_POST['survey_set_unique_id']) ? $_POST['survey_set_unique_id'] : '';
    $question = isset($_POST['question']) ? $_POST['question'] : '';
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $labels = isset($_POST['label']) ? $_POST['label'] : [];

    // Validate the data
    if ($question_id && $survey_set_unique_id && $question && $type) {
        // Retrieve the current data from Firebase
        $current_data = $firebase->retrieve("questions/$question_id");
        $current_data = json_decode($current_data, true);

        // Create formatted options
        $frm_option = [];
        foreach ($labels as $label) {
            $frm_option[uniqid()] = $label; // Using uniqid() for unique IDs
        }
        $frm_option_json = json_encode($frm_option);

        // Create data array
        $update_data = [
            'question' => $question,
            'frm_option' => $frm_option_json,
            'type' => $type,
            'survey_set_unique_id' => $survey_set_unique_id,
            'date_create' => date('Y-m-d H:i:s') // Update creation date
        ];

        // Check if the data has changed
        if ($update_data['question'] == $current_data['question'] &&
            $update_data['frm_option'] == $current_data['frm_option'] &&
            $update_data['type'] == $current_data['type'] &&
            $update_data['survey_set_unique_id'] == $current_data['survey_set_unique_id']) {
            $response = array('status' => 'info', 'message' => 'No data change.');
        } else {
            // Update data in Firebase
            $update_response = $firebase->update("questions", $question_id, $update_data);

            if ($update_response) {
                $response = array('status' => 'success', 'message' => 'Question updated successfully.');
            } else {
                $response['message'] = 'Failed to update question.';
            }
        }
    } else {
        $response['message'] = 'Invalid data.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
