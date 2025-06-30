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

// Check if appointment is pending
$stmt = $conn->prepare("SELECT userStatus, doctorStatus FROM appointment WHERE id = ? AND userId = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}
$stmt->bind_param("ii", $appointmentId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();
$stmt->close();

if (!$appointment || $appointment['userStatus'] != 1 || $appointment['doctorStatus'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Appointment is not pending or unauthorized']);
    exit;
}

// Cancel appointment
$stmt = $conn->prepare("UPDATE appointment SET userStatus = 0, updationDate = CURRENT_TIMESTAMP WHERE id = ? AND userId = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $appointmentId, $userId);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment canceled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel appointment: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>