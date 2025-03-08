<?php

require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php';

$firebase = new firebaseRDB($databaseURL);

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $date = date("Y-m-d");

    // Insert the new contact form data
    $data = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'date' => $date
    ];

    // Store the data in Firebase
    $result = $firebase->insert("contact_query", $data);

    // Redirect to contact.php with success or error message
    if ($result) {
        header("Location: contact.php?status=success");
        exit(); // Make sure the script stops after redirection
    } else {
        header("Location: contact.php?status=error");
        exit(); // Make sure the script stops after redirection
    }
}
?>
