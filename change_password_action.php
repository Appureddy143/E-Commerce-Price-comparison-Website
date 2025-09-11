<?php
session_start();
include 'db_connect.php';

// Security check: If the user is not logged in, they can't change a password.
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Make sure the form was submitted using the POST method.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // First, check if the new password and confirm password fields match.
    if ($new_password !== $confirm_password) {
        $_SESSION['message'] = "New password and confirm password do not match.";
        $_SESSION['message_type'] = "error";
        header('Location: profile.php');
        exit;
    }

    // Now, get the user's current hashed password from the database.
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify if the 'current_password' submitted by the user matches the one in the database.
    // password_verify() is a secure PHP function for this exact purpose.
    if (password_verify($current_password, $user['password'])) {
        // If the current password is correct, we can update it.
        // First, we must hash the new password for secure storage.
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database.
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_password_hashed, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Password updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error: Could not update password.";
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();

    } else {
        // If the current password was incorrect.
        $_SESSION['message'] = "Incorrect current password.";
        $_SESSION['message_type'] = "error";
    }

    $conn->close();
    header('Location: profile.php');
    exit;

} else {
    // If someone tries to access this file directly without submitting the form.
    header('Location: profile.php');
    exit;
}
?>
