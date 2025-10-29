<?php
// api_handler.php
// Updated to use PostgreSQL and query the 'products' table

header('Content-Type: application/json');
session_start();
require 'db_connect.php'; // This now connects to PostgreSQL via $pdo

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get the search term from the POST request
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';

if (empty($searchTerm)) {
    echo json_encode([]); // Return empty array if no search term
    exit;
}

// Save search to history
try {
    $hist_stmt = $pdo->prepare("INSERT INTO history (user_id, search_query) VALUES (?, ?)");
    $hist_stmt->execute([$_SESSION['user_id'], $searchTerm]);
} catch (PDOException $e) {
    // Log error, but don't stop the search
    error_log("Failed to save history: " . $e->getMessage());
}

// --- This is the new logic that replaces reading from Excel ---
// Search the 'products' table
try {
    // Use 'ILIKE' for case-insensitive search in PostgreSQL
    // Use 'LIKE' for MySQL
    $searchQuery = '%' . $searchTerm . '%';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name ILIKE ? OR description ILIKE ?");
    $stmt->execute([$searchQuery, $searchQuery]);
    $products = $stmt->fetchAll();

    // Return the found products as JSON
    echo json_encode($products);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database query failed: ' . $e->getMessage()]);
}
?>
