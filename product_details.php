<?php
session_start();
require 'db_connect.php'; // Connects to PostgreSQL ($pdo)

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id === 0) {
    die("Invalid product ID.");
}

// --- 1. Fetch Product Details ---
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        die("Product not found.");
    }
} catch (PDOException $e) {
    die("Error fetching product: " . $e->getMessage());
}

// --- 2. Fetch Price History for the Graph ---
$price_history_data = [];
try {
    $hist_stmt = $pdo->prepare("
        SELECT store_name, price, timestamp 
        FROM price_history 
        WHERE product_id = ? 
        ORDER BY timestamp ASC
    ");
    $hist_stmt->execute([$product_id]);
    
    // Process data for Chart.js
    // We need to group data by store
    $stores_data = [];
    while ($row = $hist_stmt->fetch()) {
        $store = $row['store_name'];
        if (!isset($stores_data[$store])) {
            $stores_data[$store] = [];
        }
        $stores_data[$store][] = [
            'x' => $row['timestamp'], // 'x' for time
            'y' => (float)$row['price']  // 'y' for price
        ];
    }
    
    // Get unique labels (dates)
    $labels = array_unique(array_column($hist_stmt->fetchAll(), 'timestamp'));
    sort($labels);
    
    // Create Chart.js datasets
    $datasets = [];
    // Pre-defined colors for the graph lines
    $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
    $color_index = 0;
    
    foreach ($stores_data as $store_name => $data_points) {
        $color = $colors[$color_index % count($colors)]; // Cycle through colors
        $datasets[] = [
            'label' => $store_name,
            'data' => $data_points,
            'borderColor' => $color,
            'backgroundColor' => $color . '33', // Lighter fill
            'fill' => false,
            'tension' => 0.1
        ];
        $color_index++;
    }

    // Pass all data to JavaScript
    $chart_data_json = json_encode(['datasets' => $datasets]);

} catch (PDOException $e) {
    die("Error fetching price history: " . $e->getMessage());
}

// --- 3. Fetch Current Prices (Lowest price from each store) ---
$current_prices = [];
try {
    // This is a complex query. It finds the *latest* price for *each store* for this product.
    $price_stmt = $pdo->prepare("
        SELECT t1.store_name, t1.price, t1.product_url, t1.timestamp
        FROM price_history t1
        INNER JOIN (
            SELECT store_name, MAX(timestamp) as max_timestamp
            FROM price_history
            WHERE product_id = ?
            GROUP BY store_name
        ) t2 ON t1.store_name = t2.store_name AND t1.timestamp = t2.max_timestamp
        WHERE t1.product_id = ?
        ORDER BY t1.price ASC
    ");
    $price_stmt->execute([$product_id, $product_id]);
    $current_prices = $price_stmt->fetchAll();

} catch (PDOException $e) {
    die("Error fetching current prices: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Price Details</title>
    <link rel="stylesheet" href="panel_style.css">
    
    <!-- ** NEW: Include Chart.js library ** -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    
    <style>
        /* Add some styling for the details page */
        .product-details-container {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .product-details-image {
            flex: 1 1 300px;
        }
        .product-details-image img {
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .product-details-info {
            flex: 2 1 400px;
        }
        
        /* Store price list */
        .price-list {
            list-style: none;
            padding: 0;
            margin-top: 1.5rem;
        }
        .price-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            background: #fafafa;
        }
        .price-list-item .store-name {
            font-weight: bold;
            font-size: 1.2rem;
        }
        .price-list-item .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #16a34a; /* Green */
        }
        .price-list-item .btn-store {
            padding: 0.5rem 1rem;
            background: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .price-list-item .btn-store:hover {
            background: #4338ca;
        }
        
        /* Graph container */
        .chart-container {
            width: 100%;
            padding: 1.5rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <header class="panel-header">
        <h1><a href="user_panel.php">Price<span class="highlight">Comp</span></a></h1>
        <nav>
            <a href="profile.php">Profile</a>
            <a href="history.php">History</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <div class="product-details-container">
            <div class="product-details-image">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-details-info">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p><?php echo htmlspecialchars($product['description']); ?></p>

                <h3>Current Prices</h3>
                <?php if (empty($current_prices)): ?>
                    <p>No price information available for this product yet.</p>
                <?php else: ?>
                    <ul class="price-list">
                        <?php foreach ($current_prices as $price_entry): ?>
                        <li class="price-list-item">
                            <span class="store-name"><?php echo htmlspecialchars($price_entry['store_name']); ?></span>
                            <span class="price">$<?php echo htmlspecialchars($price_entry['price']); ?></span>
                            <a href="<?php echo htmlspecialchars($price_entry['product_url']); ?>" class="btn-store" target="_blank" rel="noopener noreferrer">Go to Store</a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- ** NEW: Price History Graph Section ** -->
        <section class="price-graph-section">
            <h3>Price History</h3>
            <div class="chart-container">
                <canvas id="priceChart"></canvas>
            </div>
        </section>
    </main>

    <script>
    // --- NEW: JavaScript to render the chart ---
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('priceChart').getContext('2d');
        
        // Get the data from PHP
        const chartData = <?php echo $chart_data_json; ?>;

        if (chartData.datasets.length > 0) {
            const priceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: chartData.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'day',
                                tooltipFormat: 'MMM d, yyyy'
                            },
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Price ($)'
                            },
                            beginAtZero: false // Start the graph near the lowest price
                        }
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        } else {
            // If no data, show a message
            document.querySelector('.chart-container').innerHTML = '<p>No price history data available to display a graph.</p>';
        }
    });
    </script>
</body>
</html>
