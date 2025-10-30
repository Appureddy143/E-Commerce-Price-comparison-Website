<?php
session_start();
// Security check: Ensure user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}

$pageTitle = "Add New Product";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - PriceComp</title>
    <link rel="stylesheet" href="panel.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="panel-container">
        <?php include __DIR__ . '/admin_header.php'; // Includes the navigation ?>

        <main class="content">
            <div class="header">
                <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
            </div>

            <?php
            // Display error or success messages
            if (isset($_GET['error'])) {
                echo '<div class="message error-message">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            if (isset($_GET['success'])) {
                echo '<div class="message success-message">' . htmlspecialchars($_GET['success']) . '</div>';
            }
            ?>

            <div class="card">
                <div class="card-body">
                    <form action="add_product_action.php" method="POST" class="form-layout">
                        
                        <!-- Product Details -->
                        <fieldset>
                            <legend>Product Details</legend>
                            <div class="input-group">
                                <label for="name">Product Name</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="input-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="4" required></textarea>
                            </div>
                            <div class="input-group">
                                <label for="category">Category</label>
                                <input type="text" id="category" name="category" placeholder="e.g., Electronics, Books" required>
                            </div>
                            <div class="input-group">
                                <label for="image_url">Image URL</label>
                                <input type="url" id="image_url" name="image_url" placeholder="https://example.com/image.jpg" required>
                            </div>
                        </fieldset>

                        <!-- === NEW: Dynamic URL List === -->
                        <fieldset>
                            <legend>Product URLs</legend>
                            <p>Add the full product links from stores like Amazon, Flipkart, Croma, etc.</p>
                            <div id="url-entries">
                                <!-- Initial URL Entry -->
                                <div class="url-entry">
                                    <div class="input-group">
                                        <label>Product URL</label>
                                        <input type="url" name="urls[]" placeholder="https://amazon.com/product-link" required>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-url-btn" class="btn btn-secondary">Add Another URL</button>
                        </fieldset>

                        <button type="submit" class="btn">Add Product</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.getElementById('add-url-btn').addEventListener('click', function() {
            const container = document.getElementById('url-entries');
            const index = container.getElementsByClassName('url-entry').length;

            const newEntry = document.createElement('div');
            newEntry.className = 'url-entry';
            newEntry.innerHTML = `
                <hr>
                <div class="input-group">
                    <label>Product URL ${index + 1}</label>
                    <input type="url" name="urls[]" placeholder="https://flipkart.com/product-link" required>
                </div>
                <button type="button" class="btn btn-danger btn-small" onclick="removeUrlEntry(this)">Remove</button>
            `;
            container.appendChild(newEntry);
        });

        function removeUrlEntry(button) {
            button.parentElement.remove();
        }
    </script>
</body>
</html>