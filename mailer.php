<?php
require_once 'includes/config.php';

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . "/vendor/autoload.php";

// Load the configuration

$mail = new PHPMailer(true);

// $mail->SMTPDebug = SMTP::DEBUG_SERVER;

$mail->isSMTP();
$mail->SMTPAuth = true;
    
$mail->Host = "smtp.gmail.com";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
$mail->Username = EMAIL_USERNAME; // Use the defined constant
$mail->Password = EMAIL_PASSWORD; // Use the defined constant

$mail->isHtml(true);

return $mail;
