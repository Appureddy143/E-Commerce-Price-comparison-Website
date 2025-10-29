<?php
session_start();
// Security check: Ensure user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}

// Include the panel header
$pageTitle = "Add New Product";
// We don't need db connection for the form itself, but header might
require __DIR__ . '/db_connect.php'; 
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
        <?php include 'admin_header.php'; // Includes the navigation ?>

        <main class="content">
            <div class="header">
                <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Product Details</h2>
                </div>
                <div class="card-body">
                    <?php
                    // Display error or success messages
                    if (isset($_GET['error'])) {
                        echo '<div class="message error-message">' . htmlspecialchars($_GET['error']) . '</div>';
                    }
                    if (isset($_GET['success'])) {
                        echo '<div class="message success-message">' . htmlspecialchars($_GET['success']) . '</div>';
                    }
                    ?>
                    
                    <form action="add_product_action.php" method="POST" id="add-product-form">
                        <!-- Product Details -->
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" id="category" name="category" placeholder="e.g., Electronics, Books">
                        </div>
                        <div class="form-group">
                            <label for="image_url">Image URL</label>
                            <input type="text" id="image_url" name="image_url" placeholder="https://example.com/image.jpg">
                        </div>

                        <hr class="form-divider">

                        <!-- Dynamic Price Entries -->
                        <h3>Product Prices</h3>
                        <div id="price-entries-container">
                            <!-- Initial Price Entry -->
                            <div class="price-entry">
                                <div class="form-group">
                                    <label>E-commerce Site</label>
                                    <input type="text" name="store_name[]" placeholder="e.g., Amazon, Flipkart" required>
                                </div>
                                <div class="form-group">
                                    <label>Price (₹)</label>
                                    <input type="number" name="price[]" step="0.01" placeholder="e.g., 499.99" required>
                                </div>
                                <div class="form-group">
                                    <label>Product URL</label>
                                    <input type="text" name="product_url[]" placeholder="Full product link" required>
                                </div>
                                <button type="button" class="btn btn-danger btn-small" onclick="removePriceEntry(this)">Remove</button>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-secondary" onclick="addPriceEntry()">Add Another Price</button>
                        
                        <hr class="form-divider">
                        
                        <button type="submit" class="btn">Save Product</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
    function addPriceEntry() {
        const container = document.getElementById('price-entries-container');
        const newEntry = document.createElement('div');
        newEntry.className = 'price-entry';
        newEntry.innerHTML = `
            <div class="form-group">
                <label>E-commerce Site</label>
                <input type="text" name="store_name[]" placeholder="e.g., Amazon, Flipkart" required>
            </div>
            <div class="form-group">
                <label>Price (₹)</label>
                <input type="number" name="price[]" step="0.01" placeholder="e.g., 499.99" required>
            </div>
            <div class="form-group">
                <label>Product URL</label>
                <input type="text" name="product_url[]" placeholder="Full product link" required>
            </div>
            <button type="button" class="btn btn-danger btn-small" onclick="removePriceEntry(this)">Remove</button>
        `;
        container.appendChild(newEntry);
    }

    function removePriceEntry(button) {
        // Don't remove the last entry
        const container = document.getElementById('price-entries-container');
        if (container.children.length > 1) {
            button.parentElement.remove();
        } else {
            alert("You must add at least one price entry.");
        }
    }
    </script>
</body>
</html>

