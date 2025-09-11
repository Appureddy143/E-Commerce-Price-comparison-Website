<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password, $role);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start session
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role == 'admin') {
                header("location: admin_panel.php");
            } else {
                header("location: user_panel.php");
            }
            exit;
        } else {
            // Incorrect password
            header("location: index.php?error=Invalid email or password");
            exit;
        }
    } else {
        // No user found
        header("location: index.php?error=Invalid email or password");
        exit;
    }

    $stmt->close();
}
$conn->close();
?>
