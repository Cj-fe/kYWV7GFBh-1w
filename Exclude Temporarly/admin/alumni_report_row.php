<?php
// Include FirebaseRDB class and initialize Firebase connection
require_once 'includes/firebaseRDB.php';

// Your Firebase Realtime Database URL
require_once 'includes/config.php'; // Include your config file
$firebase = new firebaseRDB($databaseURL);

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch alumni data
    $alumniData = $firebase->retrieve("alumni/$id");
    $alumniData = json_decode($alumniData, true);

    if ($alumniData && isset($alumniData['course']) && isset($alumniData['batch'])) {
        $courseId = $alumniData['course'];
        $batchId = $alumniData['batch'];

        // Fetch course details based on course ID
        $courseData = $firebase->retrieve("course/$courseId"); // Adjust path as per your database structure
        $courseData = json_decode($courseData, true);

        // Fetch batch year based on batch ID
        $batchData = $firebase->retrieve("batch_yr/$batchId"); // Adjust path as per your database structure
        $batchData = json_decode($batchData, true);

        // Append course name and batch year to alumni data
        $alumniData['course_name'] = $courseData['course_name']; // Assuming 'course_name' is the field containing course name
        $alumniData['batch_year'] = $batchData['batch_yrs']; // Assuming 'batch_yrs' is the field containing batch year

        // Output alumni data as JSON
        echo json_encode($alumniData);
    } else {
        echo json_encode(['error' => 'Alumni not found or course/batch ID missing']);
    }
} else {
    echo json_encode(['error' => 'ID parameter not provided']);
}
?>
