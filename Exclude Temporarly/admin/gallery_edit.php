<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all necessary data is provided and not empty
    $required_fields = ['id', 'edit_album_name'];

    $valid = true;
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $valid = false;
            $missing_fields[] = $field;
        }
    }

    if ($valid) {
        // Include FirebaseRDB class and initialize
        require_once 'includes/firebaseRDB.php';
        require_once '../vendor/autoload.php'; // Autoload HTMLPurifier if using Composer

        require_once 'includes/config.php'; // Include your config file
        $firebase = new firebaseRDB($databaseURL);

        // Extract ID and data to update
        $id = htmlspecialchars($_POST['id']);
        $updateData = [
            "gallery_name" => htmlspecialchars($_POST['edit_album_name']),
        ];

        // Handle image upload if file is provided
        if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/'; // Directory where images will be uploaded
            $filename = $_FILES['imageUpload']['name'];
            $tmp_name = $_FILES['imageUpload']['tmp_name'];
            $target_path = $upload_dir . basename($filename);

            // Move uploaded file to specified directory
            if (move_uploaded_file($tmp_name, $target_path)) {
                $updateData['image_url'] = $target_path; // Store image URL in update data
            } else {
                $_SESSION['error'] = 'Failed to upload image.';
                header('Location: gallery.php');
                exit;
            }
        }

        // Function to update gallery data
        function updateGalleryData($firebase, $id, $updateData) {
            $table = 'gallery'; // Assuming 'event' is your Firebase database node for event data
            $result = $firebase->update($table, $id, $updateData);
            return $result;
        }

        // Perform update
        $result = updateGalleryData($firebase, $id, $updateData);

        // Check result
        if ($result === null) {
            $_SESSION['error'] = 'Failed to update event data in Firebase.';
            error_log('Firebase error: Failed to update event data.');
        } else {
            $_SESSION['success'] = 'Album data updated successfully!';
        }

        // Redirect to the appropriate page (gallery.php)
        header('Location: gallery.php');
        exit;
    } else {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: gallery.php');
        exit;
    }
} else {
    $_SESSION['error'] = 'Invalid request method.';
    header('Location: gallery.php');
    exit;
}
?>
