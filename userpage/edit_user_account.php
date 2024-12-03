<?php
session_start();
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

$firebase = new firebaseRDB($databaseURL);

// Retrieve the user ID from the form
$user_id = $_POST['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['update_message'] = 'User ID is missing';
    header('Location: update_account.php');
    exit();
}

// Prepare the data to be updated
$update_data = [];
$updated_fields = [];

// Helper function to sanitize and validate input
function sanitize_input($data, $is_html = false) {
    if ($is_html) {
        return trim($data);  // Do not encode HTML content
    }
    return htmlspecialchars(trim($data));
}

// Helper function to format date
function format_date($date) {
    // Try to parse the date
    $parsed_date = date_parse_from_format("d/m/Y", $date);
    
    // If parsing fails, try another common format
    if ($parsed_date['error_count'] > 0) {
        $parsed_date = date_parse($date);
    }
    
    // If we successfully parsed the date, format it as YYYY-MM-DD
    if ($parsed_date['error_count'] == 0) {
        return sprintf("%04d-%02d-%02d", $parsed_date['year'], $parsed_date['month'], $parsed_date['day']);
    }
    
    // If all parsing attempts fail, return null
    return null;
}

// Handle file uploads
function handle_file_upload($file_input_name, $target_dir) {
    if (!empty($_FILES[$file_input_name]['name'])) {
        $target_file = $target_dir . basename($_FILES[$file_input_name]['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if the file is an image
        $check = getimagesize($_FILES[$file_input_name]['tmp_name']);
        if($check !== false) {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_file)) {
                return $target_file;
            }
        }
    }
    return null;
}

// Define directories for profile and cover photos
$profile_picture_dir = 'uploads/';
$cover_photo_dir = 'uploads/cover_photos/';

// Ensure directories exist
if (!file_exists($profile_picture_dir)) {
    mkdir($profile_picture_dir, 0777, true);
}
if (!file_exists($cover_photo_dir)) {
    mkdir($cover_photo_dir, 0777, true);
}

// Handle profile picture upload
$profile_image = handle_file_upload('profile_url', $profile_picture_dir);
if ($profile_image) {
    $update_data['profile_url'] = $profile_image;
    $updated_fields[] = 'Profile Picture';
}

// Handle cover photo upload
$cover_photo = handle_file_upload('cover_photo_url', $cover_photo_dir);
if ($cover_photo) {
    $update_data['cover_photo_url'] = $cover_photo;
    $updated_fields[] = 'Cover Photo';
}

// Update other fields from the form
$fields = [
    'firstname', 'middlename', 'lastname', 'gender', 'civilstatus',
    'state', 'city', 'barangay', 'contactnumber', 'reserve_email', 'addressline1',
    'zipcode', 'work_status', 'first_employment_date', 'date_for_current_employment',
    'name_company', 'employment_location', 'type_of_work', 'work_position',
    'current_monthly_income', 'job_satisfaction', 'work_related',
    'major', 'graduation_year', 'work_classification', 'bio'
];

foreach ($fields as $field) {
    if (isset($_POST[$field])) {
        // If the field is 'bio', allow HTML content without encoding it
        $new_value = sanitize_input($_POST[$field], $field === 'bio');
        if (!isset($_SESSION['user'][$field]) || $_SESSION['user'][$field] !== $new_value) {
            $update_data[$field] = $new_value;
            $updated_fields[] = ucfirst(str_replace('_', ' ', $field));
        }
    }
}

// Handle birthdate separately
if (isset($_POST['birthdate'])) {
    $formatted_birthdate = format_date($_POST['birthdate']);
    if ($formatted_birthdate) {
        $update_data['birthdate'] = $formatted_birthdate;
        $updated_fields[] = 'Birthdate';
    } else {
        $_SESSION['update_message'] = 'Invalid birthdate format. Please use DD/MM/YYYY.';
        header('Location: update_account.php');
        exit();
    }
}

