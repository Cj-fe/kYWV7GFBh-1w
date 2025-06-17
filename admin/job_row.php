<?php
// fetch_alumni.php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';

    require_once 'includes/config.php'; // Include your config file
    $firebase = new firebaseRDB($databaseURL);

    // Retrieve specific alumni data
    $job = $firebase->retrieve("job/$id");
    $job = json_decode($job, true);

    // Output alumni data as JSON
    echo json_encode($job);
}
?>
