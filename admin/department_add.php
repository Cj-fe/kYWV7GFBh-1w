<?php
session_start(); // Start the session
header('Content-Type: application/json'); // Set content type to JSON

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure departmentName is set and not empty
    if (isset($_POST['departmentName']) && !empty($_POST['departmentName'])) {
        $departmentName = $_POST['departmentName'];

        // Process the data further (e.g., save to database, interact with Firebase, etc.)
        // Example: Connect to Firebase and add the department
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php'; // Include your config file
        $firebase = new firebaseRDB($databaseURL);

        // Function to check if department exists
        function departmentExists($firebase, $departmentName) {
            $table = 'departments';
            $departments = $firebase->retrieve($table);
            $departments = json_decode($departments, true);

            foreach ($departments as $department) {
                if (strcasecmp($department['Department Name'], $departmentName) == 0) {
                    return true;
                }
            }
            return false;
        }

        // Function to add a department
        function addDepartment($firebase, $departmentName) {
            $table = 'departments';
            $data = array('Department Name' => $departmentName);
            $result = $firebase->insert($table, $data);
            return $result;
        }

        // Check if department already exists
        if (departmentExists($firebase, $departmentName)) {
            $response['status'] = 'error';
            $response['message'] = 'Department already exists.';
        } else {
            // Add department
            $result = addDepartment($firebase, $departmentName);

            // Check result
            if ($result === 'null') {
                $response['status'] = 'error';
                $response['message'] = 'Failed to add department.';
            } else {
                $response['status'] = 'success';
                $response['message'] = 'Department added successfully!';
            }
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Department name is required.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>
