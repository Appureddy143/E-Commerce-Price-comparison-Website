<?php
session_start();
// Use __DIR__ to ensure the path is correct
require __DIR__ . '/db_connect.php'; 
require __DIR__ . '/user_activity.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data and sanitize it
    $name = trim(htmlspecialchars($_POST['name']));
    $gender = trim(htmlspecialchars($_POST['gender']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $profile_photo = trim(htmlspecialchars($_POST['profile_photo']));

    // --- Form Validations ---

    // 1. Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: register.php?error=Passwords do not match");
        exit;
    }

    // 2. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=Invalid email format");
        exit;
    }

    // 3. Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // --- Use the $pdo variable from db_connect.php ---

        // 4. Check if email already exists
        // Use $pdo, not $conn
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            // User already exists
            header("Location: register.php?error=Email already taken");
            exit;
        }

        // 5. Insert the new user into the database
        // Use $pdo, not $conn
        $stmt = $pdo->prepare("INSERT INTO users (name, gender, email, password, profile_photo) VALUES (?, ?, ?, ?, ?)");
        
        // Execute the statement with all parameters
        if ($stmt->execute([$name, $gender, $email, $hashed_password, $profile_photo])) {
            // Get the new user's ID
            $user_id = $pdo->lastInsertId();
            
            // Log this activity
            log_activity($pdo, $user_id, "User registered");

            // Redirect to login page with a success message
            header("Location: login.php?success=Registration successful! Please login.");
            exit;
        } else {
            header("Location: register.php?error=Registration failed. Please try again.");
            exit;
        }

    } catch (PDOException $e) {
        // Handle database errors
        // Log the error instead of showing it to the user
        error_log("Database error: " . $e->getMessage());
        header("Location: register.php?error=An internal error occurred. Please try again later.");
        exit;
    }

} else {
    // If someone tries to access this page directly without POST data
    header("Location: register.php");
    exit;
}
?>

