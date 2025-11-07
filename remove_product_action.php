<?php
session_start();
require __DIR__ . '/db_connect.php';

// 1. Security Check: Must be admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}

// 2. Get Product ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_panel.php?error=Invalid product ID.");
    exit;
}
$product_id = (int)$_GET['id'];

try {
    // 3. Use a transaction to ensure all or nothing is deleted
    $pdo->beginTransaction();

    // Delete from price_history table
    $stmt_history = $pdo->prepare("DELETE FROM price_history WHERE product_id = ?");
    $stmt_history->execute([$product_id]);

    // Delete from prices table
    $stmt_prices = $pdo->prepare("DELETE FROM prices WHERE product_id = ?");
    $stmt_prices->execute([$product_id]);

    // Delete from products table
    $stmt_product = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt_product->execute([$product_id]);
    
    // 4. Commit the transaction
    $pdo->commit();

    header("Location: admin_panel.php?success=Product and associated prices removed successfully.");
    exit;

} catch (Exception $e) {
    // 5. Rollback if anything failed
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header("Location: admin_panel.php?error=Failed to remove product: " . $e->getMessage());
    exit;
}
?>