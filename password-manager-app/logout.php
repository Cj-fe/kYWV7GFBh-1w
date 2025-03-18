<?php
// Include session management
require_once 'includes/auth.php';

// Log out the user
logoutUser();

// Redirect to login page
$_SESSION['success'] = "You have been successfully logged out.";
header('Location: index.php');
exit();
?>