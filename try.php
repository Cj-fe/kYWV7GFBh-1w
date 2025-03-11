<?php
$servername = "localhost"; // Change if needed
$username = "u510162695_mccalumni_root";
$password = "1Mccalumni_root";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to create database
$dbname = "u510162695_mccalumni";
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";

if ($conn->query($sql) === TRUE) {
    echo "Database '$dbname' created successfully";
} else {
    echo "Error creating database: " . $conn->error;
}

// Close connection
$conn->close();
?>
