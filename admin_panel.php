<?php
session_start();
require __DIR__ . '/db_connect.php';

// =======================================
// 1. SECURITY CHECK: Admin authentication
// =======================================
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}

// =A. Get Total Users
$total_users = 0;
try {
    $stmt_users = $pdo->query("SELECT COUNT(*) FROM users");
    $total_users = $stmt_users->fetchColumn();
} catch (Exception $e) {
    // Fail silently, dashboard can still load
}

// =B. Get Total Products
$total_products = 0;
try {
    $stmt_products = $pdo->query("SELECT COUNT(*) FROM products");
    $total_products = $stmt_products->fetchColumn();
} catch (Exception $e) {
    // Fail silently
}

// =C. Get Products List
$products = [];
try {
    $stmt_product_list = $pdo->query("SELECT id, name, category FROM products ORDER BY id DESC LIMIT 10");
    $products = $stmt_product_list->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $db_error = $e->getMessage();
}

// =D. Get Users List
$users = [];
try {
    $stmt_user_list = $pdo->query("SELECT id, name, email FROM users ORDER BY id DESC LIMIT 10");
    $users = $stmt_user_list->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $db_error = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="panel_style.css">
</head>
<body>

    <div class="panel-container">
        
        <?php 
        // =======================================
        // 2. INCLUDE SIDEBAR (safe include)
        // =======================================
        $header_path = __DIR__ . '/admin_header.php';
        if (file_exists($header_path)) {
            include $header_path;
        } else {
            echo "<p class='error-message'>Error: admin_header.php not found. Sidebar is missing.</p>";
        }
        ?>

        <!-- ======================================= -->
        <!-- 3. MAIN CONTENT -->
        <!-- ======================================= -->
        <main class="content">
            <div class="header">
                <h1>Admin Dashboard</h1>
                <span>Welcome back, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?>!</span>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="message success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="message error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <?php if (isset($db_error)): ?>
                <div class="message error-message">Database error: <?php echo htmlspecialchars($db_error); ?></div>
            <?php endif; ?>

            <!-- Dashboard Stats Cards -->
            <div class="card-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>Total Users</h3>
                    </div>
                    <div class="card-body">
                        <p class="stat-number"><?php echo $total_users; ?></p>
                        <p class="stat-label">Registered users</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3>Total Products</h3>
                    </div>
                    <div class="card-body">
                        <p class="stat-number"><?php echo $total_products; ?></p>
                        <p class="stat-label">Products in database</p>
                    </div>
                </div>
            </div>

            <!-- Recent Products Table -->
            <div class="card">
                <div class="card-header">
                    <h3>Recent Products</h3>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Action</th>
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
                                        <td>
                                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-small btn-secondary">View</a>
                                            <!-- ADDED THIS BUTTON -->
                                            <a href="remove_product_action.php?id=<?php echo $product['id']; ?>" 
                                               class="btn btn-small btn-danger" 
                                               onclick="return confirm('Are you sure you want to remove this product and all its prices?');">
                                               Remove
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Users Table -->
            <div class="card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h3>Recent Users</h3>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                             <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="4">No users found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <a href="remove_user_action.php?id=<?php echo $user['id']; ?>" 
                                               class="btn btn-small btn-danger" 
                                               onclick="return confirm('Are you sure you want to remove this user?');">
                                               Remove
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>