<?php
// db_connect.php
// Updated to connect to a PostgreSQL database (like Neon)
// This code expects an environment variable 'DATABASE_URL'
// In Render.com, you will set this environment variable to the connection string from Neon.

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
    $port = $db_parts['port'];
    $dbname = ltrim($db_parts['path'], '/');
    $user = $db_parts['user'];
    $pass = $db_parts['pass'];

    // Create the DSN (Data Source Name) for PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$pass";

    // Set PDO options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Create a new PDO instance
    $pdo = new PDO($dsn);

} catch (PDOException $e) {
    // Handle connection error
    die("Database connection failed: " . $e->getMessage());
}

// You can now include this file and use the $pdo variable for your queries.
// Example: $stmt = $pdo->query("SELECT * FROM users");
?>
