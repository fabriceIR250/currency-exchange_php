<?php
session_start();

header('Content-Type: application/json');

// Check if there’s a new message
if (isset($_SESSION['new_message_notification']) && $_SESSION['new_message_notification'] === true) {
    // Reset the notification flag
    unset($_SESSION['new_message_notification']);
    echo json_encode(['newMessage' => true]);
} else {
    echo json_encode(['newMessage' => false]);
}
?>


