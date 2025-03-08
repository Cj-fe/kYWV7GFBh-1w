<?php
session_start();
header('Content-Type: application/json');
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php';

    $firebase = new firebaseRDB($databaseURL);

    // Construct the path with layers
    $adminPath = "admin/{$adminNodeKey}/{$layer_one}/{$layer_two}";

    // Retrieve the current admin data
    $adminData = $firebase->retrieve($adminPath);
    $adminData = json_decode($adminData, true);

    if ($adminData === null) {
        $response['status'] = 'error';
        $response['message'] = 'Failed to retrieve existing admin data from Firebase.';
        echo json_encode($response);
        exit;
    }

    // Check if the toggle_sidebar value is set in the POST request
    if (isset($_POST['toggle_sidebar'])) {
        // Convert the checkbox value to a boolean
        $newToggleValue = $_POST['toggle_sidebar'] === 'on';

        // Check if the value has changed
        if ($adminData['toggle_sidebar'] !== $newToggleValue) {
            // Update the toggle_sidebar value in the admin data
            $adminData['toggle_sidebar'] = $newToggleValue;

            // Perform update
            $result = $firebase->update($adminPath, '', $adminData); // Pass an empty string for uniqueID

            if ($result === null) {
                $response['status'] = 'error';
                $response['message'] = 'Failed to update admin data in Firebase.';
                error_log('Firebase error: Failed to update admin data.');
            } else {
                $response['status'] = 'success';
                $response['message'] = 'Admin data updated successfully!';
            }
        } else {
            $response['status'] = 'info';
            $response['message'] = 'No changes were made to the toggle_sidebar value.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'toggle_sidebar value not set.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>