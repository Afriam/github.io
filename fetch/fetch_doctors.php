<?php
header('Content-Type: application/json');
require_once '../config/database.php';

// Check database connection
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Fetch all doctors (no status filter)
$stmt = $conn->prepare("SELECT id, doctorName, specialization, docEmail, contactno, address, docFees FROM doctors");
if (!$stmt) {
    echo json_encode(['error' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->execute();
$result = $stmt->get_result();
$doctors = [];

while ($row = $result->fetch_assoc()) {
    $doctors[] = [
        'id' => (int)$row['id'],
        'doctorName' => $row['doctorName'],
        'specialization' => $row['specialization'],
        'docEmail' => $row['docEmail'],
        'contactno' => $row['contactno'],
        'address' => $row['address'],
        'docFees' => (int)$row['docFees']
    ];
}

$stmt->close();
$conn->close();

echo json_encode($doctors);
?>