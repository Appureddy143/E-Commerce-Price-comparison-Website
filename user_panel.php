<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=You must be logged in to view this page");
    exit;
}

// We don't need the db connection here, as the search is handled by api_handler.php
// But we do need the session variables.

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
$profile_photo = $_SESSION['profile_photo'];
$is_admin = $_SESSION['is_admin'] ?? false; // Check if user is admin

// Determine avatar path
$avatar_path = "avatars/" . ($profile_photo ? $profile_photo : 'default.png');
// Add a fallback
$avatar_path_with_fallback = $avatar_path . "' onerror='this.onerror=null;this.src=\"https://placehold.co/100x100/EFEFEF/AAAAAA?text=User\";'";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Panel - PriceComp</title>
    <!-- Link to your panel_style.css -->
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Simple icon library for search icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="top-bar">
        <div class="logo">
            <a href="user_panel.php">Price<span class="highlight">Comp</span></a>
        </div>
        <nav class="main-nav">
            <a href="user_panel.php" class="active">Search</a>
            <a href="history.php">History</a>
            <!-- Show Admin Panel link only if user is admin -->
            <?php if ($is_admin): ?>
                <a href="admin_panel.php">Admin Panel</a>
            <?php endif; ?>
        </nav>
        <div class="user-menu">
            <a href="profile.php" class="profile-link">
                <img src="<?php echo $avatar_path_with_fallback; ?>" alt="Profile Photo" class="profile-pic">
                <span>Hello, <?php echo htmlspecialchars($name); ?></span>
            </a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <main class="container">
        <div class="search-container">
            <h1>Find the Best Price</h1>
            <p>Search for any product to compare prices across different stores.</p>
            <div class="search-box">
                <input type="text" id="search-input" placeholder="e.g., iPhone 15 Pro, Sony WH-1000XM5...">
                <button id="search-button"><i class="fas fa-search"></i> Search</button>
            </div>
        </div>

        <!-- This is where the results will be displayed -->
        <div id="results-container">
            <!-- Results will be injected here by JavaScript -->
            <!-- Loading spinner -->
            <div id="loading-spinner" style="display: none;">
                <div class="spinner"></div>
                <p>Searching for the best deals...</p>
            </div>
        </div>
    </main>

    <script>
        const searchInput = document.getElementById('search-input');
        const searchButton = document.getElementById('search-button');
        const resultsContainer = document.getElementById('results-container');
        const loadingSpinner = document.getElementById('loading-spinner');

        // Function to perform the search
        function performSearch() {
            const query = searchInput.value.trim();
            
            if (query === "") {
                resultsContainer.innerHTML = '<p class="error-message">Please enter a product name to search.</p>';
                return;
            }

            // Show loading spinner and clear old results
            loadingSpinner.style.display = 'block';
            resultsContainer.innerHTML = ''; // Clear previous results

            // Use fetch to call your api_handler.php
            fetch('api_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ query: query })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                loadingSpinner.style.display = 'none'; // Hide spinner
                
                if (data.error) {
                    // Handle errors from the API
                    resultsContainer.innerHTML = `<p class="error-message">${data.error}</p>`;
                } else if (data.length === 0) {
                    // Handle no results
                    resultsContainer.innerHTML = '<p class="info-message">No products found matching your search.</p>';
                } else {
                    // Build the results HTML
                    let html = '<h2>Search Results</h2>';
                    data.forEach(product => {
                        html += `
                            <div class="result-item">
                                <div class="result-image">
                                    <img src="${product.image_url}" alt="${product.name}" onerror="this.onerror=null;this.src='https://placehold.co/150x150/EFEFEF/AAAAAA?text=Product';">
                                </div>
                                <div class="result-details">
                                    <h3>${product.name}</h3>
                                    <p>${product.description.substring(0, 150)}...</p>
                                </div>
                                <div class="result-action">
                                    <span class="best-price">from $${parseFloat(product.best_price).toFixed(2)}</span>
                                    <!-- Link to the product_details.php with the product ID -->
                                    <a href="product_details.php?id=${product.id}" class="btn-view-details">View Details</a>
                                </div>
                            </div>
                        `;
                    });
                    resultsContainer.innerHTML = html;
                }
            })
            .catch(error => {
                loadingSpinner.style.display = 'none'; // Hide spinner
                console.error('Fetch error:', error);
                resultsContainer.innerHTML = '<p class="error-message">An error occurred while fetching results. Please try again.</p>';
            });
        }

        // Add event listeners
        searchButton.addEventListener('click', performSearch);
        
        // Allow pressing Enter to search
        searchInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                performSearch();
            }
        });

    </script>

</body>
</html>

