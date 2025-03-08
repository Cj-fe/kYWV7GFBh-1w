
<?php
session_start();
header('Content-Type: application/json');

$uploadDir = 'uploads/chunks/';
$finalUploadDir = 'uploads/apk/';

$response = ['status' => 'error', 'message' => 'Unknown error'];

try {
    // Validate inputs
    if (!isset($_POST['uniqueId']) || !isset($_POST['version']) || 
        !isset($_POST['totalChunks'])) {
        throw new Exception('Invalid finalization parameters');
    }

    $uniqueId = $_POST['uniqueId'];
    $version = $_POST['version'];
    $totalChunks = (int)$_POST['totalChunks'];

    // Ensure final upload directory exists
    if (!is_dir($finalUploadDir)) {
        mkdir($finalUploadDir, 0755, true);
    }

    // Final file path
    $finalFilePath = $finalUploadDir . $version . '_' . time() . '.apk';

    // Combine chunks
    $finalFile = fopen($finalFilePath, 'wb');
    if (!$finalFile) {
        throw new Exception('Could not create final file');
    }

    for ($i = 0; $i < $totalChunks; $i++) {
        $chunkFilename = $uploadDir . $uniqueId . '_chunk_' . $i . '.part';
        
        if (!file_exists($chunkFilename)) {
            throw new Exception("Missing chunk: $i");
        }

        $chunk = file_get_contents($chunkFilename);
        fwrite($finalFile, $chunk);
        
        // Delete chunk after processing
        unlink($chunkFilename);
    }

    fclose($finalFile);

    // Validate final file
    if (!file_exists($finalFilePath)) {
        throw new Exception('Final file creation failed');
    }

    // Add to Firebase (similar to your original code)
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php';
    
    $firebase = new firebaseRDB($databaseURL);
    $timestamp = date('Y-m-d H:i:s');
    
    $data = [
        'version' => $version,
        'timestamp' => $timestamp,
        'apk_file_path' => $finalFilePath
    ];
    
    $result = $firebase->insert('application', $data);

    if ($result === false || $result === 'null') {
        throw new Exception('Failed to add application version to database');
    }

    $response = [
        'status' => 'success', 
        'message' => 'APK file uploaded successfully',
        'filepath' => $finalFilePath
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
?>