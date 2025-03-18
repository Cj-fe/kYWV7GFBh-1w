
<?php
require_once 'conn.php';

try {
    $sql = "SELECT folder_id, folder_name FROM tbl_folder";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $folders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($folders);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch folders: ' . $e->getMessage()
    ]);
}
?>
