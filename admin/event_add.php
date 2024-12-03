<?php
session_start(); // Start the session

header('Content-Type: application/json'); // Set the content type to JSON
date_default_timezone_set('Asia/Manila');

// Function to send a JSON response
function sendResponse($status, $message)
{
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all form fields are set and not empty
    if (
        isset($_POST['event_date']) && !empty($_POST['event_date']) &&
        isset($_POST['event_venue']) && !empty($_POST['event_venue']) &&
        isset($_POST['event_title']) && !empty($_POST['event_title']) &&
        isset($_POST['event_author']) && !empty($_POST['event_author']) &&
        isset($_POST['event_description']) && !empty($_POST['event_description']) &&
        isset($_FILES['imageUpload']['name']) && !empty($_FILES['imageUpload']['name']) &&
        isset($_POST['event_invited']) && !empty($_POST['event_invited']) &&
        isset($_POST['course_invited']) && !empty($_POST['course_invited'])
    ) {
        $event_title = $_POST['event_title'];
        $event_venue = $_POST['event_venue'];
        $event_date = $_POST['event_date'];
        $event_author = $_POST['event_author'];
        $event_description = $_POST['event_description'];
        $event_invited = $_POST['event_invited']; // This will be an array of selected values
        $course_invited = $_POST['course_invited']; // This will also be an array of selected values

        // Convert the arrays to JSON strings to store in the database
        $event_invited_json = json_encode($event_invited);
        $course_invited_json = json_encode($course_invited);

        // File upload variables
        $file_name = $_FILES['imageUpload']['name'];
        $file_tmp = $_FILES['imageUpload']['tmp_name'];
        $file_size = $_FILES['imageUpload']['size'];
        $file_type = $_FILES['imageUpload']['type'];

        // Check if file is an image
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensions = array("jpeg", "jpg", "png", "gif");

        if (in_array($file_ext, $extensions) === false) {
            sendResponse('error', 'Extension not allowed, please choose a JPEG, JPG, PNG, or GIF file.');
        }

        // Include Firebase RDB class and initialize
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php'; // Include your config file
        $firebase = new firebaseRDB($databaseURL);

        // Function to upload image and add event
        function addEventWithImage($firebase, $event_title, $event_venue, $event_date, $event_author, $event_description, $image_url, $event_invited_json, $course_invited_json)
        {
            $table = 'event'; // Assuming 'event' is your Firebase database node for event
            $data = array(
                'event_title' => $event_title,
                'event_venue' => $event_venue,
                'event_date' => $event_date,
                'event_author' => $event_author,
                'event_description' => $event_description,
                'image_url' => $image_url,
                'event_invited' => $event_invited_json,
                'course_invited' => $course_invited_json,
                'event_created' => date('F j, Y H:i:s') // Updated format here
            );
            return $firebase->insert($table, $data);
        }

        // Upload image to server
        $upload_dir = 'uploads/';
        $file_name = uniqid() . '.' . $file_ext; // Unique file name to avoid conflicts
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $image_url = $file_path; // Use $file_path as the image URL

            // Add event with image URL to Firebase
            $result = addEventWithImage($firebase, $event_title, $event_venue, $event_date, $event_author, $event_description, $image_url, $event_invited_json, $course_invited_json);

            // Check result
            if ($result === null) {
                sendResponse('error', 'Failed to add event to Firebase.');
            } else {
                sendResponse('success', 'Event added successfully!');
            }
        } else {
            sendResponse('error', 'Failed to upload image.');
        }
    } else {
        sendResponse('error', 'All fields are required.');
    }
} else {
    sendResponse('error', 'Invalid request method.');
}
?>