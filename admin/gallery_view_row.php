<?php
// gallery.php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';

    require_once 'includes/config.php'; // Include your config file
    $firebase = new firebaseRDB($databaseURL);

    // Retrieve specific gallery data
    $gallery_view = $firebase->retrieve("gallery_view/$id");
    $gallery_view = json_decode($gallery_view
    , true);

    // Output gallery data as JSON
    echo json_encode($gallery_view);
}
?>
