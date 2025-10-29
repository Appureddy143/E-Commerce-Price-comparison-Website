<?php
session_start();
// 1. Include the new PostgreSQL connection
require 'db_connect.php'; // Provides $pdo

// 2. Check if user is logged in AND is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}

// 3. Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    $category = trim($_POST['category']);

    // 4. Validate data
    if (empty($name) || empty($category)) {
        header("Location: add_product.php?error=Product Name and Category are required");
        exit;
    }

    // Use placeholder if image URL is empty
    if (empty($image_url)) {
        $image_url = 'https://placehold.co/300x300/e0e0e0/333?text=' . urlencode(substr($name, 0, 10));
    }

    // 5. Insert into database using $pdo
    try {
        $sql = "INSERT INTO products (name, description, image_url, category) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        // Execute the statement
        if ($stmt->execute([$name, $description, $image_url, $category])) {
            // Success
            header("Location: add_product.php?success=Product added successfully!");
            exit;
        } else {
            // Fail
            header("Location: add_product.php?error=Failed to add product.");
            exit;
        }

    } catch (PDOException $e) {
        // Handle database errors
        error_log("Add product error: " . $e->getMessage()); // Log for developer
        header("Location: add_product.php?error=Database error: " . $e->getMessage());
        exit;
    }

} else {
    // Not a POST request
    header("Location: add_product.php");
    exit;
}
?>
