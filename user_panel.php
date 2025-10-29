<?php
// ... existing code ...
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Panel - PriceComp</title>
    <link rel="stylesheet" href="panel_style.css">
</head>
<body>
<!-- ... existing code ... -->
        <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
        <p>Your ultimate price comparison tool.</p>
    </header>

    <main>
        <section class="search-section">
            <h3>Find a Product</h3>
            <form id="search-form">
                <input type="text" id="search-input" name="search" placeholder="Search for products...">
                <button type="submit">Search</button>
            </form>
        </section>

        <section class="results-section">
            <h3>Results</h3>
            <div id="results-container">
                <p>Your search results will appear here.</p>
            </div>
        </section>
    </main>

    <script>
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const searchTerm = document.getElementById('search-input').value;
        const resultsContainer = document.getElementById('results-container');
        
        resultsContainer.innerHTML = '<p>Loading...</p>';

        fetch('api_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'search=' + encodeURIComponent(searchTerm)
        })
        .then(response => response.json())
        .then(data => {
            resultsContainer.innerHTML = ''; // Clear loading
            
            if (data.error) {
                resultsContainer.innerHTML = `<p class="error">${data.error}</p>`;
                return;
            }

            if (data.length === 0) {
                resultsContainer.innerHTML = '<p>No products found matching your search.</p>';
                return;
            }

            // Create product cards
            data.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';

                // ** MODIFICATION: Link to product_details.php with the product ID **
                productCard.innerHTML = `
                    <img src="${product.image_url || 'https://placehold.co/100x100/e0e0e0/333?text=No+Image'}" alt="${product.name}">
                    <div class="product-info">
                        <h4>${product.name}</h4>
                        <p>${product.description || 'No description available.'}</p>
                        <a href="product_details.php?id=${product.id}" class="btn-details">View Details</a>
                    </div>
                `;
                resultsContainer.appendChild(productCard);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            resultsContainer.innerHTML = '<p class="error">An error occurred while searching.</p>';
        });
    });
    </script>

</body>
</html>
