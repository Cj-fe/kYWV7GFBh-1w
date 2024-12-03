<?php
session_start(); // Start the session

header('Content-Type: application/json'); // Set the content type to JSON

// Function to send a JSON response
function sendResponse($status, $message, $debug = null) {
    $response = ['status' => $status, 'message' => $message];
    if ($debug !== null) {
        $response['debug'] = $debug;
    }
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure all necessary data is provided and not empty
    $required_fields = ['id', 'edit_title', 'edit_date', 'edit_venue', 'edit_author', 'edit_description'];

    $valid = true;
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $valid = false;
            $missing_fields[] = $field;
        }
    }

    if ($valid) {
        // Include FirebaseRDB class and initialize
        require_once 'includes/firebaseRDB.php';
        require_once '../richTextEditor/autoload.php'; // Autoload HTMLPurifier if using Composer
        require_once 'includes/config.php'; // Include your config file
        $firebase = new firebaseRDB($databaseURL);

        // Configure HTMLPurifier
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        // Extract ID and data to update
        $id = htmlspecialchars($_POST['id']);
        $updateData = [
            "event_title" => htmlspecialchars($_POST['edit_title']),
            "event_date" => htmlspecialchars($_POST['edit_date']),
            "event_venue" => htmlspecialchars($_POST['edit_venue']),
            "event_author" => htmlspecialchars($_POST['edit_author']),
            "event_description" => $purifier->purify($_POST['edit_description']),
        ];

        // Process invited courses and batches
        $updateData['event_invited'] = isset($_POST['edit_event_invited']) && !empty($_POST['edit_event_invited']) 
            ? json_encode(array_values($_POST['edit_event_invited'])) 
            : json_encode([]);

        $updateData['course_invited'] = isset($_POST['edit_course_invited']) && !empty($_POST['edit_course_invited']) 
            ? json_encode(array_values($_POST['edit_course_invited'])) 
            : json_encode([]);

        // Fetch current event data
        $currentEvent = json_decode($firebase->retrieve("event", $id), true);

        if ($currentEvent === null) {
            sendResponse('error', 'Failed to retrieve current event data.', ['id' => $id]);
        }

        // Check if any changes were made
        $changesDetected = false;
        $changes = [];
        foreach ($updateData as $key => $value) {
            if ($key === 'event_invited' || $key === 'course_invited') {
                $currentValue = isset($currentEvent[$key]) ? json_decode($currentEvent[$key], true) : [];
                $newValue = json_decode($value, true);
                if ($currentValue != $newValue) {
                    $changesDetected = true;
                    $changes[$key] = ['old' => $currentValue, 'new' => $newValue];
                }
            } elseif (!isset($currentEvent[$key]) || $currentEvent[$key] !== $value) {
                $changesDetected = true;
                $changes[$key] = ['old' => $currentEvent[$key] ?? null, 'new' => $value];
            }
        }

        // Handle image upload if file is provided
        if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK) {
            $changesDetected = true;
            $upload_dir = 'uploads/'; // Directory where images will be uploaded
            $filename = $_FILES['imageUpload']['name'];
            $tmp_name = $_FILES['imageUpload']['tmp_name'];
            $target_path = $upload_dir . basename($filename);

            // Move uploaded file to specified directory
            if (move_uploaded_file($tmp_name, $target_path)) {
                $updateData['image_url'] = $target_path; // Store image URL in update data
                $changes['image_url'] = ['old' => $currentEvent['image_url'] ?? null, 'new' => $target_path];
            } else {
                sendResponse('error', 'Failed to upload image.', ['upload_error' => error_get_last()]);
            }
        }

        if (!$changesDetected) {
            sendResponse('info', 'No changes were detected. Update aborted.', ['current' => $currentEvent, 'update' => $updateData]);
        }

        // Function to update event data
        function updateEventData($firebase, $id, $updateData) {
            $table = 'event'; // Assuming 'event' is your Firebase database node for event data
            return $firebase->update($table, $id, $updateData);
        }

        // Perform update
        $result = json_decode(updateEventData($firebase, $id, $updateData), true);

        // Check result
        if ($result === null) {
            sendResponse('error', 'Failed to update event data in Firebase.', ['update_data' => $updateData]);
        } else {
            sendResponse('success', 'Event data updated successfully!', ['changes' => $changes]);
        }
    } else {
        sendResponse('error', 'All fields are required: ' . implode(', ', $missing_fields));
    }
} else {
    sendResponse('error', 'Invalid request method.');
}
?>