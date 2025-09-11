<?php
session_start();
include 'db_connect.php';
include 'api_handler.php'; // This includes our Excel reader functions

// Security check: If the user is not logged in, redirect them to the login page.
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Get the logged-in user's ID to save their search history
$user_id = $_SESSION['id'];

// Get the user's search query from the URL, if it exists.
// trim() removes any leading/trailing whitespace.
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

// If the user performed a search, save it to their history in the database.
if (!empty($search_term)) {
    // Using a prepared statement to prevent SQL injection attacks
    $stmt = $conn->prepare("INSERT INTO history (user_id, search_term) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $search_term);
    $stmt->execute();
    $stmt->close();
}

// Call our custom function to get the list of products from the Excel file.
// If there's a search term, the function will automatically filter the results.
$products = get_products_from_excel($search_term);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PriceComp</title>
    <link rel="stylesheet" href="panel_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <form class="search-bar" action="user_panel.php" method="GET">
                <input type="text" name="search" placeholder="Search products from our list..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button type="submit">Search</button>
            </form>
            <div class="nav-links">
                <a href="user_panel.php">Home</a>
                <a href="history.php">History</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>
    <main class="container">
        <div class="panel-header">
            <h1><?php echo !empty($search_term) ? 'Search Results' : 'Our Products'; ?></h1>
            <p><?php echo !empty($search_term) ? 'Showing results for "' . htmlspecialchars($search_term) . '"' : 'Browse products from our curated price list.'; ?></p>
        </div>
        <div class="product-grid">
            <?php if (!empty($products)): ?>
                <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" onerror="this.src='https://placehold.co/600x400/cccccc/ffffff?text=Image+Not+Found';">
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="price">From ₹<?php echo number_format($product['lowest_price']); ?></p>
                        <a href="product_details.php?name=<?php echo urlencode($product['title']); ?>" class="btn-view">Compare Prices</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <h2>No products found.</h2>
                    <p>Your search for "<?php echo htmlspecialchars($search_term); ?>" did not match any products in our list. <a href="user_panel.php">View all products</a>.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>
