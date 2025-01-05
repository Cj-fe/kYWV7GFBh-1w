<?php
session_start();
include '../includes/firebaseRDB.php';
include '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the CSRF token
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
        exit();
    }

    // Validate and sanitize input data
    $alumni_id = htmlspecialchars($_POST['alumni_id'] ?? '');
    $firstname = htmlspecialchars($_POST['firstname'] ?? '');
    $middlename = htmlspecialchars($_POST['middlename'] ?? '');
    $lastname = htmlspecialchars($_POST['lastname'] ?? '');
    $auxiliaryname = htmlspecialchars($_POST['auxiliaryname'] ?? '');
    $birthdate = htmlspecialchars($_POST['birthdate'] ?? '');
    $gender = htmlspecialchars($_POST['gender'] ?? '');
    $civilstatus = htmlspecialchars($_POST['civilstatus'] ?? '');
    $city = htmlspecialchars($_POST['city'] ?? '');
    $state = htmlspecialchars($_POST['state'] ?? '');
    $zipcode = htmlspecialchars($_POST['zipcode'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $contactnumber = htmlspecialchars($_POST['contactnumber'] ?? '');
    $course_id = htmlspecialchars($_POST['course'] ?? '');
    $batch_id = htmlspecialchars($_POST['batch'] ?? '');
    $studentid = htmlspecialchars($_POST['studentid'] ?? '');
    $barangay = htmlspecialchars($_POST['barangay'] ?? '');
    $graduation_year = htmlspecialchars($_POST['graduation_year'] ?? '');

    // Handle work status
    $selected_work_status = htmlspecialchars($_POST['work_status'] ?? '');
    $other_work_status = htmlspecialchars($_POST['other_work_status'] ?? '');
    $work_status = ($selected_work_status === 'Other') ? $other_work_status : $selected_work_status;

    // Handle file upload
    $profileImage = $_FILES['profileImage'] ?? null;
    $uploadDir = 'uploads/';
    $profile_url = '';
    
    if ($profileImage && $profileImage['error'] === UPLOAD_ERR_OK) {
        // Generate unique filename
        $fileExtension = pathinfo($profileImage['name'], PATHINFO_EXTENSION);
        $uniqueFilename = uniqid('profile_', true) . '.' . $fileExtension;
        $uploadFile = $uploadDir . $uniqueFilename;

        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($profileImage['tmp_name'], $uploadFile)) {
            $profile_url = $uploadFile;
        }
    }

    // Fetch course name and batch year from Firebase
    $courseData = json_decode($firebase->retrieve("course/{$course_id}"), true);
    $batchData = json_decode($firebase->retrieve("batch_yr/{$batch_id}"), true);
    $course_name = $courseData['course_name'] ?? 'Unknown';
    $batch_year = $batchData['batch_yrs'] ?? 'Unknown';

    // Initialize the update data array for Firebase
    $firebaseUpdateData = [
        'firstname' => $firstname,
        'middlename' => $middlename,
        'lastname' => $lastname,
        'auxiliaryname' => $auxiliaryname,
        'birthdate' => $birthdate,
        'gender' => $gender,
        'civilstatus' => $civilstatus,
        'city' => $city,
        'state' => $state,
        'zipcode' => $zipcode,
        'email' => $email,
        'contactnumber' => $contactnumber,
        'course' => $course_id,
        'batch' => $batch_id,
        'work_status' => $work_status,
        'selected_work_status' => $selected_work_status,
        'other_work_status' => $other_work_status,
        'barangay' => $barangay,
        'profile_url' => $profile_url,
        'forms_completed' => true,
        'date_responded' => date('F j, Y'),
        'studentid' => $alumni_id,
        'graduation_year' => $graduation_year
    ];

    // Add employment-related fields if status is "Employed"
    if ($selected_work_status === 'Employed') {
        $employmentFields = [
            'name_company' => htmlspecialchars($_POST['name_company'] ?? ''),
            'first_employment_date' => htmlspecialchars($_POST['first_employment_date'] ?? ''),
            'date_for_current_employment' => htmlspecialchars($_POST['date_for_current_employment'] ?? ''),
            'type_of_work' => htmlspecialchars($_POST['type_of_work'] ?? ''),
            'work_position' => htmlspecialchars($_POST['work_position'] ?? ''),
            'work_employment_status' => htmlspecialchars($_POST['work_employment_status'] ?? ''),
            'employment_location' => htmlspecialchars($_POST['employment_location'] ?? ''),
            'current_monthly_income' => htmlspecialchars($_POST['current_monthly_income'] ?? ''),
            'job_satisfaction' => htmlspecialchars($_POST['job_satisfaction'] ?? ''),
            'work_related' => htmlspecialchars($_POST['work_related'] ?? ''),
            'work_classification' => htmlspecialchars($_POST['work_classification'] ?? '')
        ];
        
        $firebaseUpdateData = array_merge($firebaseUpdateData, $employmentFields);
    }

    // Initialize the insert data array for MySQL
    $mysqlInsertData = [
        'unique_id' => $alumni_id,
        'id_number' => $alumni_id,
        'fullname' => $firstname . ' ' . ($middlename ? $middlename . ' ' : '') . $lastname,
        'email' => $email,
        'contact' => $contactnumber,
        'sex' => $gender,
        'dob' => $birthdate,
        'year_graduated' => $graduation_year,
        'admission' => $batch_year,
        'program_graduated' => $course_name,
        'is_verified' => 1,
        'password' => $_SESSION['user']['password']
    ];

    try {
        // Update alumni data in Firebase
        $firebase->update('alumni', $alumni_id, $firebaseUpdateData);

        // Insert applicant data into MySQL
        $mysqlConn = getMySQLConnection();
        if (!$mysqlConn) {
            throw new Exception('Failed to connect to MySQL database.');
        }

        $mysqlQuery = "INSERT INTO applicant (" . implode(", ", array_keys($mysqlInsertData)) . ") 
                       VALUES ('" . implode("', '", array_map([$mysqlConn, 'real_escape_string'], array_values($mysqlInsertData))) . "')";
        
        $mysqlResult = $mysqlConn->query($mysqlQuery);
        if (!$mysqlResult) {
            throw new Exception('Failed to insert into MySQL database: ' . $mysqlConn->error);
        }
        $mysqlConn->close();

        // Update the session data
        $_SESSION['user'] = array_merge($_SESSION['user'], $firebaseUpdateData);
        $_SESSION['forms_completed'] = true;

        echo json_encode([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'redirect' => 'index.php'
        ]);
        exit();

    } catch (Exception $e) {
        error_log("Error updating profile: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update profile: ' . $e->getMessage()
        ]);
        exit();
    }
}

// If it's not a POST request, redirect to the form page
header('Location: profile_form.php');
exit();
?>