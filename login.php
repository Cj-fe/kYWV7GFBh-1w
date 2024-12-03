<?php
session_start();
include 'includes/firebaseRDB.php';

// Firebase Realtime Database URL
require_once 'includes/config.php'; // Include your config file
$firebase = new firebaseRDB($databaseURL);

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve alumni data from Firebase
    $alumniData = $firebase->retrieve("alumni");
    $alumniData = json_decode($alumniData, true);

    // Debug: Check if data retrieval was successful
    if ($alumniData === null) {
        $_SESSION['error'] = 'Error retrieving data from Firebase';
        header('location: includes/auth.php');
        exit();
    }

    // Search for the email in the retrieved data
    $foundUser = null;
    foreach ($alumniData as $id => $alumni) {
        if (isset($alumni['email']) && $alumni['email'] === $email) {
            $foundUser = $alumni;
            break;
        }
    }

    if ($foundUser) {
        // Verify the password using password_verify
        if (password_verify($password, $foundUser['password'])) {
            // Check if the user's status is 'notverified'
            if ($foundUser['status'] === 'notverified') {
                $_SESSION['error'] = 'Please verify your account';
                header('location: includes/auth.php');
                exit();
            }

            // Set session variables
            $_SESSION['alumni'] = $email; // Set session alumni email
            $_SESSION['forms_completed'] = $foundUser['forms_completed']; // Set session forms_completed flag

            // Generate token and store in session
            $token = generateToken();
            $_SESSION['token'] = $token;

            // Check if forms_completed flag is false
            if (!$foundUser['forms_completed']) {
                header('location: userpage/alumni_profile.php?token=' . $token); // Redirect to profile page with token
            } else {
                header('location: userpage/index.php'); // Redirect to alumni home
            }
            exit();
        } else {
            $_SESSION['error'] = 'Incorrect Email or Password';
        }
    } else {
        $_SESSION['error'] = 'Incorrect Email or Password';
    }
} else {
    $_SESSION['error'] = 'Input Alumni credentials first';
}

header('location: includes/auth.php');
exit();

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}
?>
