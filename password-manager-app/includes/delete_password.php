<?php
require_once 'conn.php'; // Include database connection

// Check if the request method is POST and the ID is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        // Prepare the SQL statement to delete the password entry
        $sql = "DELETE FROM tbl_save_passwords WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the statement
        if ($stmt->execute()) {
            // Response if deletion is successful
            echo json_encode(['status' => 'success', 'message' => 'Password deleted successfully.']);
        } else {
            // Response if the deletion fails
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete password.']);
        }
    } catch (PDOException $e) {
        // Handle any errors
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    // Invalid request method or missing ID
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
