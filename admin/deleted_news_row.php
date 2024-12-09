<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php';

    $firebase = new firebaseRDB($databaseURL);

    // Retrieve specific news data
    $news = $firebase->retrieve("deleted_news/$id");
    $news = json_decode($news, true);

    if ($news) {
        echo json_encode($news);
    } else {
        error_log("Failed to retrieve news with ID: $id"); // Debugging line
        echo json_encode(['error' => 'News not found']);
    }
} else {
    error_log("ID not set in request"); // Debugging line
    echo json_encode(['error' => 'Invalid request']);
}
?>