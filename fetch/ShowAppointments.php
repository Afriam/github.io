<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

$userId = isset($_GET['userId']) ? (int)$_GET['userId'] : 0;

if ($userId <= 0 || !isset($_SESSION['patient_id']) || $_SESSION['patient_id'] != $userId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$stmt = $conn->prepare("
    SELECT a.id, a.doctorSpecialization, a.doctorId, a.consultancyFees, a.appointmentDate, a.appointmentTime,
           a.userStatus, a.doctorStatus, d.doctorName
    FROM appointment a
    LEFT JOIN doctors d ON a.doctorId = d.id
    WHERE a.userId = ?
    ORDER BY a.postingDate DESC
");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
    exit;
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = [
        'id' => $row['id'],
        'doctorName' => $row['doctorName'] ?? 'Unknown',
        'doctorSpecialization' => $row['doctorSpecialization'] ?? 'N/A',
        'consultancyFees' => $row['consultancyFees'] ?? 0,
        'appointmentDate' => $row['appointmentDate'] ?? 'N/A',
        'appointmentTime' => $row['appointmentTime'] ?? 'N/A',
        'userStatus' => $row['userStatus'] ?? 0,
        'doctorStatus' => $row['doctorStatus'] ?? 0,
        'doctorId' => $row['doctorId'] ?? 0
    ];
}

$stmt->close();
$conn->close();

echo json_encode($appointments);
?>