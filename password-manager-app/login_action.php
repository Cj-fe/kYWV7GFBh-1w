<?php
session_start();
include 'includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['master-password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    try {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM tbl_account WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        // Check if user exists
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Check if account is active
                if ($user['is_active'] == 1) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['access_level'] = $user['access_level'];
                    
                    // Set remember me cookie if selected (30 days)
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        
                        // Store token in database
                        $stmtToken = $conn->prepare("UPDATE tbl_account SET remember_token = :token WHERE id = :id");
                        $stmtToken->bindParam(':token', $token);
                        $stmtToken->bindParam(':id', $user['id']);
                        $stmtToken->execute();
                        
                        // Set cookie
                        setcookie('remember_token', $token, time() + (86400 * 30), "/"); // 30 days
                    }
                    
                    // Update last login timestamp
                    $stmtLogin = $conn->prepare("UPDATE tbl_account SET last_login = NOW() WHERE id = :id");
                    $stmtLogin->bindParam(':id', $user['id']);
                    $stmtLogin->execute();
                    
                    // Redirect to dashboard
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $_SESSION['error'] = "Your account is inactive. Please contact support.";
                }
            } else {
                $_SESSION['error'] = "Invalid email or password.";
            }
        } else {
            $_SESSION['error'] = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Login failed: " . $e->getMessage();
    }
    
    // Redirect back to login page if authentication fails
    header('Location: index.php');
    exit();
} else {
    // If someone tries to access this file directly without POST
    $_SESSION['error'] = "Invalid request method.";
    header('Location: index.php');
    exit();
}
?>