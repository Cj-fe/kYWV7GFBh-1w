<?php
session_start(); // Start the session
header('Content-Type: application/json'); // Set the content type to JSON
$response = array(); // Initialize response array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure ID is provided
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $response['status'] = 'error';
        $response['message'] = 'ID is required.';
        echo json_encode($response);
        exit;
    }

    // Include FirebaseRDB class and initialize
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php'; // Include your updated config file
    $firebase = new firebaseRDB($databaseURL);

    // Extract ID to delete
    $id = $_POST['id'];

    // Function to move news data to deleted_news node in Firebase
    function moveNewsDataToDeleted($firebase, $id) {
        $table = 'news';
        $deletedTable = 'deleted_news';
        
        // Retrieve the news data
        $newsData = $firebase->retrieve($table . '/' . $id);
        $newsData = json_decode($newsData, true);
        
        if ($newsData) {
            // Debug: Log the data being moved
            error_log('News data to move: ' . print_r($newsData, true));
            
            // Insert the data into deleted_news node
            $newsData['deleted_at'] = date('Y-m-d H:i:s'); // Add deletion timestamp
            $result = $firebase->update($deletedTable, $id, $newsData); // Corrected to pass three arguments
            return $result;
        } else {
            // Debug: Log if data retrieval failed
            error_log('Failed to retrieve news data for ID: ' . $id);
        }
        return null;
    }

    // Function to delete news data from Firebase
    function deleteNewsDataFromFirebase($firebase, $id) {
        $table = 'news'; // Assuming 'news' is your Firebase database node for news data
        $result = $firebase->delete($table, $id);
        return $result;
    }

    // Move news data to deleted_news node
    $moveResult = moveNewsDataToDeleted($firebase, $id);

    // Perform delete in Firebase
    $firebaseResult = deleteNewsDataFromFirebase($firebase, $id);

    // Check results
    if ($moveResult === null) {
        $response['status'] = 'error';
        $response['message'] = 'Failed to move news data to News Archive.';
    } elseif ($firebaseResult === null) {
        $response['status'] = 'error';
        $response['message'] = 'Failed to delete news data from Firebase.';
        error_log('Firebase error: Failed to delete news data.');
    } else {
        $response['status'] = 'success';
        $response['message'] = 'News data moved to News Archive!';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

// Output JSON response
echo json_encode($response);
?>