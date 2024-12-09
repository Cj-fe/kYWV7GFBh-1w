<?php
// fetch_survey.php

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Include FirebaseRDB class
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php';

    // Instantiate FirebaseRDB object
    $firebase = new firebaseRDB($databaseURL);

    // Retrieve specific survey data
    $survey = $firebase->retrieve("deleted_survey_set/$id");
    $survey = json_decode($survey, true);

    // Output survey data as JSON
    echo json_encode($survey);
}
?>