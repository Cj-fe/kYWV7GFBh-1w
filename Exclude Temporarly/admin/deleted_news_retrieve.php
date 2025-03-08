<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        
        // Include FirebaseRDB class and config file
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php';
        
        $firebase = new firebaseRDB($databaseURL);
        
        // Retrieve the specific news data
        $newsData = $firebase->retrieve("deleted_news/$id");
        $news = json_decode($newsData, true);
        
        if ($news) {
            try {
                // Insert the news data into the news node using the same unique ID
                $insertResponse = $firebase->update("news", $id, $news);
                
                // Delete the news data from the deleted_news node
                $deleteResponse = $firebase->delete("deleted_news", $id);
                
                // Check if both operations were successful
                if ($insertResponse && $deleteResponse) {
                    echo json_encode(['status' => 'success', 'message' => 'News article restored successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to restore news article.']);
                }
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'News article not found.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>