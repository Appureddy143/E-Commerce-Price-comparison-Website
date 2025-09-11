<?php
session_start();
include 'db_connect.php';

// Security: Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Check if the form was submitted with a user_id to delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id_to_delete = (int)$_POST['user_id'];

    // --- Security Precaution ---
    // Prevent the admin from deleting their own account
    if ($user_id_to_delete === $_SESSION['id']) {
        $_SESSION['message'] = 'You cannot remove your own account.';
        $_SESSION['message_type'] = 'error';
        header('Location: admin_panel.php');
        exit;
    }

    // --- Database Deletion ---
    // Prepare a statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt) {
        // Bind the user ID to the placeholder
        $stmt->bind_param("i", $user_id_to_delete);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Check if any row was actually deleted
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = 'User has been successfully removed.';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Could not find the specified user to remove.';
                $_SESSION['message_type'] = 'error';
            }
        } else {
            // Handle execution errors
            $_SESSION['message'] = 'Error executing the delete command: ' . $stmt->error;
            $_SESSION['message_type'] = 'error';
        }
        
        // Close the statement
        $stmt->close();
    } else {
        // Handle preparation errors
        $_SESSION['message'] = 'Error preparing the delete command: ' . $conn->error;
        $_SESSION['message_type'] = 'error';
    }

} else {
    // Handle cases where the page is accessed directly or form is incomplete
    $_SESSION['message'] = 'Invalid request. Please use the remove button from the admin panel.';
    $_SESSION['message_type'] = 'error';
}

// Close the database connection
$conn->close();

// Always redirect back to the admin panel to show the result
header('Location: admin_panel.php');
exit;
?>

