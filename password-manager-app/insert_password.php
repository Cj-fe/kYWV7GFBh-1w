<?php
    include 'includes/conn.php';
    
    header('Content-Type: application/json');
    
    $response = [
        'status' => 'error',
        'message' => 'An unknown error occurred.'
    ];
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $websiteName = $_POST['websiteName'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $websiteUrl = $_POST['websiteUrl'];
        $folder = $_POST['folder'];
        $notes = $_POST['notes'];
        $iconFileName = null;
        $iconOption = null; // New variable to store the icon option
    
        // Handle file upload
        if (isset($_FILES['iconUpload']) && $_FILES['iconUpload']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $iconFileName = basename($_FILES['iconUpload']['name']);
            $uploadFilePath = $uploadDir . $iconFileName;
    
            // Move the uploaded file to the uploads directory
            if (!move_uploaded_file($_FILES['iconUpload']['tmp_name'], $uploadFilePath)) {
                $response['message'] = 'Failed to upload file.';
                echo json_encode($response);
                exit;
            }
            $iconOption = 1; // Set icon option to 1 for file upload
        } else if (!empty($_POST['iconUrl'])) {
            // Use the URL if no file is uploaded
            $iconFileName = $_POST['iconUrl'];
            $iconOption = 2; // Set icon option to 2 for URL
        }
    
        // Set the timezone to GMT+8 and get the current timestamp
        date_default_timezone_set('Asia/Singapore'); // GMT+8
        $dateCreated = date('Y-m-d H:i:s');
    
        try {
            $stmt = $conn->prepare("INSERT INTO tbl_save_passwords (id, website_name, username, password, website_url, folder, icon_file_name, icon_option, notes, date_created) VALUES (UUID(), :websiteName, :username, :password, :websiteUrl, :folder, :iconFileName, :iconOption, :notes, :dateCreated)");
            
            $stmt->bindParam(':websiteName', $websiteName);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':websiteUrl', $websiteUrl);
            $stmt->bindParam(':folder', $folder);
            $stmt->bindParam(':iconFileName', $iconFileName);
            $stmt->bindParam(':iconOption', $iconOption); // Bind the icon option
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':dateCreated', $dateCreated);
    
            $stmt->execute();
            $response['status'] = 'success';
            $response['message'] = 'Password saved successfully!';
        } catch (PDOException $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    }
    
    echo json_encode($response);
    ?>