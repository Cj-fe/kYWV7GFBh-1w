<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        
        // Include FirebaseRDB class and config file
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php';
        
        $firebase = new firebaseRDB($databaseURL);
        
        // Retrieve the specific event data
        $eventData = $firebase->retrieve("deleted_job/$id");
        $event = json_decode($eventData, true);
        
        if ($event) {
            try {
                // Insert the event data into the events node using the same unique ID
                $insertResponse = $firebase->update("job", $id, $event);
                
                // Delete the event data from the deleted_events node
                $deleteResponse = $firebase->delete("deleted_job", $id);
                
                // Check if both operations were successful
                if ($insertResponse && $deleteResponse) {
                    echo json_encode([
                        'status' => 'success', 
                        'message' => 'Event successfully restored to the calendar.'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Failed to restore the event. Please try again.'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Error occurred while restoring the event: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Event not found in the deleted events list.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Event ID is required for restoration.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid request method. Please use POST.'
    ]);
}
?>