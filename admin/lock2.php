<?php
session_start();
require_once '../vendor/autoload.php';
include 'includes/firebaseRDB.php';
require_once 'includes/config.php';

$firebase = new firebaseRDB($databaseURL);

try {
    // Construct the path with layers
    $adminPath = "admin/{$adminNodeKey}/{$layer_one}/{$layer_two}";
    
    // Retrieve admin data
    $adminData = $firebase->retrieve($adminPath);
    $adminData = json_decode($adminData, true);

    // Check if the specific admin node exists
    if (!$adminData) {
        header('Location: lock.php');
        exit();
    }

    // Access the specific admin node
    $adminNode = $adminData;

    // Check lockscreen and MFA status
    if (!$adminNode['lockscreen'] && isset($adminNode['mfa']) && !$adminNode['mfa']) {
        if (!isset($_SESSION['admin'])) {
            header('Location: index.php');
            exit();
        }
        header('Location: home.php');
        exit();
    }

    // Check lockscreen token
    if ($adminNode['lockscreen']) {
        if (!isset($_GET['token']) || $_GET['token'] !== $adminNode['token2']) {
            header('Location: lock.php');
            exit();
        }
        if (!isset($adminNode['token2']) || $adminNode['token2'] !== $_GET['token'] || !isset($adminNode['reset_token_expires_at'])) {
            session_destroy();
            header('Location: lock.php');
            exit();
        }
        $expiresAt = strtotime($adminNode['reset_token_expires_at']);
        if (time() > $expiresAt) {
            session_destroy();
            header('Location: lock.php');
            exit();
        }
    } else {
        if (isset($_SESSION['admin'])) {
            header('location: home.php');
            exit();
        }
    }

    // Set user data from admin node
    $user = [
        'email' => $adminNode['email'] ?? '',
        'phone' => $adminNode['phone'] ?? '',
        'user' => $adminNode['user'] ?? 'admin'
    ];
} catch (Exception $e) {
    session_destroy();
    header('Location: lock.php');
    exit();
}

