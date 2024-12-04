<?php
session_start();
require_once '../vendor/autoload.php';
include 'includes/firebaseRDB.php';
require_once 'includes/config.php';

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Set headers for JSON response
header('Content-Type: application/json');

// Initialize Firebase
$firebase = new firebaseRDB($databaseURL);

// Retrieve admin data
$adminPath = "admin/{$adminNodeKey}/{$layer_one}/{$layer_two}";
$adminData = $firebase->retrieve($adminPath);
$adminData = json_decode($adminData, true);

function sendInfobipSMS($to, $message) {
    $curl = curl_init();
    $headers = [
        'Authorization: App ' . INFOBIP_API_KEY,
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    $postData = [
        'messages' => [[
            'from' => INFOBIP_SENDER,
            'destinations' => [['to' => $to]],
            'text' => $message
        ]]
    ];
    curl_setopt_array($curl, [
        CURLOPT_URL => INFOBIP_BASE_URL . '/sms/2/text/advanced',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => $headers
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    return $err ? false : true;
}

function generateCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Function to insert log entry with Manila timezone
function insertLog($firebase, $email, $content, $type, $status) {
    // Ensure timestamp is in Manila time
    $timestamp = date('Y-m-d H:i:s');
    $logData = [
        'email' => $email,
        'content' => $content,
        'type' => $type,
        'status' => $status,
        'timestamp' => $timestamp
    ];
    return $firebase->insert("logs", $logData);
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'send_code':
            if (empty($adminData['phone'])) {
                echo json_encode(['success' => false, 'message' => 'Phone number not found']);
                exit;
            }
            // Generate new verification code
            $code = generateCode();
            // Update only the code field in the existing admin data
            $adminData['code'] = $code;
            // Update the existing admin node
            $updateResult = $firebase->update($adminPath, "", $adminData);
            if (!$updateResult) {
                echo json_encode(['success' => false, 'message' => 'Failed to generate verification code']);
                exit;
            }
            // Format phone number (add country code if needed)
            $phoneNumber = $adminData['phone'];
            if (!preg_match('/^\+/', $phoneNumber)) {
                $phoneNumber = '+63' . ltrim($phoneNumber, '0'); // Adding Philippines country code
            }
            // Send SMS with the stored code
            $message = "Your Admin Panel verification code is: {$code}";
            if (sendInfobipSMS($phoneNumber, $message)) {
                // Log successful code request
                insertLog($firebase, $adminData['email'] ?? '', "You request number verification code for 2FA", "number_code_2fa_request", "success");
                echo json_encode(['success' => true]);
            } else {
                // If SMS fails, clear the code
                $adminData['code'] = '';
                $firebase->update($adminPath, "", $adminData);
                // Log failed attempt
                insertLog($firebase, $adminData['email'] ?? '', "Failed to send 2FA verification code", "number_code_2fa_request", "failed");
                echo json_encode(['success' => false, 'message' => 'Failed to send SMS']);
            }
            break;
        case 'verify_code':
            $submittedCode = $_POST['code'] ?? '';
            if (empty($submittedCode)) {
                echo json_encode(['success' => false, 'message' => 'Please enter the verification code']);
                exit;
            }
            // Validate that the code contains only numbers
            if (!ctype_digit($submittedCode)) {
                echo json_encode(['success' => false, 'message' => 'Verification code must contain only numbers.']);
                exit;
            }
            if (empty($adminData['code'])) {
                echo json_encode(['success' => false, 'message' => 'No verification code found. Please request a new code']);
                exit;
            }
            // Verify the submitted code against stored code
            if ($submittedCode === $adminData['code']) {
                // Set session before clearing the code
                $_SESSION['admin_verified'] = true;
                // Store the admin token in session
                $_SESSION['admin_token'] = $adminData['token'];
                // Clear the code after successful verification
                $adminData['code'] = '';
                $firebase->update($adminPath, "", $adminData);
                // Log successful verification with Manila timestamp
                insertLog($firebase, $adminData['phone'] ?? '', "You request number verification code for 2FA", "number_code_2fa_verification", "success");
                echo json_encode([
                    'success' => true,
                    'redirect' => 'index.php?token=' . $adminData['token']
                ]);
            } else {
                // Log failed verification attempt with Manila timestamp
                insertLog($firebase, $adminData['email'] ?? '', "Invalid 2FA code verification attempt", "number_code_2fa_verification", "failed");
                echo json_encode(['success' => false, 'message' => 'Invalid verification code']);
            }
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}