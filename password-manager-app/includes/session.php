<?php
session_start();
include 'includes/conn.php';

// Function to check if user is logged in
function isLoggedIn() {
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        return true;
    }
    
    // Check for remember me cookie
    if (isset($_COOKIE['remember_token']) && !empty($_COOKIE['remember_token'])) {
        validateRememberToken();
        
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            return true;
        }
    }
    
    return false;
}

// Function to validate remember token from cookie
function validateRememberToken() {
    global $conn;
    
    try {
        $token = $_COOKIE['remember_token'];
        
        $stmt = $conn->prepare("SELECT * FROM tbl_account WHERE remember_token = :token LIMIT 1");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['access_level'] = $user['access_level'];
            
            // Update last login timestamp
            $stmtLogin = $conn->prepare("UPDATE tbl_account SET last_login = NOW() WHERE id = :id");
            $stmtLogin->bindParam(':id', $user['id']);
            $stmtLogin->execute();
            
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        // Clear cookie if error occurs
        setcookie('remember_token', '', time() - 3600, '/');
        return false;
    }
}

// Function to get current user's data
function getCurrentUser() {
    global $conn;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_account WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return null;
    } catch (PDOException $e) {
        return null;
    }
}

// Function to check if user has specific access level
function hasAccess($requiredLevel) {
    if (!isLoggedIn()) {
        return false;
    }
    
    return $_SESSION['access_level'] >= $requiredLevel;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Please log in to access this page";
        header('Location: index.php');
        exit();
    }
}

// Check if user has admin role
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    
    return $_SESSION['role'] === 'admin';
}

// Require admin role, redirect otherwise
function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['error'] = "You don't have permission to access this page";
        header('Location: dashboard.php');
        exit();
    }
}

// Function to log out user
function logoutUser() {
    // Clear session variables
    $_SESSION = array();
    
    // Clear session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Clear remember me cookie
    setcookie('remember_token', '', time() - 3600, '/');
    
    // Destroy session
    session_destroy();
}

// Track user activity to update session timeout
function updateActivity() {
    $_SESSION['last_activity'] = time();
}

// Check for session timeout (30 minutes of inactivity)
function checkSessionTimeout() {
    $timeout = 1800; // 30 minutes in seconds
    
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        logoutUser();
        $_SESSION['error'] = "Your session has expired due to inactivity. Please log in again.";
        header('Location: index.php');
        exit();
    }
    
    updateActivity();
}

// Initialize activity tracker
if (isLoggedIn()) {
    checkSessionTimeout();
}
?>