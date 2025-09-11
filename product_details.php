<?php
session_start();
include 'db_connect.php';
include 'api_handler.php'; // Excel reader functions

// Security check
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Get product name from URL
$product_name = isset($_GET['name']) ? trim($_GET['name']) : '';
if (empty($product_name)) {
    die("Error: No product name was specified in the URL.");
}

// Fetch product details
$product = get_product_details_from_excel($product_name);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product_name); ?> - PriceComp</title>
    <link rel="stylesheet" href="panel_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .product-detail-container {
            display: grid;
            grid-template-columns: 1fr; /* Single column on mobile */
            gap: 2rem;
            background-color: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-top: 2rem;
        }

        @media (min-width: 768px) {
            .product-detail-container {
                grid-template-columns: 1fr 1.5fr; /* Image column is smaller */
                padding: 3rem;
            }
        }

        .product-image-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .product-detail-image {
            width: 150px;
            height: 180px;
            object-fit: contain;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        .product-info-container h1 {
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }

        .product-description {
            font-size: 1rem;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .price-list-container h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #ee0c0cff;
            padding-bottom: 0.75rem;
        }

        .price-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #ffffffff;
        }

        .price-item:last-child {
            border-bottom: none;
        }

        .store-name {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .price {
            font-weight: 700;
            font-size: 1.2rem;
            color: #16a34a; /* Green */
            margin: 0 1rem;
        }

        .btn-store {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 6px;
            color: #ff0000;
            font-weight: 500;
            text-decoration: none;
            transition: opacity 0.3s;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-store:hover {
            opacity: 0.9;
        }

        /* Store button colors */
        .btn-amazon { background-color: #FF9900; }
        .btn-flipkart { background-color: #2874F0; }
        .btn-croma { background-color: #00A14B; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a class="nav-brand" href="user_panel.php">Price<span class="highlight">Comp</span></a>
            <div class="nav-links">
                <a href="user_panel.php">Home</a>
                <a href="history.php">History</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>
    <main class="container">
        <?php if (!empty($product)): ?>
            <div class="product-detail-container">
                <div class="product-image-container">
                    <img class="product-detail-image" 
                         src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>" 
                         onerror="this.src='https://placehold.co/150x150/cccccc/ffffff?text=No+Image';">
                </div>
                <div class="product-info-container">
                    <h1><?php echo htmlspecialchars($product['title']); ?></h1>
                    <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                    
                    <div class="price-list-container">
                        <h2>Compare Prices</h2>
                        <div class="price-list">
                            <?php foreach ($product['stores'] as $store): ?>
                                <div class="price-item">
                                    <span class="store-name"><?php echo htmlspecialchars($store['name']); ?></span>
                                    <span class="price">₹<?php echo number_format($store['price']); ?></span>
                                    <a href="<?php echo htmlspecialchars($store['url']); ?>" target="_blank" 
                                       class="btn-store <?php echo strtolower($store['name']); ?>">
                                       Go to Store
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="no-results">
                <h2>Product Not Found</h2>
                <p>We couldn't find any offers for "<?php echo htmlspecialchars($product_name); ?>" in our list. <a href="user_panel.php">Return to Home</a>.</p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
