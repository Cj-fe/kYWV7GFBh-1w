<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');
$response = array();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate version ID
        if (isset($_POST['versionId']) && !empty($_POST['versionId'])) {
            $versionId = $_POST['versionId'];
            require_once 'includes/firebaseRDB.php';
            require_once 'includes/config.php';
            $firebase = new firebaseRDB($databaseURL);

            // Retrieve existing data
            $existingData = $firebase->retrieve("application/{$versionId}");
            $existingData = json_decode($existingData, true);

            if (!empty($existingData)) {
                // Construct the file path
                $filePath = 'uploads/apk/' . $existingData['apk_file_path'];

                // Remove APK file if it exists
                if (!empty($existingData['apk_file_path']) && file_exists($filePath)) {
                    if (!unlink($filePath)) {
                        throw new Exception('Failed to delete APK file');
                    }
                }

                // Delete the version from Firebase
                $deleteResult = $firebase->delete("application", $versionId);
                if ($deleteResult) {
                    $response['status'] = 'success';
                    $response['message'] = 'Application version and associated file deleted successfully!';
                } else {
                    throw new Exception('Failed to delete application version from database');
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Version not found.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Version ID is required.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid request method.';
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'Error: ' . $e->getMessage();
    error_log('Exception: ' . $e->getMessage());
}

// Ensure clean JSON output
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
?>