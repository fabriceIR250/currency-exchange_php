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

// Fetch messages from the database
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<main class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-center mb-6">Contact Messages</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">Name</th>
                    <th class="px-4 py-2 border">Email</th>
                    <th class="px-4 py-2 border">Message</th>
                    <th class="px-4 py-2 border">Submitted At</th>
                    <th class="px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="px-4 py-2 border"><?php echo nl2br(htmlspecialchars(substr($row['message'], 0, 50))); ?>...</td>
                        <td class="px-4 py-2 border"><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td class="px-4 py-2 border">
                            <a href="view_message.php?id=<?php echo $row['id']; ?>" 
                               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                               View Message
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center">No messages found.</p>
    <?php endif; ?>
    <div class="mt-4 text-center">
        <a href="admin.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Back to Admin_Dashboard</a>
    </div>
</main>

</body>
</html>
