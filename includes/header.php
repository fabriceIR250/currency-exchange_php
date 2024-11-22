<?php
// You can include this header file on any page where you need the header section
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Currency Exchange</title>
    
    <!-- TailwindCSS CDN (or you can use a local build if preferred) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Additional meta tags and external resources can go here -->
</head>
<body class="bg-gray-100 text-gray-900">

    <!-- Header Section -->
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- Logo or App Name -->
            <a href="index.php" class="text-2xl font-bold">
                Currency Exchange
            </a>
            
            <!-- Navigation Menu -->
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="index.php" class="hover:text-blue-300">Home</a></li>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="login.php" class="hover:text-blue-300">Login</a></li>
                    <li><a href="register.php" class="hover:text-blue-300">Register</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="dashboard.php" class="hover:text-blue-300">Dashboard</a></li>
                        <li><a href="../public/contact.php" class="hover:text-blue-300">Contact Us</a></li>
                        <li><a href="logout.php" class="hover:text-blue-300">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Page Content Starts Here -->
    <main class="container mx-auto p-4">
