<?php
header('Content-Type: application/json');
require_once '../config/database.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$specialization = isset($_GET['specialization']) ? trim($_GET['specialization']) : '';

if (empty($specialization)) {
    echo json_encode(['success' => false, 'message' => 'Specialization is required']);
    exit;
}

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$stmt = $conn->prepare("SELECT id, doctorName, docFees FROM doctors WHERE specialization = ? ORDER BY doctorName");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("s", $specialization);
$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = [
        'id' => $row['id'],
        'doctorName' => $row['doctorName'],
        'docFees' => $row['docFees']
    ];
}

$stmt->close();
$conn->close();

echo json_encode($doctors);
?>