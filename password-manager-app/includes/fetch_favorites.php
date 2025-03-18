<?php
require_once 'conn.php';

$response = ['status' => 'error', 'data' => [], 'message' => ''];

try {
    $sql = "SELECT password_id FROM tbl_favorites";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $favorites = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $response['status'] = 'success';
    $response['data'] = $favorites;
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>