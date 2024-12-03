<?php
session_start();

// Unset all of the session variables related to the user account
unset($_SESSION['alumni']);
unset($_SESSION['user']);
unset($_SESSION['alumni_id']);
unset($_SESSION['token']);

// Redirect to the index page
header('location: index.php');
exit();
?>
