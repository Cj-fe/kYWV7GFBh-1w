<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['categoryId']) && !empty($_POST['categoryId']) && 
        isset($_POST['categoryName']) && !empty($_POST['categoryName'])) {
        
        $categoryId = $_POST['categoryId'];
        $categoryName = $_POST['categoryName'];

        try {
            require_once 'includes/firebaseRDB.php';
            require_once 'includes/config.php';
            $firebase = new firebaseRDB($databaseURL);

            function categoryExistsExcept($firebase, $categoryName, $exceptId) {
                $table = 'category';
                $categories = $firebase->retrieve($table);
                $categories = json_decode($categories, true);

                if (!is_array($categories)) {
                    return false;
                }

                foreach ($categories as $key => $category) {
                    if ($key !== $exceptId && 
                        is_array($category) && 
                        isset($category['category_name']) && 
                        strcasecmp($category['category_name'], $categoryName) == 0) {
                        return true;
                    }
                }
                return false;
            }

            function updateCategory($firebase, $categoryId, $categoryName) {
                $table = 'category';
                $data = array('category_name' => $categoryName);
                $result = $firebase->update($table, $categoryId, $data);
                
                if ($result === false || $result === 'null') {
                    throw new Exception('Failed to update category');
                }
                
                return $result;
            }

            if (categoryExistsExcept($firebase, $categoryName, $categoryId)) {
                $response['status'] = 'error';
                $response['message'] = 'A category with this name already exists.';
            } else {
                $result = updateCategory($firebase, $categoryId, $categoryName);
                $response['status'] = 'success';
                $response['message'] = 'Category updated successfully!';
            }
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Category ID and name are required.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>