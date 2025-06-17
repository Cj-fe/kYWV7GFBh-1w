<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php'; // Include your config file

    $firebase = new firebaseRDB($databaseURL);

    try {
        // Retrieve all records under "deleted_gallery"
        $galleryData = $firebase->retrieve("deleted_gallery");
        $gallery = json_decode($galleryData, true);

        // Check if there are records to delete
        if ($gallery) {
            foreach ($gallery as $id => $record) {
                // Delete each record individually
                $firebase->delete("deleted_gallery", $id);
            }
            echo json_encode(['status' => 'success', 'message' => 'All gallery archive records deleted permanently.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No gallery records to delete.']);
        }
    } catch (Exception $e) {
        // Log the exception message
        error_log('Error deleting gallery records: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>