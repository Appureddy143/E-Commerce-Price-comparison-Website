<?php
session_start();
// 1. Check if user is logged in AND is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}
// This file is just the form, so no database connection is needed here.
// The connection will be made in add_product_action.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Panel</title>
    <link rel="stylesheet" href="panel_style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; /* Important */
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .btn-submit {
            display: inline-block;
            background-color: #16a34a;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }
        .btn-submit:hover {
            background-color: #15803d;
        }
    </style>
</head>
<body>
    <header class="panel-header">
        <h1><a href="admin_panel.php">Admin Panel - Add Product</a></h1>
        <nav>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h3>Add a New Product to the Database</h3>
            
            <?php
            // Show error messages if any
            if (isset($_GET['error'])) {
                echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
            }
            if (isset($_GET['success'])) {
                echo '<p class="success-message">' . htmlspecialchars($_GET['success']) . '</p>';
            }
            ?>

            <form action="add_product_action.php" method="POST">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="text" id="image_url" name="image_url" placeholder="https://...">
                </div>
                 <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category" placeholder="e.g., Electronics">
                </div>
                <button type="submit" class="btn-submit">Add Product</button>
            </form>
        </div>
    </main>
</body>
</html>
