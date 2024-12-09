<?php
session_start();

header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['id', 'edit_title', 'edit_author', 'edit_description'];

    $valid = true;
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $valid = false;
            $missing_fields[] = $field;
        }
    }

    if ($valid) {
        require_once 'includes/firebaseRDB.php';
        require_once '../richTextEditor/autoload.php';
        require_once 'includes/config.php';
        $firebase = new firebaseRDB($databaseURL);

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        $id = htmlspecialchars($_POST['id']);
        $updateData = [
            "news_title" => htmlspecialchars($_POST['edit_title']),
            "news_author" => htmlspecialchars($_POST['edit_author']),
            "news_description" => $purifier->purify($_POST['edit_description']),
        ];

        // Fetch the existing data
        $existingData = $firebase->retrieve("news/$id");
        
        // Check if the existing data was successfully retrieved
        if ($existingData === null) {
            $response['status'] = 'error';
            $response['message'] = 'Failed to retrieve existing data from Firebase.';
            echo json_encode($response);
            exit;
        }

        // Check if any data has changed
        $dataChanged = false;
        foreach ($updateData as $key => $value) {
            if (!isset($existingData[$key]) || $existingData[$key] !== $value) {
                $dataChanged = true;
                break;
            }
        }

        $imageUploaded = isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK;

        // Handle image upload if file is provided
        if ($imageUploaded) {
            $upload_dir = 'uploads/';
            $filename = $_FILES['imageUpload']['name'];
            $tmp_name = $_FILES['imageUpload']['tmp_name'];
            $target_path = $upload_dir . basename($filename);

            if (move_uploaded_file($tmp_name, $target_path)) {
                $updateData['image_url'] = $target_path;
                $dataChanged = true;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to upload image.';
                echo json_encode($response);
                exit;
            }
        }

        if (!$dataChanged) {
            $response['status'] = 'info';
            $response['message'] = 'You have not made any changes';
        } else {
            // Perform update
            $result = $firebase->update('news', $id, $updateData);

            if ($result === null) {
                $response['status'] = 'error';
                $response['message'] = 'Failed to update news data in Firebase.';
                error_log('Firebase error: Failed to update news data.');
            } else {
                $response['status'] = 'success';
                $response['message'] = 'News data updated successfully!';
            }
        }

    } else {
        $response['status'] = 'error';
        $response['message'] = 'All fields are required.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>
