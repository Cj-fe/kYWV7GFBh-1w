<?php
// fetch_alumni.php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';

    // Firebase Realtime Database URL
    require_once 'includes/config.php'; // Include your config file
    $firebase = new firebaseRDB($databaseURL);

    // Retrieve specific alumni data
    $event = $firebase->retrieve("deleted_event/$id");
    $event = json_decode($event, true);

    // Output alumni data as JSON
    echo json_encode($event);
}
?>
