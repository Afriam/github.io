<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session to ensure we can destroy it
session_start();

// Log access for debugging
error_log("logoutury.php accessed at " . date('Y-m-d H:i:s') . " from IP: " . $_SERVER['REMOTE_ADDR']);

// Check if session variables exist before unsetting
if (isset($_SESSION)) {
    error_log("Session variables before unset: " . print_r($_SESSION, true));
} else {
    error_log("No session variables found.");
}

// Unset all session variables to ensure a clean slate
session_unset();

// Destroy the session completely
session_destroy();
error_log("Session destroyed at " . date('Y-m-d H:i:s'));

// Redirect to index.php with a logout confirmation
$redirect_url = '/new Doc Appointment/index.php?logout=success';
header('Location: ' . $redirect_url);
error_log("Redirecting to: " . $redirect_url);
exit;
?>