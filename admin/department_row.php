<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php';

$firebase = new firebaseRDB($databaseURL);
$dataDepartmentts = $firebase->retrieve("departments");
$dataDepartmentts = json_decode($dataDepartmentts, true);

echo json_encode($dataDepartmentts);
?>
