<?php
session_start();
// 1. Include the new PostgreSQL connection
require 'db_connect.php'; // Provides $pdo

// 2. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 3. Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    try {
        if ($_POST['action'] == 'delete' && isset($_POST['history_id'])) {
            // --- Delete a single item ---
            $history_id = $_POST['history_id'];
            
            // 4. Use $pdo to delete
            // IMPORTANT: Also check user_id to ensure users can only delete their own history
            $sql = "DELETE FROM history WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$history_id, $user_id]);

            header("Location: history.php?success=Item removed");
            exit;
        } 
        elseif ($_POST['action'] == 'clear_all') {
            // --- Clear all history for this user ---
            
            // 4. Use $pdo to delete
            $sql = "DELETE FROM history WHERE user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id]);

            header("Location: history.php?success=History cleared");
            exit;
        }

    } catch (PDOException $e) {
        error_log("History action error: " . $e->getMessage());
        header("Location: history.php?error=An internal error occurred.");
        exit;
    }
}

// If not a valid POST request
header("Location: history.php");
exit;
?>
