<?php
// gallery.php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';

    require_once 'includes/config.php'; // Include your config file
    $firebase = new firebaseRDB($databaseURL);

    // Retrieve specific gallery data
    $gallery = $firebase->retrieve("deleted_gallery/$id");
    $gallery = json_decode($gallery, true);

    // Output gallery data as JSON
    echo json_encode($gallery);
}
?>
