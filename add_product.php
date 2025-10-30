<?php
session_start();

// =======================================
// 1. SECURITY CHECK: Admin authentication
// =======================================
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}

// =======================================
// 2. INCLUDE HEADER (safe include)
// =======================================
$header_path = __DIR__ . '/admin_header.php';
if (file_exists($header_path)) {
    include $header_path;
} else {
    echo "<!-- Warning: admin_header.php not found. Skipping header include. -->";
}

// =======================================
// 3. DATABASE CONNECTION
// =======================================
require __DIR__ . '/db_connect.php'; 

if (!$pdo) {
    header("Location: add_product.php?error=Database connection failed.");
    exit;
}

// =======================================
// 4. PROCESS FORM SUBMISSION
// =======================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect form inputs safely
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $image_url   = trim($_POST['image_url'] ?? '');
    $prices_array = $_POST['prices'] ?? [];

    // =============================
    // 4A. BASIC VALIDATION
    // =============================
    if (empty($name) || empty($description) || empty($category) || empty($image_url) || empty($prices_array)) {
        header("Location: add_product.php?error=All fields are required.");
        exit;
    }

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // =============================
        // 4B. INSERT INTO 'products'
        // =============================
        $sql_product = "INSERT INTO products (name, description, category, image_url)
                        VALUES (?, ?, ?, ?)
                        RETURNING id";
        $stmt_product = $pdo->prepare($sql_product);
        $stmt_product->execute([$name, $description, $category, $image_url]);

        $product_id = $stmt_product->fetchColumn();

        if (!$product_id) {
            throw new Exception("Failed to create product record.");
        }

        // =============================
        // 4C. INSERT PRICES + HISTORY
        // =============================
        $sql_price = "INSERT INTO prices (product_id, store_name, price, url)
                      VALUES (?, ?, ?, ?)";
        $stmt_price = $pdo->prepare($sql_price);

        $sql_history = "INSERT INTO price_history (product_id, store_name, price)
                        VALUES (?, ?, ?)";
        $stmt_history = $pdo->prepare($sql_history);

        foreach ($prices_array as $entry) {
            $store_name = trim($entry['store_name'] ?? '');
            $price      = (float) ($entry['price'] ?? 0);
            $url        = trim($entry['url'] ?? '');

            if (!empty($store_name) && $price > 0 && !empty($url)) {
                $stmt_price->execute([$product_id, $store_name, $price, $url]);
                $stmt_history->execute([$product_id, $store_name, $price]);
            }
        }

        // Commit changes
        $pdo->commit();

        header("Location: add_product.php?success=Product added successfully!");
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error_message = urlencode($e->getMessage());
        header("Location: add_product.php?error=Database error: " . $error_message);
        exit;
    }

} else {
    // =======================================
    // 5. DISPLAY ADD PRODUCT FORM (optional)
    // =======================================
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Add Product</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <h1>Add New Product</h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <form method="POST" action="add_product.php">
            <label>Product Name:</label><br>
            <input type="text" name="name" required><br><br>

            <label>Description:</label><br>
            <textarea name="description" required></textarea><br><br>

            <label>Category:</label><br>
            <input type="text" name="category" required><br><br>

            <label>Image URL:</label><br>
            <input type="text" name="image_url" required><br><br>

            <h3>Prices</h3>
            <!-- Example for multiple store prices -->
            <div>
                <label>Store 1 Name:</label><br>
                <input type="text" name="prices[0][store_name]" required><br>
                <label>Store 1 Price:</label><br>
                <input type="number" step="0.01" name="prices[0][price]" required><br>
                <label>Store 1 URL:</label><br>
                <input type="text" name="prices[0][url]" required><br><br>
            </div>

            <button type="submit">Add Product</button>
        </form>
    </body>
    </html>
    <?php
}
?>
