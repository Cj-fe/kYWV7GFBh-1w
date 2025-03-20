<?php
require_once 'conn.php';

$response = ['status' => 'error', 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password_id'])) {
        // Sanitize input
        $passwordId = htmlspecialchars($_POST['password_id']);
        $currentDate = date('Y-m-d');

        // Check if the password_id already exists in the favorites table
        $checkSql = "SELECT COUNT(*) FROM tbl_favorites WHERE password_id = :password_id";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([':password_id' => $passwordId]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $response['status'] = 'info';
            $response['message'] = 'This is already added';
        } else {
            // Prepare SQL query
            $sql = "INSERT INTO tbl_favorites (id, password_id, created_at, updated_at) 
                    VALUES (:id, :password_id, :created_at, :updated_at)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':id' => hash('sha256', uniqid(rand(), true)), // Unique ID
                ':password_id' => $passwordId,
                ':created_at' => $currentDate,
                ':updated_at' => $currentDate
            ]);

            $response['status'] = 'success';
            $response['message'] = 'Added to favorites.';
        }
    } else {
        $response['message'] = 'Invalid request.';
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>