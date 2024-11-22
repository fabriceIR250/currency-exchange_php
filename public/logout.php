<?php
// Start the session to access session variables
session_start();

// Destroy the session to log the user out
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect the user to the login page (or homepage)
header("Location: login.php");
exit; // Ensure no further code is executed
?>
