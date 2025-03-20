
<?php
require_once 'conn.php';

try {
    if (isset($_GET['folder_id'])) {
        $folderId = intval($_GET['folder_id']);

        $sql = "SELECT folder_id, folder_name FROM tbl_folder WHERE folder_id = :folder_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':folder_id', $folderId, PDO::PARAM_INT);
        $stmt->execute();

        $folder = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($folder) {
            echo json_encode([
                'status' => 'success',
                'folder' => $folder
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Folder not found'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Folder ID not provided'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch folder: ' . $e->getMessage()
    ]);
}
?>
