<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // --- Validations ---
    if ($password !== $confirm_password) {
        header("location: register.php?error=Passwords do not match");
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        header("location: register.php?error=Email address already registered");
        $stmt->close();
        exit;
    }
    $stmt->close();

    // --- Profile Photo Upload ---
    $photo_filename = 'default.png'; // Default photo
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["profile_photo"]["name"], PATHINFO_EXTENSION);
        // Create a unique filename to prevent overwriting
        $photo_filename = uniqid('user_', true) . '.' . $file_extension;
        $target_file = $target_dir . $photo_filename;

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
        if($check !== false) {
            move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file);
        } else {
            header("location: register.php?error=File is not an image.");
            exit;
        }
    }

    // --- Insert into Database ---
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, gender, email, password, profile_photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $gender, $email, $hashed_password, $photo_filename);

    if ($stmt->execute()) {
        // Redirect to login page after successful registration
        header("location: index.php?success=Registration successful. Please login.");
        exit;
    } else {
        header("location: register.php?error=Something went wrong. Please try again.");
        exit;
    }

    $stmt->close();
}
$conn->close();
?>
