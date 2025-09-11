<?php
session_start();
include 'db_connect.php';

// Security check: If user is not logged in, redirect to login page.
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Prepare to fetch user data
$user_id = $_SESSION['id'];
$user = null;

// Fetch current user's data from the database
$stmt = $conn->prepare("SELECT name, gender, email, profile_photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}
$stmt->close();
$conn->close();

// Get any messages from the session (e.g., "Password changed successfully")
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message']);
unset($_SESSION['message_type']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - PriceComp</title>
    <link rel="stylesheet" href="panel_style.css?v=1.1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="nav-brand" href="user_panel.php">Price<span class="highlight">Comp</span></a>
            <div class="nav-links">
                <a href="user_panel.php">Home</a>
                <a href="history.php">History</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="profile-container">

            <?php if ($user): ?>
                <!-- THIS IS THE CORRECTED LINE -->
                <img src="avatars/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo" class="profile-photo">
                
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <p class="profile-detail"><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
                <p class="profile-detail"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <?php else: ?>
                <p class="message error">Could not load user profile.</p>
            <?php endif; ?>

            <div class="change-password-form">
                <h2>Change Password</h2>

                <!-- Display success or error messages here -->
                <?php if ($message): ?>
                    <div class="message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form action="change_password_action.php" method="POST">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_new_password">Confirm New Password</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                    </div>
                    <button type="submit" class="btn-form">Update Password</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>

