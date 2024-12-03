<?php
session_start();
include 'includes/firebaseRDB.php';
require_once 'includes/config.php';

$firebase = new firebaseRDB($databaseURL);

// Retrieve admin data
$adminData = $firebase->retrieve("admin");
$adminData = json_decode($adminData, true);

// Ensure admin data is retrieved
if (!$adminData) {
    header('Location: includes/404.html');
    exit();
}

// Access specific admin node using its unique key
if (!isset($adminData[$adminNodeKey])) {
    header('Location: includes/404.html');
    exit();
}

$adminNode = $adminData[$adminNodeKey];

// Check if token_url is not in URL but exists in admin data
if (!isset($_GET['token_url']) && isset($adminNode['token_url'])) {
    // Redirect to same page with token_url parameter
    header('Location: ' . $_SERVER['PHP_SELF'] . '?token_url=' . urlencode($adminNode['token_url']));
    exit();
}

// Check if token_url parameter exists and matches
if (!isset($_GET['token_url']) || $_GET['token_url'] !== $adminNode['token_url']) {
    header('Location: includes/404.html');
    exit();
}

// Check if lockscreen and MFA are both disabled
if (!$adminNode['lockscreen'] && !$adminNode['mfa']) {
    header('Location: index.php');
    exit();
}

// Set user data from admin data to avoid undefined variable
$user = [
    'firstname' => $adminNode['firstname'] ?? '',
    'lastname' => $adminNode['lastname'] ?? '',
    'image_url' => $adminNode['image_url'] ?? 'uploads/default_profile.png',
    'email' => $adminNode['email'] ?? '',
    'created_on' => $adminNode['created_on'] ?? ''
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>AdminLTE 2 | Locksrcjeen</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .info-icon:hover { color: #367fa9; }
        .custom-tooltip { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .tooltip-header { font-weight: bold; margin-bottom: 5px; color: white; border-bottom: 1px solid #ddd; padding-bottom: 3px; }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="hold-transition lockscreen" style="   overflow-y: hidden;">
    <div class="lockscreen-wrapper">
        <div class="lockscreen-logo">
            <a href="#"><b>Admin</b> Lock Screen</a>
        </div>
        <div class="lockscreen-name">
            <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>
            <br>
            <i class="fa fa-info-circle info-icon" data-toggle="tooltip" data-html="true" data-template='<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner custom-tooltip"></div></div>' title="<div class='tooltip-header'>Why do I have to do this?</div>The Admin Enabled the Two-factor authentication (2FA). You need to enter the verification code to access the admin login page."></i>
        </div>
        <div class="lockscreen-item">
            <div class="lockscreen-image">
                <img src="<?php echo htmlspecialchars($user['image_url']); ?>" alt="User Image">
            </div>
            <div class="lockscreen-credentials">
                <form id="email-form">
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" placeholder="name@example.com" required>
                        <div class="input-group-btn">
                            <button type="submit" class="btn">
                                <i class="fa fa-arrow-right text-muted"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <form id="code-form" style="display: none;">
                    <div class="input-group">
                        <input type="text" class="form-control" name="verification_code" placeholder="Enter verification code" required>
                        <div class="input-group-btn">
                            <button type="submit" class="btn">
                                <i class="fa fa-arrow-right text-muted"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <center>
        <div class="g-recaptcha" data-sitekey="6LfKXHwqAAAAAM_MCNoufDM0CEZaazGwShOlMApS"></div>

        </center>

        <div class="help-block text-center" id="message">
            <?php if ($user['created_on']): ?>
                <small>Account created on: <?php echo htmlspecialchars($user['created_on']); ?></small>
            <?php endif; ?>
        </div>
        <div class="lockscreen-footer text-center">
            Copyright &copy; 2024-2025 <b><a href="https://www.facebook.com/FariolaJohnChristian" class="text-black">John Christian Fariola</a></b><br>
            All rights reserved
        </div>
    </div>
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        $(function () {
            $('#email-form').on('submit', function (e) {
                e.preventDefault();
                var email = $(this).find('input[name="email"]').val();
                var recaptchaResponse = grecaptcha.getResponse();

                $.ajax({
                    url: 'verify_lockscreen.php',
                    method: 'POST',
                    data: {
                        email: email,
                        'g-recaptcha-response': recaptchaResponse
                    },
                    dataType: 'json',
                    beforeSend: function () {
                        $('#message').html('<i class="fa fa-spinner fa-spin"></i> Sending verification code...').removeClass('text-danger').addClass('text-info');
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#message').html(response.message).removeClass('text-danger').addClass('text-success');
                            $('#email-form').hide();
                            $('#code-form').show();
                        } else {
                            $('#message').html(response.message).removeClass('text-success').addClass('text-danger');
                        }
                    },
                    error: function () {
                        $('#message').html('An error occurred. Please try again.').removeClass('text-success').addClass('text-danger');
                    }
                });
            });

            $('#code-form').on('submit', function (e) {
                e.preventDefault();
                var code = $(this).find('input[name="verification_code"]').val();

                $.ajax({
                    url: 'verify_code.php',
                    method: 'POST',
                    data: { code: code },
                    dataType: 'json',
                    beforeSend: function () {
                        $('#message').html('<i class="fa fa-spinner fa-spin"></i> Verifying code...').removeClass('text-danger').addClass('text-info');
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#message').html(response.message).removeClass('text-danger').addClass('text-success');
                            setTimeout(function () {
                                window.location.href = response.redirect;
                            }, 1500);
                        } else {
                            $('#message').html(response.message).removeClass('text-success').addClass('text-danger');
                        }
                    },
                    error: function () {
                        $('#message').html('An error occurred. Please try again.').removeClass('text-success').addClass('text-danger');
                    }
                });
            });

            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            $('[data-toggle="tooltip"]').tooltip({ html: true, container: 'body' });
        });
    </script>
</body>
</html>