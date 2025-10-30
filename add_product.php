<?php
session_start();
// Security check: Ensure user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}

// Database connection
require __DIR__ . '/db_connect.php'; 

// === NEW: Include Composer's autoloader for Goutte ===
require __DIR__ . '/vendor/autoload.php';
use Goutte\Client;

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
    // === NEW: This is now just an array of URLs ===
    $url_array = $_POST['urls'] ?? [];

    // Basic validation
    if (empty($name) || empty($description) || empty($category) || empty($image_url) || empty($url_array)) {
        header("Location: add_product.php?error=All fields are required.");
        exit;
    }

    // === NEW: Initialize the Goutte scraping client ===
    $client = new Client();

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // 1. Insert into 'products' table
        $sql_product = "INSERT INTO products (name, description, category, image_url) VALUES (?, ?, ?, ?) RETURNING id";
        $stmt_product = $pdo->prepare($sql_product);
        $stmt_product->execute([$name, $description, $category, $image_url]);
        
        $product_id = $stmt_product->fetchColumn();

        if (!$product_id) {
            throw new Exception("Failed to create product.");
        }

        // 2. Insert into 'prices' and 'price_history' tables
        $sql_price = "INSERT INTO prices (product_id, store_name, price, url) VALUES (?, ?, ?, ?)";
        $stmt_price = $pdo->prepare($sql_price);

        $sql_history = "INSERT INTO price_history (product_id, store_name, price) VALUES (?, ?, ?)";
        $stmt_history = $pdo->prepare($sql_history);

        // === NEW: Loop over URLs, scrape, and insert ===
        foreach ($url_array as $url) {
            if (empty(trim($url))) continue;

            $store_name = "Unknown";
            $price_text = "0";

            // --- Scraper Logic ---
            // WARNING: This is fragile and will break when websites update their HTML.
            try {
                $crawler = $client->request('GET', $url);

                if (str_contains($url, 'amazon.in')) {
                    $store_name = 'Amazon';
                    // Try to find price in different common Amazon selectors
                    $price_node = $crawler->filter('span.a-price-whole')->first();
                    if ($price_node->count() === 0) {
                         $price_node = $crawler->filter('#priceblock_ourprice')->first();
                    }
                    if ($price_node->count() > 0) {
                        $price_text = $price_node->text();
                    }

                } elseif (str_contains($url, 'flipkart.com')) {
                    $store_name = 'Flipkart';
                    $price_node = $crawler->filter('div._30jeq3')->first();
                     if ($price_node->count() > 0) {
                        $price_text = $price_node->text();
                    }

                } elseif (str_contains($url, 'croma.com')) {
                    $store_name = 'Croma';
                    // Croma often loads price with JS, but we try the static price
                    $price_node = $crawler->filter('span.amount')->first();
                     if ($price_node->count() > 0) {
                        $price_text = $price_node->text();
                    }
                }
            } catch (Exception $scrape_error) {
                // Couldn't scrape, but we still save the URL
                // You can log $scrape_error->getMessage() if you want
            }
            // --- End Scraper Logic ---

            // Clean the price text (remove ₹, ,, etc.)
            $price = (float) preg_replace('/[^0-9.]/', '', $price_text);
            
            if ($price > 0) {
                // Add to prices table
                $stmt_price->execute([$product_id, $store_name, $price, $url]);
                // Add to price_history table
                $stmt_history->execute([$product_id, $store_name, $price]);
            }
        }

        // Commit the transaction
        $pdo->commit();

        // Redirect back with success message
        header("Location: add_product.php?success=Product added successfully! (Prices scraped)");
        exit;

    } catch (Exception $e) {
        // Roll back the transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error_message = urlencode($e->getMessage());
        header("Location: add_product.php?error=Database error: " . $error_message);
        exit;
    }

} else {
    // Not a POST request
    header("Location: add_product.php");
    exit;
}
?>

