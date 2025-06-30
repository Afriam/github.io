CheckP<?php
session_start();
header('Content-Type: application/json');
require_once 'config/database.php';

if (!isset($_SESSION['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$patient_id = $_SESSION['patient_id'];
$stmt = $conn->prepare("SELECT status FROM patients WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'status' => $patient['status'] ?? '1']);
?>