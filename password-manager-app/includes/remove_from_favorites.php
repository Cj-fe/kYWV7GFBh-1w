

<?php
require_once 'conn.php';

$response = ['status' => 'error', 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password_id'])) {
        // Sanitize input
        $passwordId = htmlspecialchars($_POST['password_id']);

        // Prepare SQL query
        $sql = "DELETE FROM tbl_favorites WHERE password_id = :password_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':password_id' => $passwordId]);

        $response['status'] = 'success';
        $response['message'] = 'Favorite removed successfully.';
    } else {
        $response['message'] = 'Invalid request.';
    }
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>