// Final token check
if ($_GET['token'] !== $adminNode['token2']) {
    header('Location: lock.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Lockscreen</title>
    <?php include 'includes/header.php'; ?>
    <style>
        .lockscreen {
            overflow-y: hidden;
            background: #f4f6f9;
        }
        .lockscreen-wrapper {
            max-width: 400px;
            margin: 0 auto;
            margin-top: 10%;
            padding: 20px;
        }
        .lockscreen-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .lockscreen-logo a {
            font-size: 35px;
            font-weight: 300;
            color: #444;
            text-decoration: none;
        }
        .auth-method-btn {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .auth-method-btn button {
            padding: 12px;
            font-size: 16px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        .auth-method-btn button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .auth-method-btn i {
            font-size: 20px;
        }
        .popup-modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }
        .popup-modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { 
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .close-button {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            color: #666;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .close-button:hover {
            color: #333;
        }
        .input-container {
            margin-top: 20px;
        }
        .verification-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
            letter-spacing: 4px;
            margin-bottom: 15px;
            transition: border-color 0.3s ease;
        }
        .verification-input:focus {
            border-color: #3c8dbc;
            outline: none;
        }
        .verify-btn {
            width: 100%;
            padding: 12px;
            background: #3c8dbc;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .verify-btn:hover {
            background: #367fa9;
        }
        #countdown {
            text-align: center;
            margin-top: 15px;
            color: #666;
            font-size: 14px;
        }
        #resend-code {
            display: none;
            width: 100%;
            padding: 10px;
            background: none;
            border: 1px solid #3c8dbc;
            color: #3c8dbc;
            border-radius: 4px;
            margin-top: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        #resend-code:hover {
            background: #f8f9fa;
        }
        .alert {
            padding: 15px;
            margin: 15px 0;
            border: 1px solid transparent;
            border-radius: 4px;
            animation: slideIn 0.3s ease;
        }
        .alert-success {
            background-color: #dff0d8;
            border-color: #d6e9c6;
            color: #3c763d;
        }
        .alert-danger {
            background-color: #f2dede;
            border-color: #ebccd1;
            color: #a94442;
        }
        .user-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .user-info .fa-user-circle {
            font-size: 64px;
            color: #3c8dbc;
            margin-bottom: 10px;
        }
        .user-info h4 {
            color: #444;
            margin: 10px 0;
        }
        .user-info p {
            color: #666;
            margin: 5px 0;
        }
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loading .verify-btn {
            pointer-events: none;
            opacity: 0.8;
        }
    </style>
</head>
<body class="hold-transition lockscreen">
    <div class="lockscreen-wrapper">
        <div class="lockscreen-logo">
            <a href="#"><b>Admin</b>Panel</a>
        </div>

        <div class="user-info">
            <i class="fa fa-user-circle"></i>
            <h4><?php echo htmlspecialchars($user['user']); ?></h4>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="text-center">
            <p>Please verify your identity to continue</p>
            <div class="auth-method-btn">
                <button class="btn btn-primary" id="number-auth">
                    <i class="fa fa-phone"></i> Phone Verification
                </button>
              
            </div>
        </div>

        <div id="message" class="text-center"></div>

        <div class="lockscreen-footer text-center">
            <small>
                Copyright &copy; 2024 <br>
                <strong>Admin Panel System</strong>
            </small>
        </div>
    </div>

    <!-- Popup modal for phone verification -->
    <div id="number-auth-modal" class="popup-modal">
        <div class="popup-modal-content">
            <span class="close-button">&times;</span>
            <h3>Phone Verification</h3>
            <p>
                Enter the verification code sent to<br>
                <strong><?php echo substr($user['phone'], 0, 3) . 'xxxx' . substr($user['phone'], -4); ?></strong>
            </p>
            <div class="input-container">
                <input type="text" 
                       id="verification-code" 
                       class="verification-input" 
                       placeholder="000000" 
                       maxlength="6" 
                       autocomplete="off">
                <button class="verify-btn" id="verify-code">
                    Verify Code
                </button>
            </div>
            <div id="countdown"></div>
            <button id="resend-code" class="btn">Resend Code</button>
        </div>
    </div>

    <!-- Required Scripts -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
        $(function() {
            let countdownInterval;
            const COUNTDOWN_TIME = 300; // 5 minutes in seconds

            function startCountdown(duration) {
                clearInterval(countdownInterval);
                let timer = duration;
                $('#resend-code').hide();
                $('#countdown').show();

                countdownInterval = setInterval(function() {
                    const minutes = parseInt(timer / 60, 10);
                    const seconds = parseInt(timer % 60, 10);
                    $('#countdown').text(
                        `Request new code in ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`
                    );

                    if (--timer < 0) {
                        clearInterval(countdownInterval);
                        $('#countdown').hide();
                        $('#resend-code').show();
                    }
                }, 1000);
            }

            function showMessage(message, type = 'success') {
                $('#message').html(`
                    <div class="alert alert-${type} alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        ${message}
                    </div>
                `);
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    $('.alert').fadeOut();
                }, 5000);
            }

            function sendVerificationCode() {
                const $btn = $('#resend-code');
                $btn.prop('disabled', true)
                    .html('<div class="spinner"></div>Sending...');

                $.ajax({
                    url: 'verify_number.php',
                    type: 'POST',
                    data: {
                        action: 'send_code'
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('Verification code sent successfully');
                            startCountdown(COUNTDOWN_TIME);
                        } else {
                            showMessage(response.message || 'Error sending verification code', 'danger');
                        }
                        $btn.prop('disabled', false)
                            .text('Resend Code');
                    },
                    error: function(xhr, status, error) {
                        showMessage('Error sending code: ' + error, 'danger');
                        $btn.prop('disabled', false)
                            .text('Resend Code');
                    }
                });
            }

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Handle number authentication button click
            $('#number-auth').on('click', function() {
                $('#number-auth-modal').fadeIn(300);
                sendVerificationCode();
                $('#verification-code').val('').focus();
            });

            // Handle verification code input
            $('#verification-code').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 6) {
                    $('#verify-code').focus();
                }
            });

            // Handle modal close
            $('.close-button, .popup-modal').on('click', function(e) {
                if (e.target === this) {
                    $('#number-auth-modal').fadeOut(300);
                    clearInterval(countdownInterval);
                }
            });

            // Handle verify code button click
            $('#verify-code').on('click', function() {
                const code = $('#verification-code').val();
                if (code.length !== 6) {
                    showMessage('Please enter a valid 6-digit code', 'danger');
                    return;
                }

                const $btn = $(this);
                $btn.prop('disabled', true)
                    .html('<div class="spinner"></div>Verifying...');

                $.ajax({
                    url: 'verify_number.php',
                    type: 'POST',
                    data: {
                        action: 'verify_code',
                        code: code
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('Verification successful! Redirecting...', 'success');
                            setTimeout(() => {
                                window.location.href = response.redirect;
                            }, 1500);
                        } else {
                            showMessage(response.message || 'Verification failed', 'danger');
                            $btn.prop('disabled', false)
                                .text('Verify Code');
                        }
                    },
                    error: function(xhr, status, error) {
                        showMessage('Error verifying code: ' + error, 'danger');
                        $btn.prop('disabled', false)
                            .text('Verify Code');
                    }
                });
            });

            // Handle enter key press in verification code input
            $('#verification-code').on('keypress', function(e) {
                if (e.which === 13 && this.value.length === 6) {
                    $('#verify-code').click();
                }
            });

            // Handle resend code button click
            $('#resend-code').on('click', function() {
                sendVerificationCode();
            });
        });
    </script>
</body>
</html>