<?php
session_start(); // Start the session
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file
$firebase = new firebaseRDB($databaseURL);

header('Content-Type: application/json'); // Set header to return JSON response

$response = [
    'status' => 'error',
    'message' => 'Invalid request method.'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $survey_set_id = isset($_POST['survey_set_id']) ? trim($_POST['survey_set_id']) : null;
    $question = isset($_POST['question']) ? trim($_POST['question']) : null;
    $type = isset($_POST['type']) ? trim($_POST['type']) : null;
    $labels = isset($_POST['label']) ? $_POST['label'] : [];
    
    // Validate required fields
    if (empty($survey_set_id) || empty($question) || empty($type)) {
        $response['message'] = 'All fields are required.';
    } elseif (($type !== 'textfield_s') && empty($labels)) {
        // Only check labels if the type is not textfield_s
        $response['message'] = 'All fields are required.';
    } else {
        // Create formatted options if applicable
        $frm_option = [];
        if ($type !== 'textfield_s') {
            foreach ($labels as $label) {
                $frm_option[uniqid()] = $label; // Using uniqid() for unique IDs
            }
        }
        $frm_option_json = json_encode($frm_option);

        // Create data array
        $data = [
            'question' => $question,
            'frm_option' => $frm_option_json,
            'type' => $type,
            'survey_set_unique_id' => $survey_set_id,
            'date_create' => date('Y-m-d H:i:s')
        ];

        // Insert data into Firebase
        $firebase_response = $firebase->insert("questions", $data);
        if ($firebase_response) {
            $response['status'] = 'success';
            $response['message'] = 'Question added successfully.';
        } else {
            $response['message'] = 'Failed to add question.';
        }
    }
}

echo json_encode($response);
exit;
?>
