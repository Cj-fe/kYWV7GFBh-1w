<?php
session_start();

require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php';

$firebase = new firebaseRDB($databaseURL);
$email = "";
$errors = array();


function debug_log($message)
{
    error_log(print_r($message, true));
}

if (isset($_POST['check-email'])) {
    debug_log("Form submitted");
    $email = $_POST['email'];
    debug_log("Email submitted: " . $email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format!";
    } else {
        $data = $firebase->retrieve("alumni");
        debug_log("Data retrieved from Firebase:");
        debug_log($data);
        $alumni = json_decode($data, true);
        $found_alumni = null;
        $found_alumni_key = null;

        foreach ($alumni as $key => $value) {
            if ($value['email'] === $email) {
                $found_alumni = $value;
                $found_alumni_key = $key;
                break;
            }
        }

        if ($found_alumni) {
            $token = bin2hex(random_bytes(16));
            $token_hash = hash("sha256", $token);
            $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

            $found_alumni["reset_token_hash"] = $token_hash;
            $found_alumni["reset_token_expires_at"] = $expiry;
            $updateResult = $firebase->update("alumni", $found_alumni_key, $found_alumni);
            debug_log("Update result:");
            debug_log($updateResult);

            $subject = "Password Reset";
            $message = <<<END
            <html>
            <head>
            <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f9f9f9;
                margin: 0;
                padding: 0;
                color: #333;
            }
            .container {
                width: 100%;
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 8px;
            }
            .header {
                text-align: center;
                padding: 20px 0;
                background-color: #f2f2f2;
                border-bottom: 1px solid #ddd;
            }
            .header img {
                max-width: 150px;
            }
            .content {
                padding: 20px;
                text-align: center;
            }
            .content h1 {
                font-size: 24px;
                color: #333;
            }
            .content p {
                font-size: 16px;
                line-height: 1.5;
                color: #666;
            }
            .button {
                display: inline-block;
                padding: 15px 25px;
                font-size: 16px;
                color: #ffffff;
                background-color: #007BFF;
                text-decoration: none;
                border-radius: 5px;
                margin: 20px 0;
            }
            .footer {
                padding: 20px;
                text-align: center;
                font-size: 14px;
                color: #777;
                border-top: 1px solid #ddd;
            }
            .footer a {
                color: #007BFF;
                text-decoration: none;
            }
            .social-icons {
                margin-top: 10px;
            }
            .social-icons img {
                width: 24px;
                margin: 0 5px;
            }
            </style>
            </head>
            <body>
            <div class="container">
                <div class="header">
                    <img src="https://mccalumnitracker.com/images/logo/alumni_logo.png" alt="Company Logo">
                </div>
                <div class="content">
                    <img src="https://ucarecdn.com/09bcae53-4a97-41fa-a664-25c0c1042382/airywomanopeninglockwithakey.png" alt="Password Reset Image">
                    <h1>Reset your password</h1>
                    <p>We've got a request from you to reset the password for your account. Please click on the button below to get a new password.</p>
                    <a href="https://mccalumnitracker.com/new-password.php?token=$token" class="button" style="color: white; text-style;">Reset my password</a>
                    <p>If you didn't request a password reset, please ignore this email or contact support if you have questions.</p>
                </div>
                <div class="footer">
                    <p>Questions? Contact us at <a href="mailto:support@company.com">support@company.com</a> or call 1-877-123-4567.</p>
                    <p class="social-icons">
                        <a href="https://facebook.com"><img src="https://ucarecdn.com/7ead65a8-a270-4a31-8f89-abdbdc718677/fb.png" alt="Facebook"></a>
                        <a href="https://instagram.com"><img src="https://img.icons8.com/fluency/50/instagram-new.png" alt="Instagram"></a>
                        <a href="https://twitter.com"><img src="https://img.icons8.com/papercut/120/twitter.png" alt="Twitter"></a>
                    </p>
                    <p>© 2024 Company. All rights reserved.</p>
                </div>
            </div>
            </body>
            </html>
            END;

            $mail = require __DIR__ . "/mailer.php";
            $mail->setFrom("noreply@example.com");
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $message;

            try {
                if ($mail->send()) {
                    $_SESSION['success'] = "We've sent a password reset link to your email - $email";
                } else {
                    $_SESSION['error'] = "Failed while sending link!";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
            }
        } else {
            $_SESSION['error'] = "This email address does not exist!";
        }
    }
    header('Location: includes/auth.php');
    exit();
}

if (isset($_POST['change-password'])) {
    $_SESSION['info'] = "";
    $token = $_POST["token"];
    $token_hash = hash("sha256", $token);

    // Fetch the alumni data from Firebase
    $data = $firebase->retrieve("alumni");
    $alumni = json_decode($data, true);
    debug_log("Alumni data retrieved for password change:");
    debug_log($alumni);

    $found_alumni = null;
    $found_alumni_key = null;

    foreach ($alumni as $key => $value) {
        if (isset($value['reset_token_hash']) && $value['reset_token_hash'] === $token_hash) {
            $found_alumni = $value;
            $found_alumni_key = $key;
            break;
        }
    }

    if (!$found_alumni) {
        $errors['token'] = "Token not found or invalid";
    } else {
        if (isset($found_alumni["reset_token_expires_at"]) && strtotime($found_alumni["reset_token_expires_at"]) <= time()) {
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

            // Update the alumni's password in Firebase and clear reset token
            $found_alumni["password"] = $password_hash;
            $found_alumni["reset_token_hash"] = "";
            $found_alumni["reset_token_expires_at"] = "";
            $updateResult = $firebase->update("alumni", $found_alumni_key, $found_alumni);
            debug_log("Password update result:");
            debug_log($updateResult);

            // Update the password in MySQL
            $mysqlConn = getMySQLConnection();
            if (!$mysqlConn) {
                $errors['mysql'] = 'Failed to connect to MySQL database.';
            } else {
                $mysqlQuery = "UPDATE applicant SET password = '" . $mysqlConn->real_escape_string($password_hash) . "' WHERE email = '" . $mysqlConn->real_escape_string($found_alumni['email']) . "'";
                $mysqlResult = $mysqlConn->query($mysqlQuery);
                $mysqlConn->close();

                if (!$mysqlResult) {
                    $errors['mysql'] = 'Failed to update password in MySQL database.';
                } else {
                    $_SESSION['info'] = "Your password has been changed. Now you can log in with your new password in Both MCC ALUMNI TRACKER AND MCC DOCUMENT TRACKER.";
                    header('Location: index.php');
                    exit();
                }
            }
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