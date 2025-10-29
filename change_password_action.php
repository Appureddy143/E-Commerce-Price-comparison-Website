<?php
session_start();
// 1. Include the new PostgreSQL connection
require 'db_connect.php'; // Provides $pdo

// 2. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // 3. Validate input
    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        header("Location: profile.php?error=Please fill in all password fields");
        exit;
    }

    if ($new_password !== $confirm_new_password) {
        header("Location: profile.php?error=New passwords do not match");
        exit;
    }

    try {
        // 4. Fetch current password from DB using $pdo
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user) {
             header("Location: profile.php?error=User not found");
             exit;
        }

        // 5. Verify current password
        if (password_verify($current_password, $user['password'])) {
            
            // 6. Hash and update new password
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($update_stmt->execute([$hashed_new_password, $user_id])) {
                header("Location: profile.php?success=Password changed successfully");
                exit;
            } else {
                header("Location: profile.php?error=Failed to update password");
                exit;
            }

        } else {
            // Current password was incorrect
            header("Location: profile.php?error=Incorrect current password");
            exit;
        }

    } catch (PDOException $e) {
        error_log("Password change error: " . $e->getMessage());
        header("Location: profile.php?error=An internal database error occurred.");
        exit;
    }

} else {
    header("Location: profile.php");
    exit;
}
?>
