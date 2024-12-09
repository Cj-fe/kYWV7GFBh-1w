<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php';

// Set default timezone to Asia/Manila
date_default_timezone_set('Asia/Manila');

// Initialize Firebase
$firebase = new firebaseRDB($databaseURL);
$email = "";
$errors = array();

function debug_log($message) {
    error_log(print_r($message, true));
}

if (isset($_POST['check-email'])) {
    debug_log("Form submitted");
    $email = $_POST['email'];
    debug_log("Email submitted: " . $email);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format!";
    } else {
        // Construct the path with layers
        $adminPath = "admin/{$adminNodeKey}/{$layer_one}/{$layer_two}";

        // Fetch user data from Firebase
        $data = $firebase->retrieve($adminPath);
        debug_log("Data retrieved from Firebase:");
        debug_log($data);
        $admin = json_decode($data, true);

        if ($admin && isset($admin['email']) && $admin['email'] === $email) {
            $token = bin2hex(random_bytes(16));
            $token_hash = hash("sha256", $token);
            $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30 minutes expiry

            // Update the user's reset token and expiration in Firebase
            $admin["reset_token_hash"] = $token_hash;
            $admin["reset_token_expires_at"] = $expiry;
            $updateResult = $firebase->update($adminPath, null, $admin);
            debug_log("Update result:");
            debug_log($updateResult);

            // Send email with reset link
            $subject = "Password Reset";
            $message = <<<END
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password Verification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .verification-link { background-color: #f5f5f5; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0; word-break: break-all; }
        .verification-link a { color: #1a73e8; text-decoration: none; font-weight: bold; }
        h2 { color: #1a73e8; border-bottom: 2px solid #1a73e8; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2>Forgot Password Verification</h2>
        <p>Hello,</p>
        <p>You've requested to reset your password. Click the link below to proceed:</p>
        <div class="verification-link">
            <a href="https://mccalumnitracker.com/admin/new-password.php?token=$token">Reset Password</a>
        </div>
        <p>This password reset link is valid for 30 minutes.</p>
        <p>If you did not request a password reset, please ignore this email or contact support.</p>
        <p>Best regards,<br>Support Team</p>
    </div>
</body>
</html>
END;

            $sender = "From: johnchristianfariola@gmail.com";
            // Assuming $mail is already set up in mailer.php
            $mail = require __DIR__ . "/mailer.php";
            $mail->setFrom("noreply@example.com");
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->isHTML(true); // Set email format to HTML
            $mail->Body = $message;

            try {
                if ($mail->send()) {
                    $info = "We've sent a password reset code to your email - $email";
                    $_SESSION['info'] = $info;
                    $_SESSION['email'] = $email;

                    // Create log entry
                    $logData = [
                        "content" => "You request reset link for forgot password",
                        "email" => $email,
                        "status" => "success",
                        "timestamp" => date("Y-m-d H:i:s"),
                        "type" => "forgot_password"
                    ];

                    // Insert log entry into Firebase
                    $firebase->insert("logs", $logData);
                    header('location: index.php?token=' . $token);
                    exit();
                } else {
                    $errors['otp-error'] = "Failed while sending code!";
                }
            } catch (Exception $e) {
                $errors['otp-error'] = "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
            }
        } else {
            $errors['email'] = "This email address does not exist!";
        }
    }
}

if (isset($_POST['change-password'])) {
    $_SESSION['info'] = "";
    $token = $_POST["token"];
    $token_hash = hash("sha256", $token);

    // Construct the path with layers
    $adminPath = "admin/{$adminNodeKey}/{$layer_one}/{$layer_two}";

    // Fetch the admin data from Firebase
    $data = $firebase->retrieve($adminPath);
    $admin = json_decode($data, true);
    debug_log("Admin data retrieved for password change:");
    debug_log($admin);

    if (!$admin || !isset($admin['reset_token_hash']) || $admin['reset_token_hash'] !== $token_hash) {
        $errors['token'] = "Token not found or invalid";
    } else {
        if (isset($admin["reset_token_expires_at"]) && strtotime($admin["reset_token_expires_at"]) <= time()) {
            $errors['token'] = "Token has expired";
        } elseif (strlen($_POST["password"]) < 8) {
            $errors['password'] = "Password must be at least 8 characters";
        } elseif (!preg_match("/[a-z]/i", $_POST["password"])) {
            $errors['password'] = "Password must contain at least one letter";
        } elseif (!preg_match("/[0-9]/", $_POST["password"])) {
            $errors['password'] = "Password must contain at least one number";
        } elseif ($_POST["password"] !== $_POST["password_confirmation"]) {
            $errors['password'] = "Passwords must match";
        } else {
            $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

            // Update the user's password in Firebase and clear reset token
            $admin["password"] = $password_hash;
            $admin["reset_token_hash"] = null;
            $admin["reset_token_expires_at"] = null;
            $updateResult = $firebase->update($adminPath, null, $admin);
            debug_log("Password update result:");
            debug_log($updateResult);

            // Create log entry for password reset
            $logData = [
                "content" => "You update your forgot password",
                "status" => "success",
                "timestamp" => date("Y-m-d H:i:s"),
                "type" => "password_reset"
            ];

            // Insert log entry into Firebase
            $firebase->insert("logs", $logData);
            $_SESSION['info'] = "Your password has been changed. Now you can log in with your new password.";
            header('Location: index.php?token=' . $token);
            exit();
        }
    }
}

// Display errors if any
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
}
?>