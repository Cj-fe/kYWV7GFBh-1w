<?php
require_once 'conn.php';

function fetchPasswords() {
    global $conn;
    $sql = "SELECT p.id, p.website_name, p.username, p.password, p.website_url, p.folder, p.icon_image, p.icon_file_name, p.date_created, f.folder_name
            FROM tbl_save_passwords p
            LEFT JOIN tbl_folder f ON p.folder = f.folder_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'passwords' => fetchPasswords()]);
?>