<?php
// user_activity.php
// Updated to accept the $pdo object instead of $conn

/**
 * Logs user activity to the database.
 *
 * @param PDO $pdo The PDO database connection object.
 * @param int $user_id The ID of the user.
 * @param string $activity_type The type of activity (e.g., 'login', 'logout', 'search').
 */
function log_activity($pdo, $user_id, $activity_type) {
    try {
        // Use a prepared statement
        $sql = "INSERT INTO user_activity (user_id, activity_type) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $activity_type]);
        
    } catch (PDOException $e) {
        // Log error to a file or error console
        // Don't kill the script, just log the failure.
        error_log("Failed to log activity: " . $e->getMessage());
    }
}
?>
