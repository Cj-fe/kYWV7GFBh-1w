<?php
session_start(); // Start the session

header('Content-Type: application/json'); // Set content type to JSON

$response = array(); // Initialize response array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all form fields are set and not empty
    if (
        isset($_POST['course_name']) && !empty($_POST['course_name']) &&
        isset($_POST['courCode']) && !empty($_POST['courCode']) &&
        isset($_POST['department']) && !empty($_POST['department'])
    ) {
        $courseName = $_POST['course_name'];
        $courCode = $_POST['courCode'];
        $department = $_POST['department'];

        // Include Firebase RDB class and initialize
        require_once 'includes/firebaseRDB.php';
        require_once 'includes/config.php'; // Include your config file
        $firebase = new firebaseRDB($databaseURL);

        // Function to check if a Course already exists
        function courseExists($firebase, $courseName, $courCode, $department) {
            $table = 'course'; // Assuming 'course' is your Firebase database node for courses
            $existingCourses = $firebase->retrieve($table);
            $courses = json_decode($existingCourses, true);

            // Debugging output to verify retrieved courses
            error_log('Retrieved Courses: ' . print_r($courses, true));

            foreach ($courses as $course) {
                // Debugging output to verify comparison
                error_log('Checking Course: ' . print_r($course, true));

                if (
                    ($course['course_name'] === $courseName && $course['department'] === $department) ||
                    ($course['courCode'] === $courCode && $course['department'] === $department)
                ) {
                    return true;
                }
            }
            return false;
        }

        // Function to add a Course
        function addCourse($firebase, $courseName, $courCode, $department) {
            $table = 'course';
            $data = array(
                'course_name' => $courseName,
                'courCode' => $courCode,
                'department' => $department
            );
            $result = $firebase->insert($table, $data);
            return $result;
        }

        // Check if the Course already exists
        if (courseExists($firebase, $courseName, $courCode, $department)) {
            $response['status'] = 'error';
            $response['message'] = 'Course name or code already exists in this department.';
        } else {
            // Add Course
            $result = addCourse($firebase, $courseName, $courCode, $department);

            // Check result
            if ($result === null) {
                $response['status'] = 'error';
                $response['message'] = 'Failed to add Course to Firebase.';
                error_log('Firebase error: Failed to insert course data.');
            } else {
                $response['status'] = 'success';
                $response['message'] = 'Course added successfully!';
            }
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'All fields are required.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

// Output the JSON response
echo json_encode($response);
?>
