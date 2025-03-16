<?php
include 'conn.php';

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderId = $_POST['editFolderId']; // Fetch the folder ID from POST
    $folderName = $_POST['editFolderName']; // Fetch new folder name

    // Validate inputs
    if (empty($folderId) || empty($folderName)) {
        $response['message'] = 'Folder ID and name cannot be empty.';
        echo json_encode($response);
        exit;
    }

    // Update folder name in DB
    try {
        $stmt = $conn->prepare("UPDATE tbl_folder SET folder_name = :folderName WHERE folder_id = :folderId");

        // Bind as strings
        $stmt->bindParam(':folderId', $folderId, PDO::PARAM_STR);
        $stmt->bindParam(':folderName', $folderName, PDO::PARAM_STR);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $response['status'] = 'success';
            $response['message'] = 'Folder updated successfully!';
        } else {
            $response['message'] = 'No changes were made.';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
