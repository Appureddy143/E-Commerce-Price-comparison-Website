<?php
session_start();
// 1. Include the new PostgreSQL connection file
require 'db_connect.php'; // This now provides the $pdo object
// 2. Include the updated activity logger
require 'user_activity.php'; // This now requires $pdo

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: user_panel.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: login.php?error=Email and password are required");
        exit;
    }

    try {
        // 3. Use $pdo and a prepared statement (?)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Check if user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Password is correct! Start the session.
            session_regenerate_id(true); // Security best practice
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['is_admin'] = (bool)$user['is_admin']; // Ensure it's a boolean

            // 4. Log activity using the new $pdo object
            log_activity($pdo, $user['id'], 'login');

            // Redirect based on admin status
            if ($_SESSION['is_admin']) {
                header("Location: admin_panel.php");
            } else {
                header("Location: user_panel.php");
            }
            exit;

        } else {
            // Invalid email or password
            header("Location: login.php?error=Invalid email or password");
            exit;
        }

    } catch (PDOException $e) {
        // Handle database errors
        error_log("Login error: " . $e->getMessage()); // Log for developer
        header("Location: login.php?error=An internal error occurred. Please try again later.");
        exit;
    }

} else {
    // Not a POST request
    header("Location: login.php");
    exit;
}
?>
