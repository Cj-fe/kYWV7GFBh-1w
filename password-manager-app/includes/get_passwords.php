
<?php
require_once 'conn.php';

try {
    // Modified SQL query to include favorite status
    $sql = "SELECT p.id, p.website_name, p.username, p.password, p.website_url, p.folder, 
                   p.icon_image, p.icon_file_name, p.date_created, f.folder_name,
                   CASE WHEN fav.id IS NOT NULL THEN 1 ELSE 0 END as is_favorite
            FROM tbl_save_passwords p
            LEFT JOIN tbl_folder f ON p.folder = f.folder_id
            LEFT JOIN tbl_favorites fav ON p.id = fav.password_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $passwords = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'passwords' => $passwords
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch passwords: ' . $e->getMessage()
    ]);
}
?>
