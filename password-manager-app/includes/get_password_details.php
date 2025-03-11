<?php
// get_password_details.php
require_once 'conn.php';

// Check if the ID parameter is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Password ID is required'
    ]);
    exit;
}

$passwordId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

// Query to get the password details
$sql = "SELECT p.*, f.folder_name
        FROM tbl_save_passwords p
        LEFT JOIN tbl_folder f ON p.folder = f.folder_id
        WHERE p.id = :id";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $passwordId, PDO::PARAM_STR); // Use PDO::PARAM_STR for VARCHAR
    $stmt->execute();
    
    $password = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($password) {
        echo json_encode([
            'status' => 'success',
            'password' => $password
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Password not found'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>