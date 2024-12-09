<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
header('Content-Type: application/json');

require 'vendor/autoload.php';
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php';

$firebase = new firebaseRDB($databaseURL);

if (isset($_POST['schoolId'], $_POST['lastname'], $_POST['email'], $_POST['password'], $_POST['curpassword'])) {
    $lastname = $_POST['lastname'];
    $studentid = $_POST['schoolId'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['curpassword'];

    if ($password !== $cpassword) {
        echo json_encode(['error' => 'Confirm password not matched!']);
        exit();
    }

    $data = $firebase->retrieve("alumni");
    $data = json_decode($data, true);

    $email_exists = false;
    $email_verified = false;
    foreach ($data as $id => $alumni) {
        if (isset($alumni['email']) && $alumni['email'] == $email) {
            $email_exists = true;
            if (isset($alumni['status']) && $alumni['status'] === 'verified') {
                $email_verified = true;
            }
            break;
        }
    }

    if ($email_exists && $email_verified) {
        echo json_encode(['error' => 'This email is already associated with a verified account.']);
        exit();
    }

    $alumni_id = null;
    $already_verified = false;
    foreach ($data as $id => $alumni) {
        if (strcasecmp($alumni['lastname'], $lastname) == 0 && $alumni['studentid'] == $studentid) {
            $alumni_id = $id;
            if (isset($alumni['status']) && $alumni['status'] === 'verified') {
                $already_verified = true;
            }
            break;
        }
    }

    if (!$alumni_id) {
        echo json_encode(['error' => 'No matching alumni found with the provided last name and Alumni ID!']);
        exit();
    }

    if ($already_verified) {
        echo json_encode(['error' => 'You are already verified. You cannot sign up again.']);
        exit();
    }

    $encpass = password_hash($password, PASSWORD_BCRYPT);
    $code = rand(999999, 111111);
    $status = "notverified";

    $update_data = [
        'email' => $email,
        'password' => $encpass,
        'code' => $code,
        'status' => $status
    ];

    try {
        $firebase->update("alumni", $alumni_id, $update_data);

        if (sendVerificationEmail($email, $lastname, $code)) {
            $_SESSION['success'] = "Verification code has been sent to your email";
            $_SESSION['info'] = "We've sent a verification code to your email - $email";
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;
            echo json_encode([
                'success' => true,
                'message' => 'Verification code has been sent to your email',
                'redirect' => '../user-otp.php'
            ]);
            exit();
        } else {
            echo json_encode(['error' => 'Failed to send verification code. Please try again.']);
            exit();
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed while sending code! ' . $mail->ErrorInfo]);
        exit();
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

function sendVerificationEmail($email, $lastname, $code) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom(EMAIL_USERNAME, 'Aurelia!');
        $mail->addAddress($email, $lastname);
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification Code';
        
        // Updated email body
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2>Email Verification</h2>
                <p>Thank you for registering! Please use the following code to verify your email address:</p>
                <div style='background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 24px; letter-spacing: 5px;'>
                    <strong>{$code}</strong>
                </div>
                <p>This code will expire in 15 minutes.</p>
                <p>If you didn't request this verification, please ignore this email.</p>
            </div>
        ";

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}