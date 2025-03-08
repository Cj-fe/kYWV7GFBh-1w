<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['categoryId']) && !empty($_POST['categoryId'])) {
        $categoryId = $_POST['categoryId'];

        try {
            require_once 'includes/firebaseRDB.php';
            require_once 'includes/config.php';
            $firebase = new firebaseRDB($databaseURL);

            // Fetch the category first to ensure it exists
            $category = $firebase->retrieve("category/$categoryId");
            $categoryData = json_decode($category, true);

            if ($categoryData === null) {
                throw new Exception("Category not found or already deleted.");
            }

            $result = $firebase->delete("category", $categoryId);

            // Check the result
            if ($result === null || $result === false) {
                throw new Exception("Failed to delete category. Firebase returned: " . json_encode($result));
            }

            $response['status'] = 'success';
            $response['message'] = 'Category deleted successfully!';
            
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error: ' . $e->getMessage();
            error_log("Exception during category delete: " . $e->getMessage());
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Category ID is required.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>