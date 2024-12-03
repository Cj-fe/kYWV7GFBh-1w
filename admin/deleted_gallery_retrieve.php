<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Include FirebaseRDB class and config file
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php';
        $firebase = new firebaseRDB($databaseURL);

        // Retrieve the specific gallery data
        $galleryData = $firebase->retrieve("deleted_gallery/$id");
        $gallery = json_decode($galleryData, true);

        if ($gallery) {
            try {
                // Insert the gallery data into the gallery node using the same unique ID
                $insertResponse = $firebase->update("gallery", $id, $gallery);

                // Delete the gallery data from the deleted_gallery node
                $deleteResponse = $firebase->delete("deleted_gallery", $id);

                // Check if both operations were successful
                if ($insertResponse && $deleteResponse) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Gallery successfully restored.'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Failed to restore the gallery. Please try again.'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error occurred while restoring the gallery: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gallery not found in the deleted galleries list.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gallery ID is required for restoration.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Please use POST.'
    ]);
}
?>