// Update the data in Firebase and MySQL
try {
    $mysql_conn = getMySQLConnection();
    if (!$mysql_conn) {
        throw new Exception('Failed to connect to MySQL database.');
    }

    // Prepare MySQL update data
    $mysql_update_data = [];
    
    // Handle name fields separately
    $name_fields = ['firstname', 'middlename', 'lastname'];
    $name_parts = [];
    foreach ($name_fields as $field) {
        if (isset($update_data[$field])) {
            $name_parts[$field] = $update_data[$field];
        } else {
            // If a name part wasn't updated, fetch it from the session
            $name_parts[$field] = $_SESSION['user'][$field] ?? '';
        }
    }
    
    // Construct the full name
    $fullname = trim(implode(' ', $name_parts));
    if (!empty($fullname)) {
        $mysql_update_data['fullname'] = $fullname;
    }

    // Add other fields that need to be updated in MySQL
    $mysql_fields = [
        'contactnumber' => 'contact',
        'addressline1' => 'address',
        'gender' => 'sex',
        'birthdate' => 'dob',
        'graduation_year' => 'year_graduated'
    ];

    foreach ($mysql_fields as $firebase_field => $mysql_field) {
        if (isset($update_data[$firebase_field])) {
            $mysql_update_data[$mysql_field] = $update_data[$firebase_field];
        }
    }

    // Handle course update
    if (isset($_POST['course']) && $_POST['course'] !== $_SESSION['user']['course_id']) {
        $course_id = $_POST['course'];
        $update_data['course'] = $course_id;
        $course_data = $firebase->retrieve("course/$course_id");
        $course_data = json_decode($course_data, true);
        if (isset($course_data['courCode'])) {
            $mysql_update_data['program_graduated'] = $course_data['courCode'];
        }
    }

    // Handle batch update
    if (isset($_POST['batch']) && $_POST['batch'] !== $_SESSION['user']['batch_id']) {
        $batch_id = $_POST['batch'];
        $update_data['batch'] = $batch_id;
        $batch_data = $firebase->retrieve("batch_yr/$batch_id");
        $batch_data = json_decode($batch_data, true);
        if (isset($batch_data['batch_yrs'])) {
            $mysql_update_data['admission'] = $batch_data['batch_yrs'];
        }
    }

    // Update Firebase
    if (!empty($update_data)) {
        $firebase_result = $firebase->update("alumni/", $user_id, $update_data);
    } else {
        $firebase_result = true;
    }

    // Build MySQL query
    if (!empty($mysql_update_data)) {
        $mysql_query = "UPDATE applicant SET ";
        $update_parts = [];
        foreach ($mysql_update_data as $key => $value) {
            $update_parts[] = "$key = '" . $mysql_conn->real_escape_string($value) . "'";
        }
        $mysql_query .= implode(", ", $update_parts);
        $mysql_query .= " WHERE unique_id = '" . $mysql_conn->real_escape_string($user_id) . "'";

        $mysql_result = $mysql_conn->query($mysql_query);

        if (!$mysql_result) {
            throw new Exception('Failed to update MySQL database: ' . $mysql_conn->error);
        }
    } else {
        $mysql_result = true; // No MySQL update needed
    }

    if ($firebase_result && $mysql_result) {
        // Update session data
        foreach ($update_data as $key => $value) {
            $_SESSION['user'][$key] = $value;
        }
        if (isset($course_data['courCode'])) {
            $_SESSION['user']['course'] = $course_data['courCode'];
        }
        if (isset($batch_data['batch_yrs'])) {
            $_SESSION['user']['batch'] = $batch_data['batch_yrs'];
        }
        
        $_SESSION['update_message'] = 'Data Updated Successfully';
    } else {
        throw new Exception('Failed to update profile in one or both databases.');
    }

    $mysql_conn->close();
} catch (Exception $e) {
    $_SESSION['update_message'] = 'Error: ' . $e->getMessage();
}

// Redirect back to the main page
header('Location: update_account.php');
exit();
?>