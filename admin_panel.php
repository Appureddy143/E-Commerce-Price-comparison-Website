<?php
session_start();
// Security check: Ensure user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}

require __DIR__ . '/db_connect.php';

$pageTitle = "Admin Dashboard";
$userCount = 0;
$productCount = 0;
$users = [];
$products = [];

try {
    // Get stats
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

    // Get all users (except the current admin)
    $stmt_users = $pdo->prepare("SELECT id, name, email, is_admin, created_at FROM users WHERE id != ? ORDER BY created_at DESC");
    $stmt_users->execute([$_SESSION['user_id']]);
    $users = $stmt_users->fetchAll(PDO::FETCH_ASSOC);

    // Get all products
    $stmt_products = $pdo->query("SELECT id, name, category, created_at FROM products ORDER BY created_at DESC");
    $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - PriceComp</title>
    <link rel="stylesheet" href="panel_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="panel-container">
        <?php include __DIR__ . '/admin_header.php'; // Includes the navigation ?>

        <main class="content">
            <div class="header">
                <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
            </div>

            <?php
            // Display error or success messages
            if (isset($_GET['error'])) {
                echo '<div class="message error-message">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            if (isset($_GET['success'])) {
                echo '<div class="message success-message">' . htmlspecialchars($_GET['success']) . '</div>';
            }
            if (isset($error)) {
                echo '<div class="message error-message">' . $error . '</div>';
            }
            ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="card stat-card">
                    <h2>Total Users</h2>
                    <p><?php echo $userCount; ?></p>
                </div>
                <div class="card stat-card">
                    <h2>Total Products</h2>
                    <p><?php echo $productCount; ?></p>
                </div>
            </div>

            <!-- Products Table -->
            <div class="card">
                <div class="card-header">
                    <h2>All Products (<?php echo $productCount; ?>)</h2>
                    <a href="add_product.php" class="btn btn-small">Add Product</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="4">No products found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['id']); ?></td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($product['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-header">
                    <h2>All Users</h2>
                    <a href="add_admin.php" class="btn btn-small">Add Admin</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Date Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="6">No other users found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <?php if ($user['is_admin']): ?>
                                                <span class="badge admin-badge">Admin</span>
                                            <?php else: ?>
                                                <span class="badge user-badge">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <?php if (!$user['is_admin']): // Only show remove for non-admins ?>
                                                <a href="remove_user_action.php?id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-danger btn-small" 
                                                   onclick="return confirm('Are you sure you want to remove this user?');">Remove</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>
</html>

