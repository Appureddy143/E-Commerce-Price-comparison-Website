<?php
session_start();

// Security: Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Get any feedback messages from the session after adding a product
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message'], $_SESSION['message_type']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Panel</title>
    <link rel="stylesheet" href="panel_style.css?v=1.4">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* --- Add Product Form Styles (for add_product.php) --- */
.form-container {
    background-color: #ffffff;
    padding: 2rem 3rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    max-width: 700px;
    margin: 2rem auto; /* Center the form on the page */
}

.form-container h2 {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2rem;
    color: #333;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #555;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    font-family: 'Inter', sans-serif;
    transition: border-color 0.3s, box-shadow 0.3s;
    box-sizing: border-box; /* Ensures padding doesn't affect width */
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.btn-form {
    display: block;
    width: 100%;
    padding: 0.9rem;
    border: none;
    background-color: #4f46e5;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
    margin-top: 1rem;
}

.btn-form:hover {
    background-color: #4338ca;
}



</style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="nav-brand" href="admin_panel.php">Admin<span class="highlight">Panel</span></a>
            <div class="nav-links">
                <a href="admin_panel.php">Home</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container">
        <!-- Display any success or error messages -->
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Section to Add New Products -->
        <div class="form-container">
            <h2>Add New Product Price</h2>
            <p>This form will add a new row to your <strong>prices.xlsx</strong> file.</p>
            <form action="add_product_action.php" method="POST" class="add-product-form">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" placeholder="e.g., iPhone 15" required>
                </div>
                <div class="form-group">
                    <label for="store">Store</label>
                    <input type="text" id="store" name="store" placeholder="e.g., Amazon, Flipkart, Croma" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (INR)</label>
                    <input type="number" id="price" name="price" placeholder="e.g., 71999" required>
                </div>
                <div class="form-group">
                    <label for="url">Product URL</label>
                    <input type="url" id="url" name="url" placeholder="https://..." required>
                </div>
                 <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="url" id="image_url" name="image_url" placeholder="https://..." required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" required>
                </div>
                <button type="submit" class="btn-form">Add to Excel Sheet</button>
            </form>
        </div>
    </main>
</body>
</html>
