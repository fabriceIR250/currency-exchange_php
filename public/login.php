<?php
session_start();

// Include necessary files
include '../includes/db.php';
include '../includes/header.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on user role
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
        exit;
    } else {
        header('Location: dashboard.php');
        exit;
    }
}

// CSRF token generation if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } elseif ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token.";
    } else {
        // Query user based on email
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables and redirect
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "No user found with that email.";
        }
    }
}

?>

<!-- Login Form -->
<main class="container mx-auto p-4">

    <h1 class="text-3xl font-semibold text-center mb-8">Login</h1>

    <?php if (isset($error)): ?>
        <div class="bg-red-600 text-white p-4 rounded mb-4">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-4 bg-white p-6 rounded shadow-md">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

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

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Login</button>
    </form>

    <div class="mt-4 text-center">
        <p>Don't have an account? <a href="register.php" class="text-blue-600">Register here</a></p>
    </div>

</main>

<?php include '../includes/footer.php'; ?>
