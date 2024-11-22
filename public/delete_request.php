<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not an admin
    exit;
}

// Get the request ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Delete the record from the database
    $query = "DELETE FROM exchange_requests WHERE id = $id";
    if ($conn->query($query) === TRUE) {
        header('Location: admin_dashboard.php'); // Redirect back to dashboard
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    header('Location: admin_dashboard.php'); // Redirect if no ID is provided
    exit;
}
?>
