<?php
include 'includes/session.php'; // Include session or any necessary files

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $ipToBlock = $data['ip'];

    // Retrieve logs from Firebase
    $logsData = $firebase->retrieve("logs");
    $logs = json_decode($logsData, true) ?: [];

    // Update the logs with the block key
    foreach ($logs as &$log) {
        if (isset($log['ip']) && $log['ip'] === $ipToBlock) {
            $log['block'] = true;
            break; // Update only one entry as per your requirement
        }
    }

    // Save the updated logs back to Firebase
    $updateResult = $firebase->update("logs", '', $logs);

    if ($updateResult) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>