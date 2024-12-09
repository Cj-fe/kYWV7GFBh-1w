<?php
session_start(); // Start the session

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all form fields are set and not empty
    if (
        isset($_POST['job_title']) && !empty($_POST['job_title']) &&
        isset($_POST['company_name']) && !empty($_POST['company_name']) &&
        isset($_POST['job_description']) && !empty($_POST['job_description']) &&
        isset($_POST['status']) && !empty($_POST['status']) &&
        isset($_POST['work_status']) && !empty($_POST['work_status']) &&
        isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK &&
        isset($_FILES['imageUploadlogo']) && $_FILES['imageUploadlogo']['error'] === UPLOAD_ERR_OK &&
        isset($_POST['job_categories']) && !empty($_POST['job_categories']) &&
        isset($_POST['expertise_specification']) && !empty($_POST['expertise_specification']) &&
        isset($_POST['about_the_role']) && !empty($_POST['about_the_role'])
    ) {
        $job_title = $_POST['job_title'];
        $company_name = $_POST['company_name'];
        $job_description = $_POST['job_description'];
        $status = $_POST['status'];
        $work_time = $_POST['work_status'];
        $location = $_POST['location'];
        $salary_range = $_POST['salary_range'];
        $job_categories = json_decode($_POST['job_categories'], true);
        $expertise_specification = $_POST['expertise_specification'];
        $about_the_role = $_POST['about_the_role'];

        // Handle the image uploads
        $image = $_FILES['imageUpload'];
        $imagePath = 'uploads/' . basename($image['name']);
        
        $logo = $_FILES['imageUploadlogo'];
        $logoPath = 'uploads/' . basename($logo['name']);

        // Move the files
        if (move_uploaded_file($image['tmp_name'], $imagePath) && move_uploaded_file($logo['tmp_name'], $logoPath)) {
            // Include Firebase RDB class and initialize
            require_once 'includes/firebaseRDB.php';
            require_once 'includes/config.php';
            $firebase = new firebaseRDB($databaseURL);

            // Function to add job
            function addJob($firebase, $job_title, $company_name, $job_description, $status, $work_time, $location, $salary_range, $job_categories, $imagePath, $logoPath, $expertise_specification, $about_the_role) {
                $table = 'job';
                
                // Set timezone to Asia/Manila
                date_default_timezone_set('Asia/Manila');
                
                $data = array(
                    'job_title' => $job_title,
                    'company_name' => $company_name,
                    'job_description' => $job_description,
                    'expertise_specification' => $expertise_specification,
                    'about_the_role' => $about_the_role,
                    'status' => $status,
                    'work_time' => $work_time,
                    'location' => $location,
                    'salary_range' => $salary_range,
                    'job_categories' => $job_categories,
                    'image_path' => $imagePath,
                    'logo_path' => $logoPath,
                    'job_created' => date('F j, Y H:i:s')
                );
                $result = $firebase->insert($table, $data);
                return $result;
            }

            // Add job to Firebase
            $result = addJob($firebase, $job_title, $company_name, $job_description, $status, $work_time, $location, $salary_range, $job_categories, $imagePath, $logoPath, $expertise_specification, $about_the_role);

            // Check result
            if ($result === null) {
                $response = array('status' => 'error', 'message' => 'Failed to add job to Firebase.');
                echo json_encode($response);
                exit;
            } else {
                $response = array('status' => 'success', 'message' => 'Job added successfully!');
                echo json_encode($response);
                exit;
            }
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to upload image(s).');
            echo json_encode($response);
            exit;
        }
    } else {
        $response = array('status' => 'error', 'message' => 'All fields are required.');
        echo json_encode($response);
        exit;
    }
} else {
    $response = array('status' => 'error', 'message' => 'Invalid request method.');
    echo json_encode($response);
    exit;
}
?>