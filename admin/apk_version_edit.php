<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

$response = array();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate version ID and version
        if (isset($_POST['versionId']) && !empty($_POST['versionId']) && isset($_POST['edit_version']) && !empty($_POST['edit_version'])) {
            $versionId = $_POST['versionId'];
            $newVersion = $_POST['edit_version'];
            $timestamp = date('Y-m-d H:i:s'); // Current timestamp

            require_once 'includes/firebaseRDB.php';
            require_once 'includes/config.php';
            $firebase = new firebaseRDB($databaseURL);

            // Handle APK file upload
            $apkFilePath = null;
            if (isset($_FILES['edit_apkFile']) && $_FILES['edit_apkFile']['error'] == UPLOAD_ERR_OK) {
                // Create uploads/apk directory if it doesn't exist
                $uploadDir = 'uploads/apk/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate unique filename
                $fileExtension = pathinfo($_FILES['edit_apkFile']['name'], PATHINFO_EXTENSION);
                $newFileName = $newVersion . '_' . time() . '.' . $fileExtension;
                $apkFilePath = $uploadDir . $newFileName;

                // Move uploaded file
                if (!move_uploaded_file($_FILES['edit_apkFile']['tmp_name'], $apkFilePath)) {
                    throw new Exception('Failed to move uploaded file');
                }

                // Remove old APK file if it exists
                $existingData = $firebase->retrieve("application/{$versionId}");
                $existingData = json_decode($existingData, true);
                if (!empty($existingData['apk_file_path']) && file_exists($existingData['apk_file_path'])) {
                    unlink($existingData['apk_file_path']);
                }
            }

            // Prepare update data
            $updateData = [
                'version' => $newVersion,
                'timestamp' => $timestamp
            ];

            // Add APK file path if a new file was uploaded
            if ($apkFilePath) {
                $updateData['apk_file_path'] = $apkFilePath;
            }

            // Update Firebase database
            $updateResult = $firebase->update("application", $versionId, $updateData);

            // Ensure JSON response
            $response['status'] = 'success';
            $response['message'] = 'Application version updated successfully!';
            $response['file_path'] = $apkFilePath ? $apkFilePath : 'No new file uploaded';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Version ID and new version are required.';
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