<?php
// Start the session to access session variables
session_start();

// Include the database connection and the activity logger
// Use __DIR__ for reliable pathing
require __DIR__ . '/db_connect.php';
require __DIR__ . '/user_activity.php';

// Check if the user is logged in before trying to log their activity
if (isset($_SESSION['user_id']) && isset($pdo)) {
    // Call the correct function name: log_user_activity()
    log_user_activity($pdo, $_SESSION['user_id'], 'logout');
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: login.php");
exit;
?>

