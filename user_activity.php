<?php
/**
 * Logs a user's activity to the database.
 *
 * @param PDO $pdo The database connection object.
 * @param int $user_id The ID of the user performing the action.
 * @param string $action The action being performed (e.g., 'login', 'logout', 'search').
 * @param string|null $details Additional details about the action (e.g., search query).
 */
function log_user_activity($pdo, $user_id, $action, $details = null) {
    try {
        // Prepare the SQL statement to insert into the user_activity table
        // We use COALESCE for user_id to handle potential nulls, although user_id should ideally always be present for logged actions.
        $sql = "INSERT INTO user_activity (user_id, action, details) VALUES (COALESCE(?, (SELECT id FROM users WHERE email = 'guest@example.com')), ?, ?)";
        
        // Use prepared statements to prevent SQL injection
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters and execute
        // Note: We're passing $user_id directly. Ensure it's a valid integer.
        $stmt->execute([
            $user_id, 
            $action, 
            $details
        ]);

    } catch (PDOException $e) {
        // If logging fails, we don't want to crash the whole application.
        // Instead, we log the error to the server's error log for the admin to see.
        error_log("Failed to log user activity: " . $e->getMessage());
    }
}
// We remove the final ?> tag to prevent "headers already sent" errors.

