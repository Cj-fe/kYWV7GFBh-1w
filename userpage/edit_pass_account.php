<?php
require_once '../includes/config.php';
require_once '../includes/session.php';

$firebase = new firebaseRDB($databaseURL);

// Retrieve the user ID from the session
$user_id = $_SESSION['alumni_id'] ?? null;

if (!$user_id) {
    die('User ID is missing');
}

// Prepare the data to be updated
$update_data = [];
$update_messages = [];

// Helper function to sanitize and validate input
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Update email if provided and changed
if (isset($_POST['email']) && $_POST['email'] !== $_SESSION['user']['email']) {
    $new_email = sanitize_input($_POST['email']);
    $update_data['email'] = $new_email;
    $update_messages[] = 'Email updated successfully';
}

// Update password if provided
if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password === $confirm_password) {
        if (strlen($new_password) >= 8) {
            // Hash the password before storing it
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_data['password'] = $hashed_password;
            $update_messages[] = 'Password updated successfully';
        } else {
            $update_messages[] = 'Password must be at least 8 characters long';
        }
    } else {
        $update_messages[] = 'Passwords do not match';
    }
}

// Check if there is any data to update
if (empty($update_data)) {
    $_SESSION['update_message'] = 'No changes were made';
    header('Location: update_password.php');
    exit();
}

// Update the data in Firebase
try {
    $result = $firebase->update("alumni/", $user_id, $update_data);

    if ($result) {
        // Update session data if email was changed
        if (isset($update_data['email'])) {
            $_SESSION['alumni'] = $update_data['email'];
            $_SESSION['user']['email'] = $update_data['email'];
        }
        
        $_SESSION['update_message'] = implode('. ', $update_messages);

        // Update MySQL database
        updateMySQLDatabase($user_id, $update_data);
    } else {
        $_SESSION['update_message'] = 'Failed to update profile';
    }
} catch (Exception $e) {
    $_SESSION['update_message'] = 'Error: ' . $e->getMessage();
}

// Redirect back to the main page
header('Location: update_password.php');
exit();

// Function to update MySQL database
function updateMySQLDatabase($user_id, $update_data) {
    $mysqlConn = getMySQLConnection();

    if (!$mysqlConn) {
        error_log('Failed to connect to MySQL database.');
        return;
    }

    $query = "UPDATE applicant SET ";
    $updateParts = [];

    if (isset($update_data['email'])) {
        $updateParts[] = "email = '" . $mysqlConn->real_escape_string($update_data['email']) . "'";
    }

    if (isset($update_data['password'])) {
        $updateParts[] = "password = '" . $mysqlConn->real_escape_string($update_data['password']) . "'";
    }

    if (empty($updateParts)) {
        $mysqlConn->close();
        return;
    }

    $query .= implode(", ", $updateParts);
    $query .= " WHERE unique_id = '" . $mysqlConn->real_escape_string($user_id) . "'";

    $result = $mysqlConn->query($query);

    if (!$result) {
        error_log('MySQL Update Error: ' . $mysqlConn->error);
    }

    $mysqlConn->close();
}
?>