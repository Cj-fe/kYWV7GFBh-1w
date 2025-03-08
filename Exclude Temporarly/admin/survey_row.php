<?php
// fetch_alumni.php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';

    // Firebase Realtime Database URL
    require_once 'includes/config.php';

    // Instantiate FirebaseRDB object
    $firebase = new firebaseRDB($databaseURL);

    // Retrieve specific alumni data
    $news = $firebase->retrieve("survey_set/$id");
    $news = json_decode($news, true);

    // Output alumni data as JSON
    echo json_encode($news);
}
?>
