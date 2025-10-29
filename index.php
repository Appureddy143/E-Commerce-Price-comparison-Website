<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on admin status
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        header("Location: admin_panel.php");
    } else {
        header("Location: user_panel.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to PriceComp</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="welcome-box">
            <h1 class="title">Price<span class="highlight">Comp</span></h1>
            <p class="subtitle">Your ultimate price comparison tool.</p>
            <div class="welcome-actions">
                <a href="login.php" class="btn">Login</a>
                <a href="register.php" class="btn btn-secondary-outline">Register</a>
            </div>
        </div>
    </div>
</body>
</html>
