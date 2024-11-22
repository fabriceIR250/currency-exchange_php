<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $fetch_query = "SELECT admin_screenshot FROM exchange_requests WHERE id = $id";
    $result = $conn->query($fetch_query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (!empty($row['admin_screenshot']) && file_exists('../' . $row['admin_screenshot'])) {
            unlink('../' . $row['admin_screenshot']);
        }

        $query = "UPDATE exchange_requests 
                  SET status = 'Rejected', admin_screenshot = NULL, finalized = TRUE 
                  WHERE id = $id";
        if ($conn->query($query) === TRUE) {
            header('Location: admin.php.?message=Request rejected successfully');
            exit;
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        header('Location: admin.php?error=Request not found');
        exit;
    }
} else {
    header('Location: admin.php?error=Invalid request ID');
    exit;
}
?>
