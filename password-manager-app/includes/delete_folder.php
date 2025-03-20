<?php
include 'conn.php';

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred.'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $folderId = $_POST['deleteFolderId']; // Get the folder ID from the POST request

    // Check if folder ID is provided
    if (empty($folderId)) {
        $response['message'] = 'Folder ID is required.';
        echo json_encode($response);
        exit;
    }

    // Attempt to delete the folder and associated records from the database
    try {
        // Start a transactionf
        $conn->beginTransaction();

        // Delete associated passwords first
        $stmtPasswords = $conn->prepare("DELETE FROM tbl_save_passwords WHERE folder = :folder");
        $stmtPasswords->bindParam(':folder', $folderId, PDO::PARAM_STR);
        $stmtPasswords->execute();

        // Delete the folder
        $stmtFolder = $conn->prepare("DELETE FROM tbl_folder WHERE folder_id = :folderId");
        $stmtFolder->bindParam(':folderId', $folderId, PDO::PARAM_STR);
        $stmtFolder->execute();

        if ($stmtFolder->rowCount() > 0) {
            $conn->commit();
            $response['status'] = 'success';
            $response['message'] = 'Folder and associated passwords deleted successfully!';
        } else {
            $conn->rollBack();
            $response['message'] = 'Folder not found or already deleted.';
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
