<?php
// db_connect.php
// Connects to a PostgreSQL database (like Neon)
// Expects an environment variable 'DATABASE_URL'.
// In Render, set DATABASE_URL to your Neon connection string.

$pdo = null;

try {
    // Get the database URL from the environment variable
    $db_url = getenv('DATABASE_URL');

    if ($db_url === false) {
        die("Error: DATABASE_URL environment variable is not set.");
    }

    // Parse the connection string
    $db_parts = parse_url($db_url);

    $host = $db_parts['host'];
    $port = $db_parts['port'] ?? 5432; // default to 5432 if not specified
    $dbname = ltrim($db_parts['path'], '/');
    $user = $db_parts['user'];
    $pass = $db_parts['pass'];

    // Build DSN (Data Source Name)
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

    // Set PDO options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Create PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (PDOException $e) {
    // Handle connection error
    die("Database connection failed: " . $e->getMessage());
}
?>
