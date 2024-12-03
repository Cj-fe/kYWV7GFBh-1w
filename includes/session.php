<?php
session_start();
include 'firebaseRDB.php';

// Firebase Realtime Database URL
include 'config.php';

// Instantiate FirebaseRDB object
$firebase = new firebaseRDB($databaseURL);

// Check if the alumni session is set
if (!isset($_SESSION['alumni']) || trim($_SESSION['alumni']) == '') {
    header('location: index.php');
    exit();
}

$alumniEmail = $_SESSION['alumni'];

// Retrieve alumni data from Firebase
try {
    $alumniData = $firebase->retrieve("alumni");
    $alumniData = json_decode($alumniData, true);

    // Retrieve batch and course data from Firebase
    $batchData = $firebase->retrieve("batch_yr");
    $batchData = json_decode($batchData, true);

    $courseData = $firebase->retrieve("course");
    $courseData = json_decode($courseData, true);

    $categoryData = $firebase->retrieve("category");
    $categoryData = json_decode($categoryData, true);

} catch (Exception $e) {
    // Handle errors when retrieving data
    error_log("Error retrieving data: " . $e->getMessage());
    header('location: index.php');
    exit();
}

// Find the alumni record based on email
$authenticated = false;
foreach ($alumniData as $id => $alumni) {
    if (isset($alumni['email']) && $alumni['email'] === $alumniEmail) {
        // Map batch and course codes using unique IDs
        $batchYear = isset($batchData[$alumni['batch']]['batch_yrs']) ? $batchData[$alumni['batch']]['batch_yrs'] : 'Unknown Batch';
        $courseCode = isset($courseData[$alumni['course']]['courCode']) ? $courseData[$alumni['course']]['courCode'] : 'Unknown Course';
        
        // Check if work_classification exists and is not empty
        $workClassification = isset($alumni['work_classification']) && !empty($alumni['work_classification']) 
                              ? $alumni['work_classification'] 
                              : null;
        
        $categoryName = $workClassification && isset($categoryData[$workClassification]['category_name']) 
                        ? $categoryData[$workClassification]['category_name'] 
                        : 'Unspecified';

        // Alumni user is authenticated, store user data in session
        $user = [
            'id' => $id,
            'firstname' => $alumni['firstname'],
            'middlename' => $alumni['middlename'],
            'lastname' => $alumni['lastname'],
            'auxiliaryname' => $alumni['auxiliaryname'],
            'birthdate' => $alumni['birthdate'],
            'addressline1' => $alumni['addressline1'],
            'civilstatus' => $alumni['civilstatus'],
            'gender' => $alumni['gender'],
            'studentid' => $id, // Using the unique identifier but naming it 'studentid'
            'zipcode' => $alumni['zipcode'],
            'email' => $alumni['email'],
            'password' => $alumni['password'],
            'contactnumber' => $alumni['contactnumber'],
            'state' => $alumni['state'] ?? '',
            'city' => $alumni['city'] ?? '',
            'barangay' => $alumni['barangay'] ?? '',
            'batch' => $batchYear,
            'course' => $courseCode,
            'category' => $categoryName,
            'batch_id' => $alumni['batch'],
            'course_id' => $alumni['course'],
            'category_id' => $workClassification,
        ];
        $_SESSION['user'] = $user;
        $_SESSION['alumni_id'] = $id; // Store alumni ID in session
        $authenticated = true;
        break;
    }
}

if (!$authenticated) {
    // If the email in the session doesn't match any user, clear the session and redirect to login
    session_unset();
    session_destroy();
    header('location: index.php');
    exit();
}

// Generate CSRF token if not already set
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32)); // Generate a random token
}

$token = $_SESSION['token'];
?>