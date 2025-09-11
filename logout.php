<?php
// This script handles the logout process.

session_start();

// Unset all of the session variables.
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to the main landing page after logging out.
header("location: index.php");
exit;
?>
