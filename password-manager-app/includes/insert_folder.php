<?php
// Adjust the path as needed
include 'conn.php'; 

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred.'
];

if (!isset($conn)) {
    $response['message'] = 'Database connection could not be established.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $folderName = $_POST['folderName'];

    if (!empty($folderName)) {
        // Set the timezone to GMT+8 and get the current timestamp
        date_default_timezone_set('Asia/Singapore'); // GMT+8
        $createdDate = $modifiedDate = date('Y-m-d H:i:s');

        try {
            $stmt = $conn->prepare("INSERT INTO tbl_folder (folder_id, folder_name, created_date, modified_date) VALUES (UUID(), :folderName, :createdDate, :modifiedDate)");

            $stmt->bindParam(':folderName', $folderName);
            $stmt->bindParam(':createdDate', $createdDate);
            $stmt->bindParam(':modifiedDate', $modifiedDate);

            $stmt->execute();
            $response['status'] = 'success';
            $response['message'] = 'Folder added successfully!';
        } catch (PDOException $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Folder name is required.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>