<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

// Check if the user is logged in
if (!isset($_SESSION['patient_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Get record ID
$recordId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$patientId = (int)$_SESSION['patient_id'];

if ($recordId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid record ID']);
    exit;
}

// Check database connection
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Delete the record (only if it belongs to the logged-in patient)
$stmt = $conn->prepare("DELETE FROM tblmedicalhistory WHERE ID = ? AND PatientID = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ii", $recordId, $patientId);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Record deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Record not found or you do not have permission to delete it']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to delete record: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>