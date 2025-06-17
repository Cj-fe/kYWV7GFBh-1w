<?php
session_start();
header('Content-Type: application/json');
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php';

// Set timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

$firebase = new firebaseRDB($databaseURL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $submitted_code = $_POST['code'];

    // Validate that the code contains only numbers
    if (!ctype_digit($submitted_code)) {
        echo json_encode([
            'success' => false,
            'message' => 'Verification code must contain only numbers.'
        ]);
        exit;
    }

    if (!isset($_SESSION['lockscreen_email'])) {
        $log_data = [
            'type' => 'gmail_code_2fa_request',
            'content' => 'Session expired during verification attempt',
            'timestamp' => date('Y-m-d H:i:s'), // Now using Asia/Manila timezone
            'status' => 'failed',
            'email' => 'unknown'
        ];
        $firebase->insert("logs", $log_data);
        echo json_encode(['success' => false, 'message' => 'Session expired. Please try again.']);
        exit;
    }

    try {
        // Construct the path with layers
        $adminPath = "admin/{$adminNodeKey}/{$layer_one}/{$layer_two}";

        // Retrieve specific admin node data
        $adminData = $firebase->retrieve($adminPath);
        $adminData = json_decode($adminData, true);

        if ($adminData && isset($adminData['code']) && $submitted_code === $adminData['code']) {
            $code_timestamp = $adminData['code_timestamp'] ?? 0;
            $current_time = time(); // Get current timestamp in Asia/Manila timezone

            if ($current_time - $code_timestamp > 900) {
                $log_data = [
                    'type' => 'gmail_code_2fa_request',
                    'content' => 'Verification code expired',
                    'timestamp' => date('Y-m-d H:i:s'), // Using Asia/Manila timezone
                    'status' => 'failed',
                    'email' => $_SESSION['lockscreen_email']
                ];
                $firebase->insert("logs", $log_data);
                echo json_encode(['success' => false, 'message' => 'Verification code has expired. Please request a new one.']);
                exit;
            }

            // Format expiration date 24 hours from now in Asia/Manila time
            $token_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

            // Generate new tokens
            $new_token = hash('sha256', bin2hex(random_bytes(16)));
            $new_token2 = hash('sha256', bin2hex(random_bytes(16)));

            $update_data = [
                'code' => '',
                'code_timestamp' => null,
                'reset_token_expires_at' => $token_expires, // Using Asia/Manila timezone
                'token' => $new_token,
                'token2' => $new_token2
            ];

            // Update specific admin node
            $firebase->update($adminPath, "", $update_data);

            $log_data = [
                'type' => 'gmail_code_2fa_request',
                'content' => 'You request verification code for 2FA',
                'timestamp' => date('Y-m-d H:i:s'), // Using Asia/Manila timezone
                'status' => 'success',
                'email' => $_SESSION['lockscreen_email']
            ];
            $firebase->insert("logs", $log_data);

            unset($_SESSION['lockscreen_email']);
            $_SESSION['admin_token'] = $new_token;

            if (isset($adminData['mfa']) && $adminData['mfa'] === true) {
                echo json_encode([
                    'success' => true,
                    'message' => 'First verification successful! Proceeding to second factor.',
                    'redirect' => 'lock2.php?token=' . $new_token2,
                    'expires_at' => $token_expires // Using Asia/Manila timezone
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'message' => 'Verification successful!',
                    'redirect' => 'index.php?token=' . $new_token,
                    'expires_at' => $token_expires // Using Asia/Manila timezone
                ]);
            }
        } else {
            $log_data = [
                'type' => 'gmail_code_2fa_request',
                'content' => 'Invalid verification code attempt',
                'timestamp' => date('Y-m-d H:i:s'), // Using Asia/Manila timezone
                'status' => 'failed',
                'email' => $_SESSION['lockscreen_email']
            ];
            $firebase->insert("logs", $log_data);
            echo json_encode(['success' => false, 'message' => 'Invalid verification code. Please try again.']);
        }
    } catch (Exception $e) {
        $log_data = [
            'type' => 'gmail_code_2fa_request',
            'content' => 'Error during verification: ' . $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s'), // Using Asia/Manila timezone
            'status' => 'error',
            'email' => $_SESSION['lockscreen_email'] ?? 'unknown'
        ];
        $firebase->insert("logs", $log_data);
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    $log_data = [
        'type' => 'gmail_code_2fa_request',
        'content' => 'Invalid request attempt - missing code or wrong method',
        'timestamp' => date('Y-m-d H:i:s'), // Using Asia/Manila timezone
        'status' => 'failed',
        'email' => 'unknown'
    ];
    $firebase->insert("logs", $log_data);
    echo json_encode(['success' => false, 'message' => 'Invalid request method or missing code']);
}