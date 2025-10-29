<?php
session_start();
// 1. Include the new PostgreSQL connection
require 'db_connect.php'; // Provides $pdo

// 2. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 3. Fetch user data using $pdo
try {
    $stmt = $pdo->prepare("SELECT name, email, gender, profile_photo FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        // This shouldn't happen if session is set, but good to check
        session_destroy();
        header("Location: login.php?error=User not found");
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Determine the full avatar path
$avatar_path = 'avatars/' . htmlspecialchars($user['profile_photo']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - PriceComp</title>
    <link rel="stylesheet" href="panel_style.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid #ddd;
            object-fit: cover;
        }
        .profile-header h2 {
            margin: 1rem 0 0.5rem;
        }
        .profile-info p {
            font-size: 1.1em;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        .profile-info p strong {
            color: #333;
            margin-right: 10px;
        }
        
        .change-password-form {
            margin-top: 2rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn-submit {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <header class="panel-header">
        <h1><a href="<?php echo $_SESSION['is_admin'] ? 'admin_panel.php' : 'user_panel.php'; ?>">Price<span class="highlight">Comp</span></a></h1>
        <nav>
            <?php if ($_SESSION['is_admin']): ?>
                <a href="admin_panel.php">Admin Panel</a>
            <?php else: ?>
                <a href="user_panel.php">Home</a>
                <a href="history.php">History</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <div class="profile-container">
            <div class="profile-header">
                <img src="<?php echo $avatar_path; ?>" alt="Profile Photo">
                <h2><?php echo htmlspecialchars($user['name']); ?></h2>
            </div>

            <div class="profile-info">
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($user['gender']); ?></p>
            </div>

            <hr style="border: 1px solid #eee; margin: 2rem 0;">

            <h3>Change Password</h3>
            
            <?php
            // Show messages
            if (isset($_GET['error'])) {
                echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
            }
            if (isset($_GET['success'])) {
                echo '<p class="success-message">' . htmlspecialchars($_GET['success']) . '</p>';
            }
            ?>

            <form action="change_password_action.php" method="POST" class="change-password-form">
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
                <button type="submit" class="btn-submit">Change Password</button>
            </form>
        </div>
    </main>
</body>
</html>
