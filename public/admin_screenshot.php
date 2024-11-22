<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not an admin
    exit;
}

// Include necessary files
include '../includes/db.php';

// Check if the form is submitted and a file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['admin_screenshot'])) {
    $request_id = $_POST['request_id']; // Get the request ID from the hidden input field
    $file = $_FILES['admin_screenshot'];

    // Validate file upload
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Define the allowed file types and size
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_file_size = 5 * 1024 * 1024; // 5MB max file size

        // Check if the file type is allowed
        if (in_array($file['type'], $allowed_types)) {
            // Check file size
            if ($file['size'] <= $max_file_size) {
                // Generate a unique file name and set the upload directory
                $upload_dir = '../public/uploads/payment_screenshots/'; // Adjust path for public directory
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
                }
                $unique_filename = uniqid('screenshot_') . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                $upload_path = $upload_dir . $unique_filename;

                // Move the uploaded file to the target directory
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Update the exchange request record in the database with the file path
                    $query = "UPDATE exchange_requests SET admin_screenshot = ? WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("si", $unique_filename, $request_id);

                    if ($stmt->execute()) {
                        // Redirect back to the admin dashboard with a success message
                        header('Location: admin.php?success=1');
                    } else {
                        // Database update failed
                        echo "Error updating the request. Please try again later.";
                    }
                } else {
                    echo "Error uploading the file. Please try again.";
                }
            } else {
                echo "File size exceeds the maximum allowed size of 5MB.";
            }
        } else {
            echo "Invalid file type. Only JPG, PNG, and GIF files are allowed.";
        }
    } else {
        echo "Error uploading file. Error code: " . $file['error'];
    }
} else {
    echo "No file was uploaded.";
}

?>
