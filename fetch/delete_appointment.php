<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$appointmentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$userId = isset($_POST['userId']) ? (int)$_POST['userId'] : 0;

if ($appointmentId <= 0 || $userId <= 0 || $userId != $_SESSION['patient_id']) {
    echo json_encode(['success' => false, 'message' => 'Invalid appointment or user ID']);
    exit;
}

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM appointment WHERE id = ? AND userId = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $appointmentId, $userId);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Appointment not found or unauthorized']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete appointment: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>