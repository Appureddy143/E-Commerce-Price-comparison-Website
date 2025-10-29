<?php
session_start();
// 1. Include the new PostgreSQL connection
require 'db_connect.php'; // Provides $pdo

// 2. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 3. Fetch search history for this user using $pdo
$user_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("
        SELECT id, search_query, search_time 
        FROM history 
        WHERE user_id = ? 
        ORDER BY search_time DESC
    ");
    $stmt->execute([$user_id]);
    $history_items = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error fetching history: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search History - PriceComp</title>
    <link rel="stylesheet" href="panel_style.css">
    <style>
        .history-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        .history-item:last-child {
            border-bottom: none;
        }
        .history-details {
            flex-grow: 1;
        }
        .history-query {
            font-weight: bold;
            font-size: 1.1em;
            color: #333;
        }
        .history-time {
            font-size: 0.9em;
            color: #777;
        }
        .btn-danger {
            background-color: #e53e3e;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            border: none;
            cursor: pointer;
        }
        .btn-danger:hover {
            background-color: #c53030;
        }
    </style>
</head>
<body>
    <header class="panel-header">
        <h1><a href="user_panel.php">Price<span class="highlight">Comp</span></a></h1>
        <nav>
            <a href="user_panel.php">Home</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <div class="history-container">
            <h3>Your Search History</h3>

            <?php if (isset($_GET['success'])): ?>
                <p class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></p>
            <?php endif; ?>
            
            <?php if (empty($history_items)): ?>
                <p>You have no search history.</p>
            <?php else: ?>
                <?php foreach ($history_items as $item): ?>
                    <div class="history-item">
                        <div class="history-details">
                            <div class="history-query"><?php echo htmlspecialchars($item['search_query']); ?></div>
                            <div class="history-time"><?php echo date('M d, Y - h:i A', strtotime($item['search_time'])); ?></div>
                        </div>
                        <!-- Form to delete a single history item -->
                        <form action="history_action.php" method="POST">
                            <input type="hidden" name="history_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="action" value="delete" class="btn-danger" title="Delete this item">&times;</button>
                        </form>
                    </div>
                <?php endforeach; ?>
                
                <hr style="margin-top: 2rem; border: 1px solid #eee;">
                
                <!-- Form to clear all history -->
                <form action="history_action.php" method="POST" onsubmit="return confirm('Are you sure you want to clear your entire search history?');" style="text-align: right; margin-top: 1rem;">
                    <button type="submit" name="action" value="clear_all" class="btn-danger">Clear All History</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
