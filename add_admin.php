<?php
session_start();
// 1. Include the new PostgreSQL connection
require 'db_connect.php'; // Provides $pdo

// 2. Check if user is logged in AND is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}

$error = '';
$success = '';

// 3. Handle POST request to add a new admin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $profile_photo = 'avatar1.gif'; // Default admin avatar

    // 4. Validate
    if (empty($name) || empty($email) || empty($password) || empty($gender)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        // 5. Use $pdo to check if email exists
        try {
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt_check->execute([$email]);
            
            if ($stmt_check->fetchColumn() > 0) {
                $error = 'Email already registered.';
            } else {
                // 6. Create new admin user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $sql = "INSERT INTO users (name, gender, email, password, profile_photo, is_admin) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_insert = $pdo->prepare($sql);
                
                // Set is_admin to true
                if ($stmt_insert->execute([$name, $gender, $email, $hashed_password, $profile_photo, true])) {
                    $success = 'New admin user created successfully.';
                } else {
                    $error = 'Failed to create admin user.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
            error_log("Add admin error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin - Admin Panel</title>
    <link rel="stylesheet" href="panel_style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .gender-options {
            display: flex;
            gap: 1.5rem;
        }
        .btn-submit {
            display: inline-block;
            background-color: #16a34a;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }
        .btn-submit:hover {
            background-color: #15803d;
        }
    </style>
</head>
<body>
    <header class="panel-header">
        <h1><a href="admin_panel.php">Admin Panel - Add Admin</a></h1>
        <nav>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h3>Create a New Admin User</h3>
            
            <?php
            if ($error) {
                echo '<p class="error-message">' . htmlspecialchars($error) . '</p>';
            }
            if ($success) {
                echo '<p class="success-message">' . htmlspecialchars($success) . '</p>';
            }
            ?>

            <form action="add_admin.php" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                 <div class="form-group">
                    <label>Gender</label>
                    <div class="gender-options">
                        <input type="radio" id="male" name="gender" value="Male" required>
                        <label for="male">Male</label>
                        <input type="radio" id="female" name="gender" value="Female">
                        <label for="female">Female</label>
                    </div>
                </div>
                <button type="submit" class="btn-submit">Create Admin</button>
            </form>
        </div>
    </main>
</body>
</html>
