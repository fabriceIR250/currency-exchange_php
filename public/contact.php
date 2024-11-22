<?php
session_start();

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'currency-app');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    
    // Validate form data
    if (empty($name) || empty($email) || empty($message)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        // Insert the message into the database
        $query = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$message')";
        if (mysqli_query($conn, $query)) {
            $success = "Your message has been sent successfully.";
        } else {
            $error = "Failed to send your message. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="bg-blue-600 text-white p-4 shadow-md">
    <h1 class="text-3xl font-bold text-center mb-6">Contact Us</h1>
    </div>

<main class="container mx-auto p-6">
    
    <?php if (isset($error)): ?>
        <div class="bg-red-600 text-white p-4 rounded mb-4">
            <p><?php echo $error; ?></p>
        </div>
    <?php elseif (isset($success)): ?>
        <div class="bg-green-600 text-white p-4 rounded mb-4">
            <p><?php echo $success; ?></p>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="bg-white p-6 rounded shadow-md space-y-4">
        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" class="w-full border-gray-300 rounded-md" required>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="w-full border-gray-300 rounded-md" required>
        </div>

        <!-- Message -->
        <div>
            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
            <textarea name="message" id="message" class="w-full border-gray-300 rounded-md" rows="5" required></textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Send Message</button>
    </form>
    <button type="submit" onclick="window.location.href='dashboard.php'" class="bg-blue-600 text-white m-20 px-6 py-2 rounded hover:bg-blue-700">back to 🏡</button>
</main>


</body>
</html>
