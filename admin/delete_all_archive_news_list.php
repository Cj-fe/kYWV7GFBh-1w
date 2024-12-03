<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php'; // Include your config file

    $firebase = new firebaseRDB($databaseURL);

    try {
        // Retrieve all records under "deleted_news"
        $newsData = $firebase->retrieve("deleted_news");
        $news = json_decode($newsData, true);

        // Check if there are records to delete
        if ($news) {
            foreach ($news as $id => $record) {
                // Delete each record individually
                $firebase->delete("deleted_news", $id);
            }
            echo json_encode(['status' => 'success', 'message' => 'All news archive records deleted permanently.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No news records to delete.']);
        }
    } catch (Exception $e) {
        // Log the exception message
        error_log('Error deleting news records: ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>