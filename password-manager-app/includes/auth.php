<?php
session_start();
include 'includes/conn.php';

// Define site sections that require authentication
$protected_sections = [
    'dashboard.php',
    'profile.php',
    'settings.php',
    'admin.php',
    'vault.php',
    'passwords/',  // Protect all files in this directory
    'admin/'       // Protect all files in this directory
];

// Define admin-only sections
$admin_sections = [
    'admin.php',
    'admin/'
];

// Check if the current page is in a protected section
function isProtectedPage() {
    global $protected_sections;
    $current_page = $_SERVER['PHP_SELF'];
    
    foreach ($protected_sections as $section) {
        if (strpos($current_page, $section) !== false) {
            return true;
        }
    }
    
    return false;
}

// Check if the current page is in admin-only section
function isAdminPage() {
    global $admin_sections;
    $current_page = $_SERVER['PHP_SELF'];
    
    foreach ($admin_sections as $section) {
        if (strpos($current_page, $section) !== false) {
            return true;
        }
    }
    
    return false;
}

// Validate remember token from cookie
function validateRememberToken() {
    global $conn;
    
    if (!isset($_COOKIE['remember_token']) || empty($_COOKIE['remember_token'])) {
        return false;
    }
    
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

// Check if user is logged in
function isLoggedIn() {
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        return true;
    }
    
    // Check for remember me cookie
    if (isset($_COOKIE['remember_token']) && !empty($_COOKIE['remember_token'])) {
        return validateRememberToken();
    }
    
    return false;
}

// Check if logged in user is admin
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    
    return $_SESSION['role'] === 'admin';
}

// Handle login page redirect if user is already logged in
if (basename($_SERVER['PHP_SELF']) === 'index.php' || basename($_SERVER['PHP_SELF']) === 'login.php') {
    if (isLoggedIn()) {
        // User is already logged in, redirect to dashboard
        header('Location: dashboard.php');
        exit();
    }
}

// Handle protected pages - redirect to login if not authenticated
if (isProtectedPage()) {
    if (!isLoggedIn()) {
        // Save the requested URL for redirection after login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        $_SESSION['error'] = "Please log in to access this page";
        header('Location: index.php');
        exit();
    }
    
    // Check for admin-only pages
    if (isAdminPage() && !isAdmin()) {
        $_SESSION['error'] = "You don't have permission to access this page";
        header('Location: dashboard.php');
        exit();
    }
    
    // Update last activity timestamp for session timeout management
    $_SESSION['last_activity'] = time();
    
    // Check for session timeout (30 minutes of inactivity)
    $timeout = 1800; // 30 minutes in seconds
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
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
        
        // Destroy session
        session_destroy();
        
        // Set error message in a new session
        session_start();
        $_SESSION['error'] = "Your session has expired due to inactivity. Please log in again.";
        header('Location: index.php');
        exit();
    }
}


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

    // Destroy session
    session_destroy();

    // Clear remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }

    // Redirect to login page
    header('Location: index.php');
    exit();
}

?>