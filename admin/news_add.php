<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all form fields are set and not empty
    if (
        isset($_POST['news_title']) && !empty($_POST['news_title']) &&
        isset($_POST['news_author']) && !empty($_POST['news_author']) &&
        isset($_POST['news_description']) && !empty($_POST['news_description']) &&
        isset($_FILES['imageUpload']['name']) && !empty($_FILES['imageUpload']['name'])
    ) {
        $news_title = $_POST['news_title'];
        $news_author = $_POST['news_author'];
        $news_description = $_POST['news_description'];

        // File upload variables
        $file_name = $_FILES['imageUpload']['name'];
        $file_tmp = $_FILES['imageUpload']['tmp_name'];
        $file_size = $_FILES['imageUpload']['size'];
        $file_type = $_FILES['imageUpload']['type'];

        // Check if file is an image
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensions = array("jpeg", "jpg", "png", "gif");

        if (in_array($file_ext, $extensions) === false) {
            $response = array('status' => 'error', 'message' => "Extension not allowed, please choose a JPEG, JPG, PNG, or GIF file.");
            echo json_encode($response);
            exit;
        }

        // Include Firebase RDB class and initialize
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php'; // Include your config file
        $firebase = new firebaseRDB($databaseURL);

        date_default_timezone_set('Asia/Manila');

        function addNewsWithImage($firebase, $news_title, $news_author, $news_description, $image_url) {
            $table = 'news'; // Assuming 'news' is your Firebase database node for news
            $data = array(
                'news_title' => $news_title,
                'news_author' => $news_author,
                'news_description' => $news_description,
                'image_url' => $image_url,
                'news_created' => date('F j, Y H:i:s')  // Include current date and time
            );
            $result = $firebase->insert($table, $data);
            return $result;
        }

        // Upload image to server
        $upload_dir = 'uploads/';
        $file_name = uniqid() . '.' . $file_ext; // Unique file name to avoid conflicts
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $image_url = $file_path; // Use $file_path as the image URL

            // Add news with image URL to Firebase
            $result = addNewsWithImage($firebase, $news_title, $news_author, $news_description, $image_url);

            // Check result
            if ($result === null) {
                $response = array('status' => 'error', 'message' => 'Failed to add news to Firebase.');
                error_log('Firebase error: Failed to insert news data.');
            } else {
                $response = array('status' => 'success', 'message' => 'News added successfully!');
            }
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to upload image.');
        }
    } else {
        $response = array('status' => 'error', 'message' => 'All fields are required.');
    }
} else {
    $response = array('status' => 'error', 'message' => 'Invalid request method.');
}

// Output the JSON response
echo json_encode($response);
exit;
?>
