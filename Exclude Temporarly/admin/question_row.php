<?php
require_once 'includes/firebaseRDB.php';

require_once 'includes/config.php'; // Include your config file
$firebase = new firebaseRDB($databaseURL);
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $question = $firebase->retrieve("questions/$id");
    echo $question;
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
