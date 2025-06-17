<?php
session_start();

require '../vendor/autoload.php';
require_once 'includes/firebaseRDB.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

require_once 'includes/config.php'; // Include your config file
$firebase = new firebaseRDB($databaseURL);

header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'An unexpected error occurred.');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['import_file'])) {
    $file_mimes = array(
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv'
    );

    if (isset($_FILES['import_file']['name']) && in_array($_FILES['import_file']['type'], $file_mimes)) {
        $file_extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

        try {
            $spreadsheet = IOFactory::load($_FILES['import_file']['tmp_name']);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $existingData = json_decode($firebase->retrieve('alumni'), true);
            $existingEntries = array();
            $existingStudentIds = array();

            if ($existingData) {
                foreach ($existingData as $record) {
                    if (isset($record['firstname'], $record['lastname'], $record['studentid'])) {
                        $existingEntries[] = $record['firstname'] . $record['lastname'] . $record['studentid'];
                        $existingStudentIds[] = $record['studentid'];
                    }
                }
            }

            $importedEntries = array();
            $errors = array();

            foreach ($sheetData as $index => $data) {
                if ($index == 1) continue; // Skip the header row

                $firstname = $data['A'];
                $lastname = $data['B'];
                $middlename = $data['C'];
                $auxiliaryname = $data['D'];
                $birthdate = $data['E'];
                $civilstatus = $data['F'];
                $gender = $data['G'];
                $addressline1 = $data['H'];
                $city = $data['I'];
                $state = $data['J'];
                $zipcode = $data['K'];
                $contactnumber = $data['L'];
                $email = $data['M'];
                $courseName = $data['N'];
                $batchYear = $data['O'];
                $studentid = $data['P'];

                $entryKey = $firstname . $lastname . $studentid;

                // Check for duplicate in the import file
                if (in_array($entryKey, $importedEntries)) {
                    $errors[] = "Reminder Duplicate data in import file: $firstname $lastname student-id $studentid";
                    continue; // Skip this entry if it's a duplicate in the file
                }

                // Check for duplicate in the database
                if (in_array($entryKey, $existingEntries)) {
                    $errors[] = 'Alumni data already exists for ' . $firstname . ' ' . $lastname . ' (' . $studentid . ')';
                    continue; // Skip this entry if it's a duplicate in the database
                }

                // Check for duplicate student ID in the database
                if (in_array($studentid, $existingStudentIds)) {
                    $errors[] = 'Student ID ' . $studentid . ' already exists in the database.';
                    continue; // Skip this entry if the student ID is a duplicate in the database
                }

                // Fetch batch ID
                $batchId = getBatchId($firebase, $batchYear);
                if ($batchId === null) {
                    $errors[] = "Batch year $batchYear not found.";
                    continue; // Skip this entry if batch year not found
                }

                // Fetch course ID
                $courseId = getCourseId($firebase, $courseName);
                if ($courseId === null) {
                    $errors[] = "Course $courseName not found.";
                    continue; // Skip this entry if course not found
                }

                // Add alumni data
                addAlumniData($firebase, $firstname, $lastname, $middlename, $auxiliaryname, $birthdate, $civilstatus, $gender, $addressline1, $city, $state, $zipcode, $contactnumber, $email, $courseId, $batchId, $studentid);

                // Record this entry as imported
                $importedEntries[] = $entryKey;
            }

            if (!empty($errors)) {
                $response['message'] = implode('<br>', $errors);
            } else {
                $response['status'] = 'success';
                $response['message'] = 'Data imported successfully!';
            }
        } catch (Exception $e) {
            $response['message'] = 'Error loading file: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid file type. Please upload an Excel or CSV file.';
    }
} else {
    $response['message'] = 'No file uploaded.';
}

echo json_encode($response);

function getBatchId($firebase, $batchYear) {
    $table = 'batch_yr';
    $result = json_decode($firebase->retrieve($table), true);
    if ($result) {
        foreach ($result as $id => $record) {
            if (isset($record['batch_yrs']) && $record['batch_yrs'] == $batchYear) {
                return $id;
            }
        }
    }
    return null;
}

function getCourseId($firebase, $courseName) {
    $table = 'course';
    $result = json_decode($firebase->retrieve($table), true);
    if ($result) {
        foreach ($result as $id => $record) {
            if (isset($record['courCode']) && $record['courCode'] == $courseName) {
                return $id;
            }
        }
    }
    return null;
}

function addAlumniData($firebase, $firstname, $lastname, $middlename, $auxiliaryname, $birthdate, $civilstatus, $gender, $addressline1, $city, $state, $zipcode, $contactnumber, $email, $courseId, $batchId, $studentid) {
    $table = 'alumni';
    $data = array(
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
        'course' => $courseId,
        'batch' => $batchId,
        'studentid' => $studentid
    );
    $firebase->insert($table, $data);
}
?>

