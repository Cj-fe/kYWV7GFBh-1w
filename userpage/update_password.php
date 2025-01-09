<?php include '../includes/session.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <?php include 'includes/header.php'; ?>
    <?php
    require_once '../includes/firebaseRDB.php';
    require_once '../includes/config.php';

    $firebase = new firebaseRDB($databaseURL);

    $alumni_data = $firebase->retrieve("alumni");
    $alumni_data = json_decode($alumni_data, true);
    
    // Assuming you have the current user's ID stored in a session variable
    $current_user_id = $_SESSION['alumni_id'];
    $current_user = $alumni_data[$current_user_id] ?? null;

    if (!$current_user) {
        // Handle the case where the user is not found
        echo "User not found";
        exit;
    }

    function getValue($array, $key)
    {
        return isset($array[$key]) && !empty($array[$key]) ? $array[$key] : "N/A";
    }
    ?>

    <!-- Modal CSS -->
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
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

        .fa-check-circle {
            color: green;
        }

        .fa-times-circle {
            color: red;
        }
    </style>
    <!-- Font Awesome CSS -->
  
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="profile-content">
        <?php
        if (isset($_SESSION['update_message'])) {
            echo '<div class="alert alert-info">' . $_SESSION['update_message'] . '</div>';
            unset($_SESSION['update_message']);
        }
        ?>
        <form id="updateProfileForm" action="edit_pass_account.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $current_user_id; ?>">
            <div id="personal-info" class="profile-section">
                <h3>Username and Password</h3>
                <div class="post-col" style="width:100% !important">
                    <div class="post-container">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-user icon"></i> User Name
                                    </label>
                                    <div class="nk-int-st">
                                        <input type="text" id="username" name="email" class="form-control"
                                            placeholder="User Name"
                                            value="<?php echo htmlspecialchars(getValue($current_user, 'email')); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="new_password" class="form-label">
                                        <i class="fas fa-lock icon"></i> New Password
                                    </label>
                                    <div class="nk-int-st password-input-wrapper">
                                        <input type="password" id="new_password" name="new_password" class="form-control"
                                            placeholder="New Password" oninput="checkPasswordMatch()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock icon"></i> Confirm New Password
                                    </label>
                                    <div class="nk-int-st password-input-wrapper">
                                        <input type="password" id="confirm_password" name="confirm_password" class="form-control"
                                            placeholder="Confirm New Password" oninput="checkPasswordMatch()">
                                        <i id="password-match-icon" class="fas fa-times-circle" style="display: none;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" style="float:right" class="btn btn-primary">Update Changes</button>
            </div>
        </form>
    </div>

    <!-- Password Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Enter Your Current Password</h2>
            <form id="passwordForm">
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <!-- Modal JavaScript -->
    <script>
    document.getElementById('updateProfileForm').addEventListener('submit', function(event) {
        event.preventDefault();
        document.getElementById('passwordModal').style.display = 'block';
    });

    document.getElementsByClassName('close')[0].addEventListener('click', function() {
        document.getElementById('passwordModal').style.display = 'none';
    });

    document.getElementById('passwordForm').addEventListener('submit', function(event) {
        event.preventDefault();
        var currentPassword = document.getElementById('current_password').value;

        fetch('validate_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'current_password=' + encodeURIComponent(currentPassword)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('passwordModal').style.display = 'none';
                document.getElementById('updateProfileForm').submit();
            } else {
                alert(data.message || 'Password validation failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during password validation');
        });
    });

    function checkPasswordMatch() {
        var newPassword = document.getElementById('new_password').value;
        var confirmPassword = document.getElementById('confirm_password').value;
        var icon = document.getElementById('password-match-icon');

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
    </script>

    <?php include 'global_chatbox.php'?>
</body>
</html>