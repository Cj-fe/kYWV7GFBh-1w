<?php
session_start();
if (isset($_SESSION['admin'])) {
    header('location: home.php');
    exit();
}
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php';
include 'controllerUserData.php';

// Initialize Firebase
$firebase = new firebaseRDB($databaseURL);

$token = $_GET["token"] ?? null;
$errors = [];

if (!$token) {
    header('location: includes/error.php?error=No+token+provided');
    exit();
}

// Fetch admin data from Firebase
$data = $firebase->retrieve("admin");
$admin = json_decode($data, true);

$token_hash = hash("sha256", $token);

if (!$admin || !isset($admin['reset_token_hash']) || $admin['reset_token_hash'] !== $token_hash) {
    header('location: includes/error.php?error=Token+not+found+or+invalid');
    exit();
}

if (isset($admin["reset_token_expires_at"]) && strtotime($admin["reset_token_expires_at"]) <= time()) {
    header('location: includes/error.php?error=Token+has+expired');
    exit();
}

// The password change logic is now handled in controllerUserData.php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reset Password</title>
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
    <style>
        .form-container {
            min-height: 450px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .login100-form-btn {
            pointer-events: auto;
        }

        .text-center {
            text-align: center;
        }

        .p-t-12 {
            padding-top: 12px;
        }

        .txt2 {
            font-family: Poppins-Regular;
            font-size: 13px;
            line-height: 1.5;
            color: #666666;
        }

        @media (max-width: 992px) {
            .wrap-login100 {
                flex-direction: column;
                align-items: center;
                padding: 50px 15px 50px 15px;
            }

            .login100-pic {
                position: static;
                margin-bottom: 50px;
            }

            .login100-form {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt" data-tilt>
                    <img src="../images/logo.png" alt="IMG">
                </div>
                <div id="form-container" class="form-container">
                    <form class="login100-form validate-form" action="" method="POST">
                        <span class="login100-form-title">
                            Reset Password
                        </span>


                        <div class="wrap-input100 validate-input" data-validate="Password is required">
                            <input class="input100" type="password" name="password" placeholder="Create new password"
                                required>
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-lock" aria-hidden="true"></i>
                            </span>
                        </div>

                        <div class="wrap-input100 validate-input" data-validate="Confirm password is required">
                            <input class="input100" type="password" name="password_confirmation"
                                placeholder="Confirm your password" required>
                            <span class="focus-input100"></span>
                            <span class="symbol-input100">
                                <i class="fa fa-lock" aria-hidden="true"></i>
                            </span>
                        </div>

                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                        <div class="container-login100-form-btn">
                            <button class="login100-form-btn" type="submit" name="change-password">
                                Reset Password
                            </button>
                        </div>

                        <div class="text-center p-t-136">
                        </div>
                    </form>
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

</body>

</html>