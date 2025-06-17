<?php
session_start();
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include the config file

// Initialize Firebase URL
$firebase = new firebaseRDB($databaseURL);

if (isset($_POST['check'])) {
    $_SESSION['info'] = "";
    $otp_code = $_POST['otp']; // Directly use the OTP code from the input
    $errors = array();

    // Retrieve alumni data
    $data = $firebase->retrieve("alumni");
    $data = json_decode($data, true);
    $alumni_id = null;
    $email = null;

    // Check if there is a match for otp code
    foreach ($data as $id => $alumni) {
        if (isset($alumni['code']) && $alumni['code'] == $otp_code) {
            $alumni_id = $id;
            $email = $alumni['email'];
            break;
        }
    }

    if (!$alumni_id) {
        $error = "Incorrect verification code. Please try again.";
        header("Location: user-otp.php?error=" . urlencode($error));
        exit();
    } else {
        $code = 0;
        $status = 'verified';

        // Update alumni data
        $update_data = [
            'code' => $code,
            'status' => $status
        ];

        try {
            // Update user data in Firebase
            $firebase->update($table, "alumni/$alumni_id", $update_data);

            // Set session variables
            $_SESSION['email'] = $email;

            // Redirect to home page
            header('Location: index.php');
            exit();
        } catch (Exception $e) {
            $errors['otp-error'] = "Failed while updating code!";
        }
    }

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
}
?>