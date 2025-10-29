<?php
session_start();
// 1. Include the new PostgreSQL connection
require 'db_connect.php'; // Provides $pdo

// 2. Check if user is logged in AND is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php?error=Access denied");
    exit;
}

// 3. Check if it's a POST request and user_id is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    
    $user_id_to_delete = $_POST['user_id'];
    $admin_id = $_SESSION['user_id'];

    // 4. Admins cannot delete themselves
    if ($user_id_to_delete == $admin_id) {
        header("Location: admin_panel.php?error=You cannot remove yourself.");
        exit;
    }

    // 5. Use $pdo to delete the user
    try {
        // We might need to delete related data first (e.g., history) if
        // foreign keys are set to 'ON DELETE RESTRICT'
        // For Postgres with 'ON DELETE CASCADE', this is simpler.
        // Assuming cascade delete is set up in `price_postgres.sql`
        
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$user_id_to_delete])) {
            header("Location: admin_panel.php?success=User removed successfully.");
            exit;
        } else {
            header("Location: admin_panel.php?error=Failed to remove user.");
            exit;
        }

    } catch (PDOException $e) {
        error_log("Remove user error: " . $e->getMessage());
        header("Location: admin_panel.php?error=Database error: " . $e->getMessage());
        exit;
    }

} else {
    // Not a valid request
    header("Location: admin_panel.php");
    exit;
}
?>
