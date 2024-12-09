<?php
session_start();
header('Content-Type: application/json');
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['studentid']) && !empty($_POST['studentid'])) {
        $studentid = $_POST['studentid'];
        // Validate the student ID format
        if (!preg_match('/^\d{4}-\d{4}$/', $studentid)) {
            $response['status'] = 'error';
            $response['message'] = 'Alumni ID must be in the format 1234-5678';
            echo json_encode($response);
            exit;
        }
        // Validate the year in the student ID
        $currentYear = date('Y');
        $idYear = substr($studentid, 0, 4);
        if ($currentYear - $idYear < 4) {
            $response['status'] = 'error';
            $response['message'] = 'Alumni ID year must be at least 4 years ago';
            echo json_encode($response);
            exit;
        }

        // Assign form data to variables
        $firstname = $_POST['firstname'] ?? '';
        $lastname = $_POST['lastname'] ?? '';
        $middlename = $_POST['middlename'] ?? '';
        $auxiliaryname = $_POST['auxiliaryname'] ?? '';
        $birthdate = $_POST['birthdate'] ?? '';
        $civilstatus = $_POST['civilstatus'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $addressline1 = $_POST['addressline1'] ?? '';
        $city = $_POST['city'] ?? '';
        $state = $_POST['state'] ?? '';
        $zipcode = $_POST['zipcode'] ?? '';
        $contactnumber = $_POST['contactnumber'] ?? '';
        $email = $_POST['email'] ?? '';
        $course = $_POST['course'] ?? '';
        $batch = $_POST['batch'] ?? '';

        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php';
        $firebase = new firebaseRDB($databaseURL);

        // Check if student ID or email already exists in Firebase
        $table = 'alumni';
        $result = $firebase->retrieve($table);
        $result = json_decode($result, true);
       
        $isStudentIdExists = false;
        $isEmailExists = false;
       
        if ($result) {
            foreach ($result as $key => $record) {
                if (isset($record['studentid']) && $record['studentid'] === $studentid) {
                    $isStudentIdExists = true;
                }
                if (isset($record['email']) && $record['email'] === $email) {
                    $isEmailExists = true;
                }
            }
        }

        if ($isStudentIdExists) {
            $response['status'] = 'error';
            $response['message'] = 'Alumni ID already exists. It cannot be reused.';
            echo json_encode($response);
            exit;
        }

        if ($isEmailExists) {
            $response['status'] = 'error';
            $response['message'] = 'Email already exists. Please use a different email.';
            echo json_encode($response);
            exit;
        }

        // Prepare alumni data for Firebase
        $alumniData = array(
            'firstname' => $firstname,
            'lastname' => $lastname,
            'middlename' => $middlename,
            'auxiliaryname' => $auxiliaryname,
            'birthdate' => $birthdate,
            'civilstatus' => $civilstatus,
            'gender' => $gender,
            'addressline1' => $addressline1,
            'city' => $city,
            'state' => $state,
            'zipcode' => $zipcode,
            'contactnumber' => $contactnumber,
            'email' => $email,
            'course' => $course,
            'batch' => $batch,
            'studentid' => $studentid,
            'forms_completed' => false
        );

        // Add alumni data to Firebase
        $firebaseResult = $firebase->insert($table, $alumniData);

        // Get the Firebase-generated ID
        $firebaseId = null;
        if ($firebaseResult !== null) {
            $insertedData = $firebase->retrieve($table);
            $insertedData = json_decode($insertedData, true);
            if (is_array($insertedData)) {
                $lastInserted = end($insertedData);
                if ($lastInserted !== false) {
                    $firebaseId = key($insertedData);
                }
            }
        }

        if ($firebaseId === null) {
            $response['status'] = 'error';
            $response['message'] = 'Failed to get Firebase-generated ID.';
            echo json_encode($response);
            exit;
        }

        // MySQL connection
        $mysqlConn = getMySQLConnection();
        if (!$mysqlConn) {
            $response['status'] = 'error';
            $response['message'] = 'Failed to connect to MySQL database.';
            echo json_encode($response);
            exit;
        }

        // Prepare alumni data for MySQL
        $mysqlData = array(
            'id' => $firebaseId,
            'firstname' => $firstname,
            'forms_completed' => 0,
            'lastname' => $lastname,
            'middlename' => $middlename,
            'studentid' => $studentid,
        );

        // Prepare MySQL query
        $mysqlQuery = "INSERT INTO alumni_verified (" . implode(", ", array_keys($mysqlData)) . ") VALUES ('" . implode("', '", array_map(array($mysqlConn, 'real_escape_string'), $mysqlData)) . "')";

        // Execute MySQL query
        $mysqlResult = $mysqlConn->query($mysqlQuery);

        // Close MySQL connection
        $mysqlConn->close();

        // Check results
        if ($firebaseResult === null || !$mysqlResult) {
            $response['status'] = 'error';
            $response['message'] = 'Failed to add alumni data to one or both databases.';
            error_log('Firebase error: ' . ($firebaseResult === null ? 'Failed to insert' : 'Inserted successfully'));
            error_log('MySQL error: ' . ($mysqlResult ? 'Inserted successfully' : $mysqlConn->error));
        } else {
            $response['status'] = 'success';
            $response['message'] = 'Alumni data added successfully to both databases!';
        }

        echo json_encode($response);
        exit;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Alumni ID is required.';
        echo json_encode($response);
        exit;
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
    exit;
}
?>