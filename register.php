<?php
// 1. Include the new PostgreSQL connection file
require 'db_connect.php'; // This now provides the $pdo object

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $name = trim($_POST['name']);
    $gender = $_POST['gender'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $profile_photo = $_POST['profile_photo']; // Filename from modal

    // --- Server-side validation ---
    if (empty($name) || empty($gender) || empty($email) || empty($password)) {
        header("Location: register.php?error=Please fill in all required fields");
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: register.php?error=Passwords do not match");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         header("Location: register.php?error=Invalid email format");
        exit;
    }

    // --- Check if email already exists ---
    try {
        // 2. Use $pdo and a prepared statement
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt_check->execute([$email]);
        
        if ($stmt_check->fetchColumn() > 0) {
            header("Location: register.php?error=Email already registered");
            exit;
        }

        // --- Create new user ---
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 3. Use $pdo to INSERT the new user
        $sql = "INSERT INTO users (name, gender, email, password, profile_photo) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql);
        
        // Execute the insertion
        if ($stmt_insert->execute([$name, $gender, $email, $hashed_password, $profile_photo])) {
            // Success
            header("Location: login.php?success=Registration successful. Please login.");
            exit;
        } else {
            // Failed to insert
            header("Location: register.php?error=Registration failed. Please try again.");
            exit;
        }

    } catch (PDOException $e) {
        // Handle database errors
        error_log("Registration error: " . $e->getMessage()); // Log for developer
        header("Location: register.php?error=An internal error occurred. Please try again.");
        exit;
    }

} else {
    // Not a POST request
    header("Location: register.php");
    exit;
}
?>
