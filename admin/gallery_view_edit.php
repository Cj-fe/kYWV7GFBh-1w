<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all necessary data is provided and not empty
    $required_fields = ['id'];

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

        require_once 'includes/config.php'; // Include your config file
        $firebase = new firebaseRDB($databaseURL);
        // Extract ID and data to update
        $id = htmlspecialchars($_POST['id']);
        $updateData = [];

        // Handle image upload if file is provided
        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/'; // Directory where images will be uploaded
            $original_filename = basename($_FILES['imageFile']['name']);
            $tmp_name = $_FILES['imageFile']['tmp_name'];
            
            // Use the user-provided new file name if available, otherwise use the original file name
            if (!empty($_POST['newFileName'])) {
                $newFileName = htmlspecialchars($_POST['newFileName']);
                $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
                $new_filename = $newFileName . '.' . $extension;
            } else {
                $new_filename = $original_filename;
            }
            
            $target_path = $upload_dir . $new_filename;

            // Move uploaded file to specified directory with the new file name
            if (move_uploaded_file($tmp_name, $target_path)) {
                $updateData['image_url'] = $target_path; // Store new image URL in update data
            } else {
                $_SESSION['error'] = 'Failed to upload image.';
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
                exit;
            }
        } else {
            // Handle renaming the existing file if no new file is uploaded and a new file name is provided
            if (!empty($_POST['newFileName']) && !empty($_POST['image_url'])) {
                $current_image_url = htmlspecialchars($_POST['image_url']);
                $current_path = $current_image_url;
                $extension = pathinfo($current_path, PATHINFO_EXTENSION);
                $new_filename = htmlspecialchars($_POST['newFileName']) . '.' . $extension;
                $new_path = 'uploads/' . $new_filename;

                // Rename the existing file
                if (rename($current_path, $new_path)) {
                    $updateData['image_url'] = $new_path; // Update the image URL with the new file name
                } else {
                    $_SESSION['error'] = 'Failed to rename existing image.';
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'Failed to rename existing image.']);
                    exit;
                }
            }
        }

        // Function to update gallery data
        function updateGalleryData($firebase, $id, $updateData) {
            $table = 'gallery_view'; // Assuming 'gallery_view' is your Firebase database node for gallery data
            $result = $firebase->update($table, $id, $updateData);
            return $result;
        }

        // Perform update if there's data to update
        if (!empty($updateData)) {
            $result = updateGalleryData($firebase, $id, $updateData);

            // Check result
            if ($result === null) {
                $_SESSION['error'] = 'Failed to update gallery data in Firebase.';
                error_log('Firebase error: Failed to update gallery data.');
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Failed to update gallery data in Firebase.']);
                exit;
            } else {
                $_SESSION['success'] = 'Gallery data updated successfully!';
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Gallery data updated successfully!']);
                exit;
            }
        } else {
            $_SESSION['error'] = 'No data to update.';
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'You have not made any changes']);
            exit;
        }
    } else {
        $_SESSION['error'] = 'All fields are required.';
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }
} else {
    $_SESSION['error'] = 'Invalid request method.';
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
?>
