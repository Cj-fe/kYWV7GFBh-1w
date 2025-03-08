<?php
session_start(); // Start the session
$response = array('status' => 'error', 'message' => ''); // Initialize response array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure Batch is set and not empty
    if (isset($_POST['bacthName']) && !empty($_POST['bacthName'])) {
        $batchName = $_POST['bacthName'];
        
        // Check if the input year is at least 4 years behind the current year
        $currentYear = date('Y');
        if ($batchName > $currentYear - 4) {
            $response['message'] = 'Batch year must be at least 4 years behind the current year.';
        } else {
            // Include Firebase RDB class and initialize
            require_once 'includes/firebaseRDB.php';
            require_once 'includes/config.php'; // Include your config file
            $firebase = new firebaseRDB($databaseURL);

            // Function to check if Batch already exists
            function batchExists($firebase, $batchName) {
                $table = 'batch_yr';
                $existingBatches = $firebase->retrieve($table);
                $batches = json_decode($existingBatches, true);
                foreach ($batches as $batch) {
                    if ($batch['batch_yrs'] === $batchName) {
                        return true;
                    }
                }
                return false;
            }

            // Function to add a Batch
            function addBatch($firebase, $batchName) {
                $table = 'batch_yr';
                $data = array('batch_yrs' => $batchName);
                $result = $firebase->insert($table, $data);
                return $result;
            }

            // Check if the Batch already exists
            if (batchExists($firebase, $batchName)) {
                $response['message'] = 'Batch year already exists.';
            } else {
                // Add Batch
                $result = addBatch($firebase, $batchName);
                // Check result
                if ($result === null) {
                    $response['message'] = 'Failed to add Batch.';
                } else {
                    $response['status'] = 'success';
                    $response['message'] = 'Batch added successfully!';
                }
            }
        }
    } else {
        $response['message'] = 'Batch name is required.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>