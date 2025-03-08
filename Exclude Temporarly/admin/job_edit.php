<?php
session_start(); // Start the session

header('Content-Type: application/json'); // Set content type to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all necessary data is provided and not empty
    $required_fields = ['id', 'edit_job_title', 'edit_company_name', 'edit_job_description', 'edit_status', 'edit_work_status', 'edit_expertise_specification', 'edit_about_the_role'];

    $valid = true;
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $valid = false;
            break;
        }
    }

    if ($valid) {
        // Include FirebaseRDB class and initialize
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php'; // Include your config file
        $firebase = new firebaseRDB($databaseURL);
    
        // Extract ID and data to update
        $id = $_POST['id'];
        $updateData = [
            "job_title" => $_POST['edit_job_title'],
            "company_name" => $_POST['edit_company_name'],
            "job_description" => $_POST['edit_job_description'],
            "expertise_specification" => $_POST['edit_expertise_specification'],
            "about_the_role" => $_POST['edit_about_the_role'],
            "status" => $_POST['edit_status'],
            "work_time" => $_POST['edit_work_status'],
            "location" => $_POST['edit_location'],  // New field
            "salary_range" => $_POST['edit_salary_range'],  // New field
        ];
    
        // Handle job categories (must be an array)
        if (isset($_POST['edit_job_categories']) && !empty($_POST['edit_job_categories'])) {
            $updateData['job_categories'] = $_POST['edit_job_categories']; // Store categories as an array
        }
    
        // Handle the primary image upload
        if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['imageUpload'];
            $imagePath = 'uploads/' . basename($image['name']);
    
            if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
                exit;
            }
    
            // Add image path to update data
            $updateData['image_path'] = $imagePath;
        }
        
        // Handle the logo image upload
        if (isset($_FILES['imageUploadlogo']) && $_FILES['imageUploadlogo']['error'] === UPLOAD_ERR_OK) {
            $logo = $_FILES['imageUploadlogo'];
            $logoPath = 'uploads/' . basename($logo['name']);
    
            if (!move_uploaded_file($logo['tmp_name'], $logoPath)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload logo image.']);
                exit;
            }
    
            // Add logo path to update data
            $updateData['logo_path'] = $logoPath;
        }
    
        // Retrieve current data
        $currentDataJson = $firebase->retrieve("job/$id");
        $currentData = json_decode($currentDataJson, true);
    
        // Check if data has changed
        $dataChanged = false;
        foreach ($updateData as $key => $value) {
            if (!isset($currentData[$key]) || $currentData[$key] != $value) {
                $dataChanged = true;
                break;
            }
        }
    
        if ($dataChanged) {
            // Perform update
            $result = $firebase->update("job", $id, $updateData);
    
            // Check result
            if ($result === null) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update job data in the database.']);
                exit;
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Job data updated successfully!']);
                exit;
            }
        } else {
            echo json_encode(['status' => 'info', 'message' => 'You have not made any changes.']);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}
?>
