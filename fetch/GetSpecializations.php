<?php
header('Content-Type: application/json');
require_once '../config/database.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$stmt = $conn->prepare("SELECT DISTINCT specialization FROM doctorspecialization ORDER BY specialization");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

$specializations = [];
while ($row = $result->fetch_assoc()) {
    $specializations[] = ['specialization' => $row['specialization']];
}

$stmt->close();
$conn->close();

echo json_encode($specializations);
?>