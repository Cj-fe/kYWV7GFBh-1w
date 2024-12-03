<?php
//apk_version_add.php
// Increase PHP configuration limits for large file uploads
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Extend maximum execution time (30 minutes)
ini_set('max_execution_time', 1800);
ini_set('max_input_time', 1800);

// Increase memory limit
ini_set('memory_limit', '512M');

// Increase upload file size limits
ini_set('upload_max_filesize', '250M');
ini_set('post_max_size', '250M');

session_start();
header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate version and file
    if (isset($_POST['version']) && !empty($_POST['version']) && isset($_FILES['apkFile'])) {
        $version = $_POST['version'];
        $timestamp = date('Y-m-d H:i:s'); // Current timestamp

        try {
            require_once 'includes/firebaseRDB.php';
            require_once 'includes/config.php';
            $firebase = new firebaseRDB($databaseURL);

            // Function to check if version exists
            function versionExists($firebase, $version) {
                $table = 'application';
                $applications = $firebase->retrieve($table);
                $applications = json_decode($applications, true);

                if (!is_array($applications)) {
                    return false;
                }

                foreach ($applications as $key => $application) {
                    if (is_array($application) && isset($application['version']) && strcasecmp($application['version'], $version) == 0) {
                        return true;
                    }
                }
                return false;
            }

            // Function to validate APK file
            function validateApkFile($file) {
                // Check if upload was successful
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('File upload failed. Error code: ' . $file['error']);
                }

                // Check file type
                $fileInfo = pathinfo($file['name']);
                $extension = strtolower($fileInfo['extension']);
                if ($extension !== 'apk') {
                    throw new Exception('Invalid file type. Only APK files are allowed.');
                }

                // Increased file size limit to 250 MB
                $maxFileSize = 250 * 1024 * 1024; // 250 MB
                if ($file['size'] > $maxFileSize) {
                    throw new Exception('File is too large. Maximum file size is 250 MB.');
                }

                return true;
            }

            // Function to save APK file
            function saveApkFile($file, $version) {
                // Create directory if it doesn't exist
                $uploadDir = 'uploads/apk/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Generate unique filename
                $fileName = $version . '_' . time() . '.apk';
                $uploadPath = $uploadDir . $fileName;

                // Move uploaded file
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    return $uploadPath;
                }

                throw new Exception('Failed to save APK file.');
            }

            // Function to add application details to Firebase
            function addApplication($firebase, $version, $timestamp, $apkFilePath) {
                $table = 'application';
                $data = array(
                    'version' => $version, 
                    'timestamp' => $timestamp,
                    'apk_file_path' => $apkFilePath
                );
                $result = $firebase->insert($table, $data);
                
                if ($result === false || $result === 'null') {
                    throw new Exception('Failed to add application version');
                }
                
                return $result;
            }

            // Validate version uniqueness
            if (versionExists($firebase, $version)) {
                $response['status'] = 'error';
                $response['message'] = 'Application version already exists.';
            } else {
                // Validate and save APK file
                validateApkFile($_FILES['apkFile']);
                $apkFilePath = saveApkFile($_FILES['apkFile'], $version);

                // Add application to Firebase
                $result = addApplication($firebase, $version, $timestamp, $apkFilePath);

                $response['status'] = 'success';
                $response['message'] = 'Application version and APK file added successfully!';
            }
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Version and APK file are required.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>