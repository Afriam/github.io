<?php
// Start the session to ensure we can destroy it
session_start();

// Unset all session variables to ensure a clean slate
session_unset();

// Destroy the session completely
session_destroy();

// Redirect to index.php with a logout confirmation
header('Location: index.php?logout=success');
exit;
?>
