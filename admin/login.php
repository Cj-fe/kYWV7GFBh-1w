<?php
session_start();
include 'includes/firebaseRDB.php';
require_once 'includes/config.php';
require_once '../vendor/autoload.php';

use Detection\MobileDetect;

$firebase = new firebaseRDB($databaseURL);

// Initialize login attempt count if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Function to get the user's IP address
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Function to get browser and device information
function getBrowserAndDevice() {
    $detect = new MobileDetect();
    $device = $detect->isMobile() ? 'Mobile' : 'Desktop';
    // Simple browser detection using HTTP_USER_AGENT
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $browser = 'Unknown';
    if (strpos($userAgent, 'Chrome') !== false) {
        $browser = 'Chrome';
    } elseif (strpos($userAgent, 'Firefox') !== false) {
        $browser = 'Firefox';
    } elseif (strpos($userAgent, 'Safari') !== false) {
        $browser = 'Safari';
    } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
        $browser = 'Internet Explorer';
    }
    return ['browser' => $browser, 'device' => $device];
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Retrieve admin credentials from Firebase
    $adminData = $firebase->retrieve("admin/{$adminNodeKey}");
    $adminData = json_decode($adminData, true);

    // Navigate through layer_one and layer_two
    if (isset($adminData[$layer_one][$layer_two])) {
        $adminNode = $adminData[$layer_one][$layer_two];

        if (isset($adminNode['user']) && $adminNode['user'] === $username) {
            if (password_verify($password, $adminNode['password'])) {
                $_SESSION['admin'] = $username; // Set session admin ID
                $_SESSION['login_attempts'] = 0; // Reset login attempts on success

                // Get the token from admin data
                $token = isset($adminNode['token']) ? $adminNode['token'] : '';

                // Redirect with token
                header('Location: home.php?token=' . urlencode($token));
                exit();
            } else {
                // Increase login attempt count on failure
                $_SESSION['login_attempts'] += 1;
            }
        } else {
            // Increase login attempt count if user is not found
            $_SESSION['login_attempts'] += 1;
        }
    }

    // Check if login attempts exceed 3
    if ($_SESSION['login_attempts'] >= 3) {
        // Log to Firebase
        $ipAddress = getUserIP();
        $timestamp = date_create('now', new DateTimeZone('Asia/Manila'))->format('Y-m-d H:i:s');
        $browserAndDevice = getBrowserAndDevice();
        $logData = [
            "ip" => $ipAddress,
            "timestamp" => $timestamp,
            "attempts" => $_SESSION['login_attempts'],
            "username" => $username,
            "browser" => $browserAndDevice['browser'],
            "device" => $browserAndDevice['device'],
            "type" => "login_attempt",
            "latitude" => $latitude,
            "longitude" => $longitude
        ];
        $firebase->insert("logs", $logData); // Save the log data to Firebase
    }

    // If login failed, redirect back with token (if exists)
    $token = isset($adminNode['token']) ? $adminNode['token'] : '';
    header('Location: index.php?token=' . urlencode($token));
    exit();
}

// If no login attempt, redirect to index without token
header('Location: index.php');
exit();
?>