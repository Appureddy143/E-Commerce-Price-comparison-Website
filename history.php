<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];

// Fetch search history for the current user
$stmt = $conn->prepare("SELECT id, search_term, searched_at FROM history WHERE user_id = ? ORDER BY searched_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search History - PriceComp</title>
    <link rel="stylesheet" href="panel_style.css">
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
        <div class="panel-header">
            <h1>Your Search History</h1>
            <?php if ($result->num_rows > 0): ?>
            <a href="history_action.php?action=clear_all" class="btn-clear-all" onclick="return confirm('Are you sure you want to clear all your history?');">Clear All History</a>
            <?php endif; ?>
        </div>

        <div class="history-list">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="history-item">
                        <div class="history-info">
                            <a href="user_panel.php?search=<?php echo urlencode($row['search_term']); ?>" class="history-term"><?php echo htmlspecialchars($row['search_term']); ?></a>
                            <span class="history-date"><?php echo date('F j, Y, g:i a', strtotime($row['searched_at'])); ?></span>
                        </div>
                        <a href="history_action.php?action=delete&id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this item?');">&times;</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-history">You have no search history yet.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
