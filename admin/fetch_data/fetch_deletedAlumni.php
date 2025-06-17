<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

function getFirebaseData($firebase, $path) {
    $data = $firebase->retrieve($path);
    return json_decode($data, true);
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
}

$deletedAlumniData = getFirebaseData($firebase, "deleted_alumni");
$batchData = getFirebaseData($firebase, "batch_yr");
$courseData = getFirebaseData($firebase, "course");

$filterCourse = isset($_GET['course']) ? sanitizeInput($_GET['course']) : '';
$filterBatch = isset($_GET['batch']) ? sanitizeInput($_GET['batch']) : '';

// Check if deletedAlumniData is an array before looping through it
if (is_array($deletedAlumniData) && count($deletedAlumniData) > 0) {
    foreach ($deletedAlumniData as $id => $alumni) {
        $courseId = $alumni['course'];
        $batchId = $alumni['batch'];

        if ($filterCourse && $filterCourse != $courseId) {
            continue;
        }
        if ($filterBatch && $filterBatch != $batchId) {
            continue;
        }

        $batchName = isset($batchData[$batchId]['batch_yrs']) ? sanitizeInput($batchData[$batchId]['batch_yrs']) : 'Unknown Batch';
        $courseName = isset($courseData[$courseId]['courCode']) ? sanitizeInput($courseData[$courseId]['courCode']) : 'Unknown Course';

        echo "<tr>
            <td style='display:none;'><input type='checkbox' class='modal-checkbox' data-id='" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "'></td>
            <td>" . sanitizeInput($alumni['studentid']) . "</td>
            <td>" . sanitizeInput($alumni['firstname']) . "</td>
            <td>" . sanitizeInput($alumni['middlename']) . "</td>
            <td>" . sanitizeInput($alumni['lastname']) . "</td>
            <td>" . sanitizeInput($alumni['email']) . "</td>
            <td>" . sanitizeInput($alumni['gender']) . "</td>
            <td>" . $courseName . "</td>
            <td>" . $batchName . "</td>
            <td>
                <a class='btn btn-info btn-sm btn-flat open-retrieve' data-id='" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "'>RESTORE</a>
                <a class='btn btn-danger btn-sm btn-flat open-delete' data-id='" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "'>DELETE</a>
            </td>
        </tr>";
    }
}
?>