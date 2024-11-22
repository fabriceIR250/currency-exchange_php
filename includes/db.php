<?php
// Include the config file to use database credentials
include '../config/config.php';

// Create a new connection using the constants from config.php
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
