<?php
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

session_start();

$firebase = new firebaseRDB($databaseURL);
$current_user_id = $_SESSION['user']['id'];

updateLastNotificationCheck($firebase, $current_user_id);

echo json_encode(['count' => 0]);
