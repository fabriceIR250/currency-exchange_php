<?php
session_start();
include '../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get form input
$from_currency = $_POST['from_currency'];
$to_currency = $_POST['to_currency'];
$amount = $_POST['amount'];
$payment_method = $_POST['payment_method'];
$pay_number=$_POST['pay_number'];

// Handle file upload for payment screenshot
if (isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] == 0) {
    $file = $_FILES['payment_screenshot'];
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    if (in_array(strtolower($file_extension), $allowed_extensions)) {
        // Define upload directory
        $upload_dir = 'uploads/payment_screenshots/';
        $file_name = time() . '-' . basename($file['name']);
        $target_file = $upload_dir . $file_name; // Store the full path temporarily

        // Move the file to the target directory
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Insert request into the database with only the file name
            $query = "INSERT INTO exchange_requests (user_id, from_currency, to_currency, amount, payment_method, payment_screenshot,payment_number status) 
                      VALUES ('$user_id', '$from_currency', '$to_currency', '$amount', '$payment_method', '$file_name',$pay_number 'Pending')";
            if ($conn->query($query) === TRUE) {
                header('Location: dashboard.php');
                exit;
            } else {
                echo "Error: " . $conn->error;
            }
        } else {
            echo "Error uploading payment screenshot. Please try again.";
        }
    } else {
        echo "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
    }
} else {
    echo "Please upload a payment screenshot.";
}
?>
