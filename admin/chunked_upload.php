<?php
// chunked_upload.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Increase PHP configuration limits for large file uploads
ini_set('max_execution_time', 1800);
ini_set('max_input_time', 1800);
ini_set('memory_limit', '512M');
ini_set('upload_max_filesize', '250M');
ini_set('post_max_size', '250M');

session_start();
header('Content-Type: application/json');

$uploadDir = 'uploads/chunks/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$response = ['status' => 'error', 'message' => 'Unknown error'];

try {
    // Validate inputs
    if (!isset($_POST['uniqueId']) || !isset($_POST['currentChunk']) || 
        !isset($_POST['totalChunks']) || !isset($_FILES['file'])) {
        throw new Exception('Invalid upload parameters');
    }

    $uniqueId = $_POST['uniqueId'];
    $currentChunk = (int)$_POST['currentChunk'];
    $totalChunks = (int)$_POST['totalChunks'];
    $version = $_POST['version'] ?? 'unknown';

    // Validate file
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $_FILES['file']['error']);
    }

    // Create chunk filename
    $chunkFilename = $uploadDir . $uniqueId . '_chunk_' . $currentChunk . '.part';
    
    // Move uploaded chunk
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $chunkFilename)) {
        throw new Exception('Failed to save chunk');
    }

    // Respond with success
    $response = [
        'status' => 'chunk_received', 
        'chunk' => $currentChunk,
        'message' => "Chunk $currentChunk uploaded successfully"
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;