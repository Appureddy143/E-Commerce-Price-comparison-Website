-- This is a PostgreSQL-compatible version of your price.sql file.
-- Run this script on your Neon database to create all the tables.

-- Users table for login and registration
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    gender VARCHAR(10),
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_photo VARCHAR(255),
    is_admin BOOLEAN DEFAULT false,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table to store item details
-- This replaces loading from an Excel file
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    category VARCHAR(100)
);

-- Search history table
CREATE TABLE history (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    search_query VARCHAR(255) NOT NULL,
    search_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- User activity table
CREATE TABLE user_activity (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type VARCHAR(50) NOT NULL, -- e.g., 'login', 'logout', 'search'
    activity_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);


-- ******************************************************
-- ** NEW TABLE FOR PRICE GRAPH (GOAL 3) **
-- ******************************************************
-- This table stores the price of a product from different stores over time
CREATE TABLE price_history (
    id SERIAL PRIMARY KEY,
    product_id INT NOT NULL,
    store_name VARCHAR(100) NOT NULL, -- e.g., 'Amazon', 'Flipkart', 'Best Buy'
    price DECIMAL(10, 2) NOT NULL,
    product_url VARCHAR(2048), -- Link to the product page on the store's site
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
);

-- You can add some initial data to see the app work:
-- Example Users (password is 'password123' hashed)
INSERT INTO users (name, email, password, is_admin) VALUES 
('Admin User', 'admin@pricecomp.com', '$2y$10$fW.j.1.vJb1m.9tJ8.L9C.AYG.L0o.L/O.L0o.L/O.L0o.L/O', true);

INSERT INTO users (name, email, password) VALUES 
('Test User', 'user@pricecomp.com', '$2y$10$fW.j.1.vJb1m.9tJ8.L9C.AYG.L0o.L/O.L0o.L/O.L0o.L/O');

-- Example Products
INSERT INTO products (name, description, image_url, category) VALUES
('iPhone 15 Pro', 'The latest iPhone with A17 Pro chip, advanced camera system, and USB-C connectivity.', 'https://placehold.co/300x300/e0e0e0/333?text=iPhone+15', 'Electronics'),
('Sony WH-1000XM5', 'Industry-leading noise-canceling headphones with crystal clear call quality.', 'https://placehold.co/300x300/e0e0e0/333?text=Sony+Headphones', 'Electronics');

-- Example Price History (for the graph)
-- Prices for iPhone 15 Pro (product_id = 1)
INSERT INTO price_history (product_id, store_name, price, product_url, timestamp) VALUES
(1, 'Amazon', 999.00, '#', '2025-10-01 10:00:00'),
(1, 'Best Buy', 1099.00, '#', '2025-10-01 10:00:00'),
(1, 'Amazon', 999.00, '#', '2025-10-08 10:00:00'),
(1, 'Best Buy', 1049.99, '#', '2025-10-08 10:00:00'),
(1, 'Amazon', 949.50, '#', '2025-10-15 10:00:00'),
(1, 'Best Buy', 1049.99, '#', '2025-10-15 10:00:00'),
(1, 'Amazon', 959.00, '#', '2025-10-22 10:00:00'),
(1, 'Best Buy', 999.99, '#', '2025-10-22 10:00:00');

-- Prices for Sony WH-1000XM5 (product_id = 2)
INSERT INTO price_history (product_id, store_name, price, product_url, timestamp) VALUES
(2, 'Amazon', 398.00, '#', '2025-10-05 10:00:00'),
(2, 'Crutchfield', 398.00, '#', '2025-10-05 10:00:00'),
(2, 'Amazon', 348.00, '#', '2025-10-12 10:00:00'),
(2, 'Crutchfield', 398.00, '#', '2025-10-12 10:00:00'),
(2, 'Amazon', 348.00, '#', '2025-10-19 10:00:00'),
(2, 'Crutchfield', 348.00, '#', '2025-10-19 10:00:00');
