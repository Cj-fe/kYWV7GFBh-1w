<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure ID is provided
    if (!isset($_POST['id']) || empty(trim($_POST['id']))) {
        $_SESSION['error'] = 'ID is required.';
        echo json_encode(['status' => 'error', 'message' => 'ID is required.']);
        exit;
    }

    // Include FirebaseRDB class and initialize
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php'; // Include your config file

    $firebase = new firebaseRDB($databaseURL);

    // Extract ID
    $id = htmlspecialchars($_POST['id']);

    // Function to remove gallery data
    function removeGalleryData($firebase, $id) {
        $table = 'gallery_view'; // Assuming 'gallery_view' is your Firebase database node for gallery data

        // Retrieve the record to get the image path
        $record = $firebase->retrieve($table, $id);
        if ($record === null) {
            error_log('Firebase error: Record not found.');
            return null;
        }

        $recordData = json_decode($record, true);
        if (!isset($recordData[$id])) {
            error_log('Firebase error: Record not found in the response.');
            return null;
        }

        $imagePath = $recordData[$id]['image_url']; // Assuming 'image_url' is the field storing the image path

        // Delete the image from the local server
        if (file_exists($imagePath)) {
            if (!unlink($imagePath)) {
                error_log('Error deleting file: ' . $imagePath);
                return null;
            }
        } else {
            error_log('File not found: ' . $imagePath);
        }

        // Delete the record from Firebase Realtime Database
        $result = $firebase->delete($table, $id);
        if ($result === null) {
            error_log('Firebase error: Failed to delete record.');
            return null;
        }

        return $result;
    }

    // Perform removal
    $result = removeGalleryData($firebase, $id);

    // Check result
    if ($result === null) {
        $_SESSION['error'] = 'Failed to remove gallery data in Firebase.';
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove gallery data in Firebase.']);
    } else {
        $_SESSION['success'] = 'Gallery data removed successfully!';
        echo json_encode(['status' => 'success', 'message' => 'Gallery data removed successfully!']);
    }
    exit;
} else {
    $_SESSION['error'] = 'Invalid request method.';
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
?>