<?php
session_start();
// 1. Include the new PostgreSQL connection
require 'db_connect.php'; // Provides $pdo

// 2. Check if user is logged in AND is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=You do not have permission to access this page");
    exit;
}

// 3. Fetch data using $pdo (PostgreSQL)
try {
    // Fetch all users
    $users_stmt = $pdo->query("SELECT id, name, email, is_admin FROM users ORDER BY id ASC");
    $users = $users_stmt->fetchAll();

    // Fetch all products
    $products_stmt = $pdo->query("SELECT id, name, category FROM products ORDER BY id ASC");
    $products = $products_stmt->fetchAll();

    // Fetch search history with user names (using JOIN)
    $history_stmt = $pdo->query("
        SELECT h.id, u.name, h.search_query, h.search_time 
        FROM history h
        JOIN users u ON h.user_id = u.id
        ORDER BY h.search_time DESC
        LIMIT 50
    ");
    $history = $history_stmt->fetchAll();

} catch (PDOException $e) {
    // Handle database errors
    die("Error fetching admin data: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - PriceComp</title>
    <link rel="stylesheet" href="panel_style.css">
    <style>
        /* Add some styles for the admin tables */
        .admin-section {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .admin-section h3 {
            margin-top: 0;
            border-bottom: 2px solid #eee;
            padding-bottom: 0.5rem;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .admin-table th, .admin-table td {
            padding: 0.8rem;
            border: 1px solid #ddd;
            text-align: left;
        }
        .admin-table th {
            background-color: #f9f9f9;
        }
        .btn-danger {
            background-color: #e53e3e;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
        }
        .btn-danger:hover {
            background-color: #c53030;
        }
        .btn-add {
            display: inline-block;
            background-color: #16a34a;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .btn-add:hover {
            background-color: #15803d;
        }
    </style>
</head>
<body>
    <header class="panel-header">
        <h1><a href="admin_panel.php">Admin Panel - Price<span class="highlight">Comp</span></a></h1>
        <nav>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <!-- User Management -->
        <section class="admin-section">
            <h3>User Management</h3>
            <!-- Add Admin button can be added here if needed -->
            <!-- <a href="add_admin.php" class="btn-add">Add New Admin</a> -->
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                        <td>
                            <!-- Form for user removal -->
                            <form action="remove_user_action.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this user?');">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Product Management -->
        <section class="admin-section">
            <h3>Product Management</h3>
            <a href="add_product.php" class="btn-add">Add New Product</a>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['id']); ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td>
                            <!-- Add edit/delete forms here if needed -->
                            <a href="product_details.php?id=<?php echo $product['id']; ?>" style="margin-right: 5px;">View</a>
                            <!-- <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a> -->
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Search History -->
        <section class="admin-section">
            <h3>Search History</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Search Query</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['search_query']); ?></td>
                        <td><?php echo htmlspecialchars($item['search_time']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

    </main>
</body>
</html>
