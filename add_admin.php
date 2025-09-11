<?php
// This is a one-time use script to create your admin account.
// Run it by navigating to http://localhost/pricecomp/add_admin.php in your browser.
// DELETE THIS FILE AFTER YOU HAVE RUN IT ONCE for security.

include 'db_connect.php';

$name = "Admin";
$gender = "N/A";
$email = "admin@example.com";
$password = "admin123";
$role = "admin";

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO users (name, gender, email, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $gender, $email, $hashed_password, $role);

// Execute the statement
if ($stmt->execute()) {
    echo "Admin user created successfully!<br>";
    echo "Email: " . $email . "<br>";
    echo "Password: " . $password . "<br>";
    echo "<strong>Please delete this file now.</strong>";
} else {
    echo "Error creating admin user: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
