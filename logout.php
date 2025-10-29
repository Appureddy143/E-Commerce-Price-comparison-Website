<?php
session_start();

// 1. Include necessary files for logging
require 'db_connect.php'; // Provides $pdo
require 'user_activity.php'; // Provides log_activity()

// 2. Log the logout activity *before* destroying the session
if (isset($_SESSION['user_id'])) {
    log_activity($pdo, $_SESSION['user_id'], 'logout');
}

// 3. Unset all session variables
$_SESSION = array();

// 4. Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 5. Finally, destroy the session
session_destroy();

// 6. Redirect to login page
header("Location: login.php?success=You have been logged out.");
exit;
?>
