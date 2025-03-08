<?php
session_start();

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'An unexpected error occurred.');

// New function to get readable batch and course
function getReadableBatchAndCourse($firebase, $batchId, $courseId) {
    $batchData = $firebase->retrieve("batch_yr/$batchId");
    $courseData = $firebase->retrieve("course/$courseId");
    
    $batchData = json_decode($batchData, true);
    $courseData = json_decode($courseData, true);
    
    $readableBatch = $batchData['batch_yrs'] ?? '';
    $readableCourse = $courseData['courCode'] ?? '';
    
    return array('batch' => $readableBatch, 'course' => $readableCourse);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['edit_studentid']) || empty($_POST['edit_studentid'])) {
        $response['message'] = 'Alumni ID is required.';
        echo json_encode($response);
        exit;
    }

    $studentid = $_POST['edit_studentid'];
    
    // Check if the student ID is in the standard format or the new format
    if (preg_match('/^\d{4}-\d{4}$/', $studentid)) {
        // Standard format (e.g., 2021-2020)
        $currentYear = date('Y');
        $idYear = substr($studentid, 0, 4);
        if ($currentYear - $idYear < 4) {
            $response['message'] = 'Alumni ID year must be at least 4 years ago';
            echo json_encode($response);
            exit;
        }
    } elseif (!preg_match('/^-[A-Za-z0-9_]{19}$/', $studentid)) {
        // If it's not in the standard format and not in the new format
        $response['message'] = 'Invalid Alumni ID format';
        echo json_encode($response);
        exit;
    }
    // If it's in the new format (-O4VZlnaiVkvz3rSp_Fx), we allow it to proceed without additional checks

    require_once 'includes/firebaseRDB.php';
    require_once 'includes/config.php';
    $firebase = new firebaseRDB($databaseURL);

    $id = $_POST['id'];
    $updateData = [
        "firstname" => $_POST['edit_firstname'] ?? '',
        "lastname" => $_POST['edit_lastname'] ?? '',
        "middlename" => $_POST['edit_middlename'] ?? '',
        "auxiliaryname" => $_POST['edit_auxiliaryname'] ?? '',
        "birthdate" => $_POST['edit_birthdate'] ?? '',
        "civilstatus" => $_POST['edit_civilstatus'] ?? '',
        "gender" => $_POST['edit_gender'] ?? '',
        "addressline1" => $_POST['edit_addressline1'] ?? '',
        "city" => $_POST['edit_city'] ?? '',
        "state" => $_POST['edit_state'] ?? '',
        "zipcode" => $_POST['edit_zipcode'] ?? '',
        "contactnumber" => $_POST['edit_contactnumber'] ?? '',
        "email" => $_POST['edit_email'] ?? '',
        "course" => $_POST['edit_course'] ?? '',
        "batch" => $_POST['edit_batch'] ?? '',
        "studentid" => $studentid
    ];

    // Get existing data
    $existingData = $firebase->retrieve('alumni/' . $id);
    $existingData = json_decode($existingData, true);

    if (!$existingData) {
        $response['message'] = 'Alumni record not found.';
        echo json_encode($response);
        exit;
    }

    // Check if the Alumni ID or email is changing and if they already exist
    $allAlumni = $firebase->retrieve('alumni');
    $allAlumni = json_decode($allAlumni, true);

    foreach ($allAlumni as $key => $alumni) {
        if ($key !== $id) {
            if ($alumni['studentid'] === $updateData['studentid'] && $updateData['studentid'] !== $existingData['studentid']) {
                $response['message'] = 'Cannot update. The new Alumni ID already exists.';
                echo json_encode($response);
                exit;
            }
            if ($alumni['email'] === $updateData['email'] && $updateData['email'] !== $existingData['email']) {
                $response['message'] = 'Cannot update. The new email already exists.';
                echo json_encode($response);
                exit;
            }
        }
    }

    // Check if there are actual changes
    $isChanged = false;
    foreach ($updateData as $key => $value) {
        if (!isset($existingData[$key]) || $existingData[$key] !== $value) {
            $isChanged = true;
            break;
        }
    }

    if (!$isChanged) {
        $response['status'] = 'info';
        $response['message'] = 'You have not made any changes';
    } else {
        // Perform update in Firebase
        $firebaseResult = $firebase->update('alumni', $id, $updateData);

        // Get readable batch and course
        $readableData = getReadableBatchAndCourse($firebase, $updateData['batch'], $updateData['course']);

        // Perform update in MySQL
        $mysqlConn = getMySQLConnection();

        if (!$mysqlConn) {
            $response['message'] = 'Failed to connect to MySQL database.';
            echo json_encode($response);
            exit;
        }

        $mysqlUpdateData = [
            'id_number' => $updateData['studentid'],
            'fullname' => $updateData['firstname'] . ' ' .  $updateData['middlename'] . ' ' .  $updateData['lastname'] ,
            'contact' => $updateData['contactnumber'],
            'address' => $updateData['addressline1'],
            'dob' => $updateData['birthdate'],
            'program_graduated' => $readableData['course'],
            'admission' => $readableData['batch'],
        ];

        $mysqlQuery = "UPDATE applicant SET ";
        $updateParts = [];
        foreach ($mysqlUpdateData as $key => $value) {
            $updateParts[] = "$key = '" . $mysqlConn->real_escape_string($value) . "'";
        }
        $mysqlQuery .= implode(", ", $updateParts);
        $mysqlQuery .= " WHERE unique_id = '" . $mysqlConn->real_escape_string($id) . "'";

        $mysqlResult = $mysqlConn->query($mysqlQuery);

        $mysqlConn->close();

        if ($firebaseResult === null || !$mysqlResult) {
            $response['message'] = 'Failed to update alumni data in one or both databases.';
            error_log('Firebase error: ' . ($firebaseResult === null ? 'Failed to update' : 'Updated successfully'));
            error_log('MySQL error: ' . ($mysqlResult ? 'Updated successfully' : $mysqlConn->error));
        } else {
            $response['status'] = 'success';
            $response['message'] = 'Alumni data updated successfully in both databases!';
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>