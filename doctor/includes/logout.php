<?php
if (!session_start()) {
    error_log("Failed to start session during logout on " . date('Y-m-d H:i:s'));
    header('Location: index.php?logout=error');
    exit;
}
?>