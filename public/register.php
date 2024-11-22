<?php
session_start();

// Include necessary files (DB connection, header, footer)
include '../includes/db.php';
include '../includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the user is an admin registering
    $role = 'user'; // Default to 'user'
    if (isset($_POST['role']) && $_POST['role'] == 'admin') {
        $role = 'admin'; // Allow admin to register as admin
    }

    // Validate form data
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password before storing
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Use prepared statement to check if email already exists
        $check_email_query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $check_email_result = $stmt->get_result();
        
        if ($check_email_result->num_rows > 0) {
            $error = "Email already exists.";
        } else {
            // Use prepared statement to insert the new user into the database
            $insert_query = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header('Location: login.php');
                exit;
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!-- Registration Form -->
<main class="container mx-auto p-4">

    <h1 class="text-3xl font-semibold text-center mb-8">Register</h1>

    <?php if (isset($error)): ?>
        <div class="bg-red-600 text-white p-4 rounded mb-4">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-4 bg-white p-6 rounded shadow-md">
        <!-- Username -->
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" class="w-full border-gray-300 rounded-md" required>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" class="w-full border-gray-300 rounded-md" required>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="w-full border-gray-300 rounded-md" required>
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="w-full border-gray-300 rounded-md" required>
        </div>

        <!-- Hidden role for Admin Registration -->
        <input type="hidden" name="role" value="user">

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Register</button>
    </form>

    <div class="mt-4 text-center">
        <p>Already have an account? <a href="login.php" class="text-blue-600">Login here</a></p>
    </div>

</main>

<?php include '../includes/footer.php'; ?>
