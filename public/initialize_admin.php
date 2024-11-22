<?php
// Include database connection
include '../includes/db.php';

// Default admin credentials
$username = "MARTIN EXCHANGER";
$email = "admin@martin.com";
$password = "admin123";
$role = "admin";

// Check if admin already exists
$check_admin_query = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($check_admin_query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert admin into the database
    $insert_admin_query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_admin_query);
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "Default admin user created successfully!";
    } else {
        echo "Error creating admin user: " . $stmt->error;
    }
} else {
    echo "Admin user already exists!";
}

$stmt->close();
$conn->close();
?>
