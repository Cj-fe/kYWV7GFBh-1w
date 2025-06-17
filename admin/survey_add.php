<?php
session_start();
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'An unknown error occurred');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        $response['message'] = 'Invalid CSRF token';
        echo json_encode($response);
        exit;
    }

    // Validate required fields
    $required_fields = ['survey_title', 'survey_desc', 'survey_start', 'survey_end', 'survey_batch', 'survey_courses'];
    $missing_fields = array_filter($required_fields, function($field) {
        return !isset($_POST[$field]) || empty($_POST[$field]);
    });

    if (!empty($missing_fields)) {
        $response['message'] = 'The following fields are required: ' . implode(', ', $missing_fields);
        echo json_encode($response);
        exit;
    }

    // Sanitize and prepare data
    $survey_title = filter_var($_POST['survey_title'], FILTER_SANITIZE_STRING);
    $survey_desc = filter_var($_POST['survey_desc'], FILTER_SANITIZE_STRING);
    $survey_start = $_POST['survey_start'];
    $survey_end = $_POST['survey_end'];
    $survey_batch = is_array($_POST['survey_batch']) ? $_POST['survey_batch'] : [$_POST['survey_batch']];
    $survey_courses = is_array($_POST['survey_courses']) ? $_POST['survey_courses'] : [$_POST['survey_courses']];

    try {
        // Include Firebase RDB class and initialize
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php';
        $firebase = new firebaseRDB($databaseURL);

        // Set timezone to Asia/Manila
        date_default_timezone_set('Asia/Manila');

        $table = 'survey_set';
        $data = array(
            'survey_title' => $survey_title,
            'survey_desc' => $survey_desc,
            'survey_batch' => $survey_batch,
            'survey_courses' => $survey_courses,
            'survey_start' => $survey_start,
            'survey_end' => $survey_end,
            'surveys_created' => date('Y-m-d H:i:s')
        );

        $result = $firebase->insert($table, $data);

        if ($result === null) {
            throw new Exception('Failed to add Survey to Firebase.');
        }

        $response['status'] = 'success';
        $response['message'] = 'Survey added successfully!';

    } catch (Exception $e) {
        $response['message'] = 'An error occurred: ' . $e->getMessage();
        error_log('Firebase error: ' . $e->getMessage());
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>