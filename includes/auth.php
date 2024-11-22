<?php
// Function to check if the user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if the logged-in user is an admin
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

// Function to redirect if the user is not logged in
function redirectIfNotLoggedIn($redirect_url = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect_url");
        exit;
    }
}

// Function to redirect if the user is logged in
function redirectIfLoggedIn($redirect_url = 'dashboard.php') {
    if (isLoggedIn()) {
        header("Location: $redirect_url");
        exit;
    }
}

// Function to log out
function logout() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

?>
