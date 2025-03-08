<?php
session_start(); // Start the session

// Function to send JSON response
function sendJsonResponse($success, $message) {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, $success ? 'success' : 'error' => $message]);
    exit;
}

// Generate a new token if one does not exist
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify the token
    if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        // Invalidate the token to prevent reuse
        unset($_SESSION['token']);

        // Ensure gallery_id is set
        if (isset($_POST['gallery_id']) && !empty($_POST['gallery_id'])) {
            $galleryId = $_POST['gallery_id'];

            // Handle multiple image uploads
            if (!empty(array_filter($_FILES['album_images']['name']))) {
                $imageDir = 'uploads/'; // Directory to store the uploaded images

                // Ensure the uploads directory exists
                if (!is_dir($imageDir)) {
                    mkdir($imageDir, 0777, true);
                }

                // Include Firebase RDB class and initialize
                require_once 'includes/firebaseRDB.php';
                require_once 'includes/config.php'; // Include your config file
                $firebase = new firebaseRDB($databaseURL);

                // Function to add image
                function addImage($firebase, $galleryId, $imagePath) {
                    $table = 'gallery_view'; // Assuming 'gallery_view' is your Firebase database table
                    $data = array(
                        'gallery_id' => $galleryId,
                        'image_url' => $imagePath,
                    );
                    $result = $firebase->insert($table, $data);
                    return $result;
                }

                $uploadedCount = 0;
                $totalCount = count($_FILES['album_images']['tmp_name']);

                foreach ($_FILES['album_images']['tmp_name'] as $key => $imageTmpName) {
                    $imageName = $_FILES['album_images']['name'][$key];
                    $imagePath = $imageDir . $imageName;

                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($imageTmpName, $imagePath)) {
                        // Add image to Firebase
                        $result = addImage($firebase, $galleryId, $imagePath);

                        // Check result
                        if ($result !== null) {
                            $uploadedCount++;
                        } else {
                            error_log('Firebase error: Failed to insert image data for ' . $imageName);
                        }
                    }
                }

                if ($uploadedCount > 0) {
                    if ($uploadedCount == $totalCount) {
                        sendJsonResponse(true, 'All ' . $uploadedCount . ' images uploaded successfully!');
                    } else {
                        sendJsonResponse(true, $uploadedCount . ' out of ' . $totalCount . ' images uploaded successfully.');
                    }
                } else {
                    sendJsonResponse(false, 'Failed to upload any images.');
                }
            } else {
                sendJsonResponse(false, 'No images selected.');
            }
        } else {
            sendJsonResponse(false, 'Gallery ID is required.');
        }
    } else {
        sendJsonResponse(false, 'Invalid token.');
    }
} else {
    sendJsonResponse(false, 'Invalid request method.');
}
?>
