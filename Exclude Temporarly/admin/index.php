<?php
session_start();
include 'controllerUserData.php';

try {
    // Retrieve admin data from Firebase using the adminNodeKey
    $adminData = $firebase->retrieve("admin/{$adminNodeKey}");
    $adminData = json_decode($adminData, true);

    // Navigate through layer_one and layer_two
    if (!isset($adminData[$layer_one][$layer_two])) {
        header('Location: lock.php');
        exit();
    }

    $adminNode = $adminData[$layer_one][$layer_two];

    if (!$adminNode || !isset($adminNode['lockscreen']) || !isset($adminNode['mfa'])) {
        // If admin data, lockscreen status, or mfa status is missing, redirect to lock page
        header('Location: lock.php');
        exit();
    }

    // Check lockscreen and MFA status
    if ($adminNode['lockscreen'] || $adminNode['mfa']) {
        // If either lockscreen or MFA is true, enforce token verification
        if (!isset($_GET['token']) || !isset($_SESSION['admin_token']) || $_GET['token'] !== $_SESSION['admin_token']) {
            header('Location: lock.php');
            exit();
        }

        // Additional token verification
        if (!isset($adminNode['token']) || $adminNode['token'] !== $_SESSION['admin_token'] || !isset($adminNode['reset_token_expires_at'])) {
            session_destroy();
            header('Location: lock.php');
            exit();
        }

        // Check if token has expired
        $expiresAt = strtotime($adminNode['reset_token_expires_at']);
        if (time() > $expiresAt) {
            session_destroy();
            header('Location: lock.php');
            exit();
        }
    } else {
        // If both lockscreen and MFA are false, use token_url verification
        if (!isset($_GET['token_url']) && isset($adminNode['token_url'])) {
            // Redirect to same page with token_url parameter
            header('Location: ' . $_SERVER['PHP_SELF'] . '?token_url=' . urlencode($adminNode['token_url']));
            exit();
        }

        // Check if token_url parameter exists and matches
        if (!isset($_GET['token_url']) || !isset($adminNode['token_url']) || $_GET['token_url'] !== $adminNode['token_url']) {
            header('Location: includes/404.html');
            exit();
        }

        // After token_url verification, check admin session
        if (isset($_SESSION['admin'])) {
            header('location: home.php');
            exit();
        }
    }
} catch (Exception $e) {
    // Error accessing Firebase
    session_destroy();
    header('Location: lock.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="images/icons/favicon.ico" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../bower_components/fontawesome-pro-5.15.3-web/css/all.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="../dist/css/login_util.css">
    <link rel="stylesheet" type="text/css" href="../dist/css/login_main.css">
    <!--===============================================================================================-->
</head>
<body>
    <?php
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger text-center'>$error</div>";
        }
    }
    if (isset($_SESSION['info'])) {
        echo "<div class='alert alert-success text-center'>" . $_SESSION['info'] . "</div>";
        unset($_SESSION['info']);
    }
    ?>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt" data-tilt>
                    <img src="../images/logo.png" alt="IMG">
                </div>
                <div id="form-container" class="form-container">
                    <!-- Forms will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!--===============================================================================================-->
    <script src="../vendor/jquery/jquery-3.2.1.min.js"></script>
    <!--===============================================================================================-->
    <script src="../vendor/bootstrap/js/popper.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <!--===============================================================================================-->
    <script src="../vendor/select2/select2.min.js"></script>
    <!--===============================================================================================-->
    <script src="../vendor/tilt/tilt.jquery.min.js"></script>
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
    <!--===============================================================================================-->
    <script src="../dist/js/login.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');
    const formContainer = document.getElementById('form-container');

    const loginFormHTML = `
        <form class="login100-form validate-form" id="login-form" action="login.php" method="POST">
            <span class="login100-form-title">
                Admin Login
            </span>

            <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                <input class="input100" type="text" name="username" placeholder="Input Username" required autofocus autocomplete="off">
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                </span>
            </div>

            <div class="wrap-input100 validate-input" data-validate="Password is required">
                <input class="input100" type="password" name="password" placeholder="Input Password">
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                </span>
            </div>

            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <div class="container-login100-form-btn">
                <button class="login100-form-btn" name="login" disabled>
                    Login
                </button>
            </div>

            <div class="text-center p-t-12">
                <span class="txt1">
                    Forgot
                </span>
                <a class="txt2" href="#" onclick="showForgotPasswordForm(); return false;">
                    Username / Password?
                </a>
            </div>

            <div class="text-center p-t-136">
            </div>
        </form>
    `;

    const forgotPasswordFormHTML = `
        <form class="login100-form validate-form" id="forgot-password-form" action="index.php" method="POST" autocomplete="off">
            <span class="login100-form-title">
                Forgot Password
            </span>

            <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                <input class="input100" type="email" name="email" placeholder="Email" required>
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                </span>
            </div>

            <div class="container-login100-form-btn">
                <button class="login100-form-btn" type="submit" name="check-email" disabled>
                    Reset Password
                </button>
            </div>

            <div class="text-center p-t-12">
                <a class="txt2" href="#" onclick="showLoginForm(); return false;">
                    Back to Login
                </a>
            </div>
        </form>
    `;

    // Make these global functions
    window.showLoginForm = function() {
        console.log('Showing login form');
        formContainer.innerHTML = loginFormHTML;
        getLocation();
    };

    window.showForgotPasswordForm = function() {
        console.log('Showing forgot password form');
        formContainer.innerHTML = forgotPasswordFormHTML;
        enableSubmitButtons();
    };

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                enableSubmitButtons();
            }, function(error) {
                console.error("Error obtaining location: ", error);
                alert("Please enable location services to proceed.");
                enableSubmitButtons();
            });
        } else {
            alert("Geolocation is not supported by this browser.");
            enableSubmitButtons();
        }
    }

    function enableSubmitButtons() {
        const loginButton = document.querySelector('#login-form .login100-form-btn');
        const forgotPasswordButton = document.querySelector('#forgot-password-form .login100-form-btn');
        if (loginButton) loginButton.disabled = false;
        if (forgotPasswordButton) forgotPasswordButton.disabled = false;
    }

    // Initially show the login form
    showLoginForm();
});
</script>
</body>
</html>