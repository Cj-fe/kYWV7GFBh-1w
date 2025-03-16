<?php require_once 'includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureVault - Password Manager</title>
    <link rel="stylesheet" href="assets/login.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 5C13.66 5 15 6.34 15 8C15 9.66 13.66 11 12 11C10.34 11 9 9.66 9 8C9 6.34 10.34 5 12 5ZM12 19.2C9.5 19.2 7.29 17.92 6 15.98C6.03 13.99 10 12.9 12 12.9C13.99 12.9 17.97 13.99 18 15.98C16.71 17.92 14.5 19.2 12 19.2Z" fill="#667eea"/>
            </svg>
        </div>
        <h1>Welcome to SecureVault</h1>
        
        <?php
        // Display error messages
        if(isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        
        // Display success messages
        if(isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        
        <div id="notification" class="notification"></div>
        
        <div id="scan-animation" class="scan-animation">
            <div class="scan-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a10 10 0 0 1 10 10c0 1.1-.75 5.75-1.75 6.75L20 19c-1 1-4.5 1.75-5.75 1.75-1 0-4.75-.5-6.25-2-1-.75-3-2.25-2.75-5.75.25-3 1-4.75 2.25-6.25 1-1.25 2.25-1.75 3-2C11.25 4.25 11.5 3.25 12 2Z"></path>
                    <path d="M12 2c.5 1.25.75 2.25 1.25 2.75.75.5 2 1 3 2.25 1.25 1.5 2 3.25 2.25 6.25.25 3.5-1.75 5-2.75 5.75-1.5 1.5-5.25 2-6.25 2C8.5 20.75 5 20 4 19l-.25-.25C2.75 17.75 2 13.1 2 12a10 10 0 0 1 10-10Z"></path>
                </svg>
            </div>
        </div>
        
        <div id="scan-message" class="scan-message">Place your finger on the scanner</div>
        
        <form id="login-form" method="post" action="login_action.php">
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="master-password">Master password</label>
                <input type="password" id="master-password" name="master-password" placeholder="Enter your master password" required>
                <div class="password-toggle" id="password-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </div>
            </div>
            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me for 30 days</label>
            </div>
            <button type="submit">Log in</button>
            <div class="options">
                <a href="forgot_password.php">Forgot master password?</a>
                <a href="register.php">Create account</a>
            </div>
            <div class="divider">
                <span>OR</span>
            </div>
            <div class="biometric-login">
                <button type="button" class="biometric-button" id="biometric-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839-1.132c.449-1.29.777-2.676.777-4.868 0-4.368-2.997-7.956-7-8.843m-7 15.636l3-1 3 1m-6-15h12"></path>
                    </svg>
                    Login with biometrics
                </button>
                <button type="button" class="fingerprint-button" id="fingerprint-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839-1.132c.449-1.29.777-2.676.777-4.868 0-4.368-2.997-7.956-7-8.843m-7 15.636l3-1 3 1m-6-15h12"></path>
                    </svg>
                    Scan fingerprint
                </button>
            </div>
        </form>

        <!-- Loading spinner -->
        <div id="loading-spinner" class="loading-spinner" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M12 2a10 10 0 0 1 10 10c0 1.1-.75 5.75-1.75 6.75L20 19c-1 1-4.5 1.75-5.75 1.75-1 0-4.75-.5-6.25-2-1-.75-3-2.25-2.75-5.75.25-3 1-4.75 2.25-6.25 1-1.25 2.25-1.75 3-2C11.25 4.25 11.5 3.25 12 2Z"></path>
            </svg>
            <span>Verifying...</span>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(event) {
            // Disable the submit button
            const submitButton = event.target.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            // Show the loading spinner
            document.getElementById('loading-spinner').style.display = 'block';
        });
    </script>
</body>
</html>