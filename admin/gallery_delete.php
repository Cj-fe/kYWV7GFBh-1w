<?php 
session_start(); 
header('Content-Type: application/json');

// Function to send a JSON response
function sendResponse($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        sendResponse('error', 'ID is required.');
    }

    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php';
    $firebase = new firebaseRDB($databaseURL);

    $id = $_POST['id'];

    function moveGalleryDataToDeleted($firebase, $id) {
        $table = 'gallery';
        $deletedTable = 'deleted_gallery';

        $galleryData = $firebase->retrieve($table . '/' . $id);
        $galleryData = json_decode($galleryData, true);

        if ($galleryData) {
            error_log('Gallery data to move: ' . print_r($galleryData, true));
            $galleryData['deleted_at'] = date('Y-m-d H:i:s');
            $result = $firebase->update($deletedTable, $id, $galleryData);
            return $result;
        } else {
            error_log('Failed to retrieve gallery data for ID: ' . $id);
            return null;
        }
    }

    function deleteGalleryData($firebase, $id) {
        $table = 'gallery';
        return $firebase->delete($table, $id);
    }

    $moveResult = moveGalleryDataToDeleted($firebase, $id);
    $deleteResult = deleteGalleryData($firebase, $id);

    if ($moveResult === null) {
        sendResponse('error', 'Failed to move gallery data to Archive Gallery.');
    } elseif ($deleteResult === null) {
        sendResponse('error', 'Failed to delete gallery data from Firebase.');
    } else {
        sendResponse('success', 'Gallery data moved to Gallery Archive!');
    }
} else {
    sendResponse('error', 'Invalid request method.');
}
?>