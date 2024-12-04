<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
header('Content-Type: application/json');
require '../vendor/autoload.php';
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php';

$firebase = new firebaseRDB($databaseURL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted email and reCAPTCHA response
    $submitted_email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    if (!isValidEmail($submitted_email)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid Gmail address'
        ]);
        exit;
    }

    // Verify reCAPTCHA
    $secretKey = RECAPTCHA_SECRET_KEY;
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
    $responseData = json_decode($verifyResponse);

    if (!$responseData->success) {
        echo json_encode([
            'success' => false,
            'message' => 'CAPTCHA verification failed.'
        ]);
        exit;
    }

    // Construct the path with layers
    $adminPath = "admin/{$adminNodeKey}/{$layer_one}/{$layer_two}";

    // Get admin data from Firebase using adminNodeKey
    $admin_data = $firebase->retrieve($adminPath);
    $admin_data = json_decode($admin_data, true);

    // Check if submitted email matches admin email
    if ($submitted_email !== $admin_data['email']) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address. Please use your admin email.'
        ]);
        exit;
    }

    // Generate a 6-digit verification code
    $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Update the admin code in Firebase using adminNodeKey
    $update_data = [
        'code' => $verification_code,
        'code_timestamp' => time() // Add timestamp for code expiration
    ];

    try {
        // Update the code in Firebase
        $firebase->update($adminPath, "", $update_data);

        // Send verification email
        if (sendVerificationEmail($admin_data['email'], $admin_data['firstname'], $verification_code)) {
            $_SESSION['lockscreen_email'] = $admin_data['email'];
            echo json_encode([
                'success' => true,
                'message' => 'Verification code has been sent to your email',
                'redirect' => 'verify_code.php'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send verification code. Please try again.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

function isValidEmail($email) {
    // Check if the email format is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    // Extract the domain from the email
    list(, $domain) = explode('@', $email);
    // Check if the domain is gmail.com
    if ($domain !== 'gmail.com') {
        return false;
    }
    // Check if the domain has valid MX records
    if (!checkdnsrr($domain, 'MX')) {
        return false;
    }
    return true;
}

function sendVerificationEmail($email, $firstname, $code) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom(EMAIL_USERNAME, 'Admin System');
        $mail->addAddress($email, $firstname);
        $mail->isHTML(true);
        $mail->Subject = 'Lockscreen Verification Code';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2>Lockscreen Verification</h2>
                <p>Hello {$firstname},</p>
                <p>You requested to unlock your admin account. Please use the following verification code:</p>
                <div style='background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 24px; letter-spacing: 5px;'>
                    <strong>{$code}</strong>
                </div>
                <p>This code will expire in 15 minutes.</p>
                <p>If you didn't request this verification, please contact your system administrator immediately.</p>
                <p>Best regards,<br>Admin System</p>
            </div>
        ";
        return $mail->send();
    } catch (Exception $e) {
        error_log('Mail Error: ' . $e->getMessage());
        return false;
    }
}