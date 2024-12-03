<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        
        // Include FirebaseRDB class and config file
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php';
        
        $firebase = new firebaseRDB($databaseURL);
        
        // Retrieve the specific alumni data
        $alumniData = $firebase->retrieve("deleted_alumni/$id");
        $alumni = json_decode($alumniData, true);
        
        if ($alumni) {
            try {
                // Insert the alumni data into the alumni node using the same unique ID
                $insertResponse = $firebase->update("alumni", $id, $alumni);
                
                // Delete the alumni data from the deleted_alumni node
                $deleteResponse = $firebase->delete("deleted_alumni", $id);
                
                // Check if both operations were successful
                if ($insertResponse && $deleteResponse) {
                    echo json_encode(['status' => 'success', 'message' => 'Alumni record retrieved successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve alumni record.']);
                }
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Alumni record not found.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>