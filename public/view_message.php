<?php
session_start();

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'currency-app');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get message ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: contact_messages.php?error=Invalid message ID');
    exit;
}

$message_id = intval($_GET['id']);

// Fetch the message details
$query = "SELECT * FROM contact_messages WHERE id = $message_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: contact_messages.php?error=Message not found');
    exit;
}

$message = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
</head>
<body>
<div class="bg-blue-600 text-white p-4 shadow-md">
<h1 class="text-3xl font-bold text-center mb-6">View Message</h1>
    </div>

<main class="container mx-auto p-6">
   

    <div class="bg-white p-6 rounded shadow-lg border border-gray-200">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($message['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
        <p><strong>Submitted At:</strong> <?php echo htmlspecialchars($message['created_at']); ?></p>
        <p><strong>Message:</strong></p>
        <p class="bg-gray-100 p-4 rounded"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
    </div>

    <div class="mt-4 text-center">
        <a href="cont_view.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">↩</a>
    </div>
</main>

</body>
</html>
