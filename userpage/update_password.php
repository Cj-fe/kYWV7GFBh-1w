<?php 
include '../includes/session.php';
require_once '../includes/firebaseRDB.php';
require_once '../includes/config.php';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$firebase = new firebaseRDB($databaseURL);

// Get current user data
$current_user_id = $_SESSION['alumni_id'] ?? null;
if ($current_user_id) {
    $alumni_data = $firebase->retrieve("alumni");
    $alumni_data = json_decode($alumni_data, true);
    $current_user = $alumni_data[$current_user_id] ?? null;
}

if (!$current_user) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php include 'includes/header.php'; ?>
    
    <!-- Custom CSS -->
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
            border-radius: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .password-input-wrapper {
            position: relative;
        }

        .password-input-wrapper .fas {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .fa-check-circle { color: green; }
        .fa-times-circle { color: red; }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="profile-content">
        <?php
        if (isset($_SESSION['update_message'])) {
            $messageClass = strpos($_SESSION['update_message'], 'success') !== false ? 'alert-success' : 'alert-danger';
            echo '<div class="alert ' . $messageClass . '">' . $_SESSION['update_message'] . '</div>';
            unset($_SESSION['update_message']);
        }
        ?>

        <form id="updateProfileForm" action="edit_pass_account.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="user_id" value="<?php echo $current_user_id; ?>">
            
            <div id="personal-info" class="profile-section">
                <h3>Username and Password</h3>
                <div class="post-col" style="width:100% !important">
                    <div class="post-container">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-user icon"></i> Username
                                    </label>
                                    <div class="nk-int-st">
                                        <input type="text" id="username" name="email" class="form-control" 
                                               value="<?php echo htmlspecialchars($current_user['email'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="new_password" class="form-label">
                                        <i class="fas fa-lock icon"></i> New Password
                                    </label>
                                    <div class="nk-int-st password-input-wrapper">
                                        <input type="password" id="new_password" name="new_password" 
                                               class="form-control" placeholder="New Password" 
                                               oninput="checkPasswordMatch()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock icon"></i> Confirm Password
                                    </label>
                                    <div class="nk-int-st password-input-wrapper">
                                        <input type="password" id="confirm_password" name="confirm_password" 
                                               class="form-control" placeholder="Confirm Password" 
                                               oninput="checkPasswordMatch()">
                                        <i id="password-match-icon" class="fas" style="display: none;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="float:right">Update Changes</button>
            </div>
        </form>
    </div>

    <!-- Password Verification Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Verify Current Password</h2>
            <form id="passwordForm">
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" 
                           class="form-control" required>
                </div>
                <div id="error-message" class="alert alert-danger" style="display: none;"></div>
                <button type="submit" class="btn btn-primary">Verify Password</button>
            </form>
        </div>
    </div>

    <script>
    function checkPasswordMatch() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const icon = document.getElementById('password-match-icon');

        if (newPassword === '' && confirmPassword === '') {
            icon.style.display = 'none';
        } else if (newPassword === confirmPassword) {
            icon.className = 'fas fa-check-circle';
            icon.style.display = 'inline';
        } else {
            icon.className = 'fas fa-times-circle';
            icon.style.display = 'inline';
        }
    }

    document.getElementById('updateProfileForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (newPassword !== confirmPassword) {
            alert('New passwords do not match!');
            return;
        }

        document.getElementById('passwordModal').style.display = 'block';
    });

    document.querySelector('.close').addEventListener('click', function() {
        document.getElementById('passwordModal').style.display = 'none';
    });

    document.getElementById('passwordForm').addEventListener('submit', function(event) {
        event.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        const currentPassword = document.getElementById('current_password').value;
        const errorDisplay = document.getElementById('error-message');

        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
        errorDisplay.style.display = 'none';

        fetch('validate_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'current_password=' + encodeURIComponent(currentPassword),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('passwordModal').style.display = 'none';
                document.getElementById('updateProfileForm').submit();
            } else {
                errorDisplay.textContent = data.message || 'Password verification failed';
                errorDisplay.style.display = 'block';
                document.getElementById('current_password').value = '';
                document.getElementById('current_password').focus();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorDisplay.textContent = 'An error occurred during verification. Please try again.';
            errorDisplay.style.display = 'block';
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Verify Password';
        });
    });

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('passwordModal')) {
            document.getElementById('passwordModal').style.display = 'none';
        }
    }
    </script>

    <?php include 'global_chatbox.php'?>
</body>
</html>