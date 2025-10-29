<?php
session_start();
// Use __DIR__ for a robust path to db_connect.php
require __DIR__ . '/db_connect.php'; 
require __DIR__ . '/user_activity.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: login.php?error=Email and password are required");
        exit;
    }

    try {
        // Prepare statement to find user by email
        // We also select the profile_photo here to add it to the session
        $sql = "SELECT id, name, email, password_hash, is_admin, profile_photo FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists AND verify the password
        if ($user && password_verify($password, $user['password_hash'])) {
            
            // Password is correct! Start the user session.
            session_regenerate_id(true); // Regenerate session ID for security
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // FIX: Add the profile_photo to the session
            // This will fix the 'Undefined array key' warning in user_panel.php
            $_SESSION['profile_photo'] = $user['profile_photo'];

            // Log the login activity
            log_user_activity($pdo, $user['id'], 'login');

            // Redirect to the user panel
            header("Location: user_panel.php");
            exit;

        } else {
            // Invalid email or password
            header("Location: login.php?error=Invalid email or password");
            exit;
        }

    } catch (PDOException $e) {
        // Handle database errors
        // Log the error instead of showing it to the user
        error_log("Login database error: " . $e->getMessage());
        
        // DEBUGGING: Send the specific database error message back to the login page.
        // This is not safe for production, but necessary to debug the problem.
        $errorMessage = "Database Error: " . $e->getMessage();
        header("Location: login.php?error=" . urlencode($errorMessage));
        exit;
    }

} else {
    // If not a POST request, redirect to login page
    header("Location: login.php");
    exit;
}
?>

