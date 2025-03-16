<?php
include 'includes/conn.php';

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred.'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $passwordId = $_POST['id']; // Assuming the ID is passed in the POST request
    $websiteName = $_POST['editWebsiteName'];
    $username = $_POST['editUsername'];
    $password = $_POST['editPassword'];
    $websiteUrl = $_POST['editWebsiteUrl'];
    $folder = $_POST['editFolder'];
    $notes = $_POST['editNotes'];
    $iconFileName = null;

    // Handle file upload
    if (isset($_FILES['editIconUpload']) && $_FILES['editIconUpload']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $iconFileName = basename($_FILES['editIconUpload']['name']);
        $uploadFilePath = $uploadDir . $iconFileName;

        // Move the uploaded file to the uploads directory
        if (!move_uploaded_file($_FILES['editIconUpload']['tmp_name'], $uploadFilePath)) {
            $response['message'] = 'Failed to upload file.';
            echo json_encode($response);
            exit;
        }
    } else if (!empty($_POST['editIconUrl'])) {
        // Use the URL if no file is uploaded
        $iconFileName = $_POST['editIconUrl'];
    }

    // Set the timezone to GMT+8 and get the current timestamp
    date_default_timezone_set('Asia/Singapore'); // GMT+8
    $dateUpdated = date('Y-m-d H:i:s');

    try {
        $stmt = $conn->prepare("UPDATE tbl_save_passwords SET website_name = :websiteName, username = :username, password = :password, website_url = :websiteUrl, folder = :folder, icon_file_name = :iconFileName, notes = :notes, date_modified = :dateUpdated WHERE id = :id");
        
        $stmt->bindParam(':id', $passwordId);
        $stmt->bindParam(':websiteName', $websiteName);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':websiteUrl', $websiteUrl);
        $stmt->bindParam(':folder', $folder);
        $stmt->bindParam(':iconFileName', $iconFileName);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':dateUpdated', $dateUpdated);

        $stmt->execute();
        $response['status'] = 'success';
        $response['message'] = 'Password updated successfully!';
    } catch (PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>