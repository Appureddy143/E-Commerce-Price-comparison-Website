<?php
session_start();
// Security check: Ensure user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}

// Database connection
require __DIR__ . '/db_connect.php'; 

// Check if $pdo was created
if (!$pdo) {
    header("Location: add_product.php?error=Database connection failed.");
    exit;
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get product details
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $image_url = $_POST['image_url'] ?? '';
    $prices_array = $_POST['prices'] ?? [];

    // Basic validation
    if (empty($name) || empty($description) || empty($category) || empty($image_url) || empty($prices_array)) {
        header("Location: add_product.php?error=All fields are required.");
        exit;
    }

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // 1. Insert into 'products' table
        $sql_product = "INSERT INTO products (name, description, category, image_url) VALUES (?, ?, ?, ?) RETURNING id";
        $stmt_product = $pdo->prepare($sql_product);
        $stmt_product->execute([$name, $description, $category, $image_url]);
        
        // Get the ID of the newly inserted product
        $product_id = $stmt_product->fetchColumn();

        if (!$product_id) {
            throw new Exception("Failed to create product.");
        }

        // 2. Insert into 'prices' and 'price_history' tables
        $sql_price = "INSERT INTO prices (product_id, store_name, price, url) VALUES (?, ?, ?, ?)";
        $stmt_price = $pdo->prepare($sql_price);

        $sql_history = "INSERT INTO price_history (product_id, store_name, price) VALUES (?, ?, ?)";
        $stmt_history = $pdo->prepare($sql_history);

        foreach ($prices_array as $price_entry) {
            $store_name = $price_entry['store_name'];
            $price = (float) $price_entry['price'];
            $url = $price_entry['url'];

            if (!empty($store_name) && $price > 0 && !empty($url)) {
                // Add to prices table
                $stmt_price->execute([$product_id, $store_name, $price, $url]);
                // Add to price_history table
                $stmt_history->execute([$product_id, $store_name, $price]);
            }
        }

        // Commit the transaction
        $pdo->commit();

        // Redirect back with success message
        header("Location: add_product.php?success=Product added successfully!");
        exit;

    } catch (Exception $e) {
        // Roll back the transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // Redirect back with error message
        $error_message = urlencode($e->getMessage());
        header("Location: add_product.php?error=Database error: " . $error_message);
        exit;
    }

} else {
    // Not a POST request, redirect to the form page
    header("Location: add_product.php");
    exit;
}
?>
