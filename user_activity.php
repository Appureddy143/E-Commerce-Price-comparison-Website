<?php
session_start();
include 'db_connect.php';

// Security: Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// --- Fetch all users from the database, including their last login time ---
$users = [];
$sql = "SELECT id, name, email, role, created_at, last_login FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}
$conn->close();

// Get any feedback messages from the session (e.g., after removing a user)
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message'], $_SESSION['message_type']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login Activity - Admin Panel</title>
    <link rel="stylesheet" href="panel_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="nav-brand" href="admin_panel.php">Admin<span class="highlight">Panel</span></a>
            <div class="nav-links">
                <a href="admin_panel.php">Home</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>
    <main class="container">
        <div class="panel-header">
            <h1>User Login Activity</h1>
            <p>View and manage all registered users and their last login times.</p>
        </div>

        <!-- Display feedback messages -->
        <?php if ($message): ?>
            <div class="message <?php echo htmlspecialchars($message_type); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered On</th>
                        <th>Last Login</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                                <td><?php echo date("d M Y", strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php
                                    if ($user['last_login'] !== null) {
                                        echo date("d M Y, h:i A", strtotime($user['last_login']));
                                    } else {
                                        echo '<span style="color: #999;">Never</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <form action="remove_user_action.php" method="POST" onsubmit="return confirm('Are you sure you want to remove this user? This action cannot be undone.');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn-remove">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
