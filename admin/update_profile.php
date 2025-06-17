<?php
include 'includes/session.php';
$return = isset($_GET['return']) ? $_GET['return'] : 'home.php';

// Enhanced logging function
function logUpdateAction($firebase, $updatedFields) {
    $logDetails = [];
    // Map specific fields to more readable log messages
    $fieldMapping = [
        'username' => 'Updated Username',
        'firstname' => 'Updated First Name',
        'lastname' => 'Updated Last Name',
        'image_url' => 'Updated Profile Photo',
        'password' => 'Updated Password',
        'lockscreen' => 'Updated Lockscreen Setting',
        'mfa' => 'Updated Multi-Factor Authentication'
    ];
    foreach ($updatedFields as $field) {
        if (isset($fieldMapping[$field])) {
            $logDetails[] = $fieldMapping[$field];
        }
    }
    $logData = [
        'timestamp' => (new DateTime('now', new DateTimeZone('Asia/Singapore')))->format('Y-m-d H:i:s'),
        'content' => implode(', ', $logDetails),
        'type' => 'admin_profile_update'
    ];
    // Log to Firebase logs node
    $table = 'logs';
    $result = $firebase->insert($table, $logData);
    return $result;
}

// Set JSON header
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required_fields = ['curr_password', 'username', 'firstname', 'lastname'];
        $valid = true;
        $missing_fields = [];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                $valid = false;
                $missing_fields[] = $field;
            }
        }
        if ($valid) {
            // Sanitize input data
            $curr_password = htmlspecialchars($_POST['curr_password']);
            $username = htmlspecialchars($_POST['username']);
            $firstname = htmlspecialchars($_POST['firstname']);
            $lastname = htmlspecialchars($_POST['lastname']);
            $photo = isset($_FILES['photo']['name']) ? $_FILES['photo']['name'] : '';

            // Get authentication method statuses
            $lockscreen = isset($_POST['lockscreen']) ? true : false;
            $mfa = isset($_POST['mfa']) ? true : false;

            if (password_verify($curr_password, $user['password'])) {
                $filename = $user['image_url']; // Default to current image
                if (!empty($photo)) {
                    $upload_dir = 'uploads/';
                    $target_file = $upload_dir . basename($photo);
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                        $filename = 'uploads/' . basename($photo);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to upload photo']);
                        exit();
                    }
                }

                // Track which fields are being updated
                $updatedFields = [];
                $hashed_password = empty($_POST['password']) ? $user['password'] : password_hash($_POST['password'], PASSWORD_DEFAULT);

                // Prepare updated data for Firebase
                $updatedData = [
                    'user' => $username,
                    'password' => $hashed_password,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'image_url' => $filename,
                    'lockscreen' => $lockscreen,
                    'mfa' => $mfa
                ];

                // Check which fields have changed
                if ($username !== $user['user']) $updatedFields[] = 'username';
                if ($firstname !== $user['firstname']) $updatedFields[] = 'firstname';
                if ($lastname !== $user['lastname']) $updatedFields[] = 'lastname';
                if ($filename !== $user['image_url']) $updatedFields[] = 'image_url';
                if ($hashed_password !== $user['password']) $updatedFields[] = 'password';
                if ($lockscreen !== $user['lockscreen']) $updatedFields[] = 'lockscreen';
                if ($mfa !== $user['mfa']) $updatedFields[] = 'mfa';

                // Include FirebaseRDB class and initialize
                require_once 'includes/firebaseRDB.php';
                require_once 'includes/config.php';
                $firebase = new firebaseRDB($databaseURL);

                // Concatenate adminNodeKey with layer_one and layer_two
                $fullNodePath = "admin/{$adminNodeKey}/{$layer_one}/{$layer_two}";

                // Update Firebase using the concatenated path
                $updateResult = $firebase->update($fullNodePath, "", $updatedData);
                $updateResult = json_decode($updateResult, true);

                if ($updateResult !== null && $updateResult !== false) {
                    // Log the update action
                    logUpdateAction($firebase, $updatedFields);

                    // Update session data
                    if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
                        $_SESSION['user'] = [];
                    }
                    $_SESSION['user'] = array_merge($_SESSION['user'], $updatedData);

                    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error updating profile in Firebase']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Incorrect current password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}