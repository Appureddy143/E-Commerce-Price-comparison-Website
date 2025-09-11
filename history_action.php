<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['id'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle deleting a single history item
if ($action == 'delete' && isset($_GET['id'])) {
    $history_id = $_GET['id'];
    // Prepare statement to ensure user can only delete their own history
    $stmt = $conn->prepare("DELETE FROM history WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $history_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Handle clearing all history for the user
if ($action == 'clear_all') {
    $stmt = $conn->prepare("DELETE FROM history WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Redirect back to the history page
header("Location: history.php");
exit;
?>
