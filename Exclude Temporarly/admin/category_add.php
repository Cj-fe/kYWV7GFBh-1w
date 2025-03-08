<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['categoryName']) && !empty($_POST['categoryName'])) {
        $categoryName = $_POST['categoryName'];

        try {
            require_once 'includes/firebaseRDB.php';
            require_once 'includes/config.php';
            $firebase = new firebaseRDB($databaseURL);

            function categoryExists($firebase, $categoryName) {
                $table = 'category';
                $categories = $firebase->retrieve($table);
                $categories = json_decode($categories, true);

                if (!is_array($categories)) {
                    return false;
                }

                foreach ($categories as $key => $category) {
                    if (is_array($category) && isset($category['category_name']) && strcasecmp($category['category_name'], $categoryName) == 0) {
                        return true;
                    }
                }
                return false;
            }

            function addCategory($firebase, $categoryName) {
                $table = 'category';
                $data = array('category_name' => $categoryName);
                $result = $firebase->insert($table, $data);
                
                if ($result === false || $result === 'null') {
                    throw new Exception('Failed to add category');
                }
                
                return $result;
            }

            if (categoryExists($firebase, $categoryName)) {
                $response['status'] = 'error';
                $response['message'] = 'Category already exists.';
            } else {
                $result = addCategory($firebase, $categoryName);
                $response['status'] = 'success';
                $response['message'] = 'Category added successfully!';
            }
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Category name is required.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>