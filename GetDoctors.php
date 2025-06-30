<?php
session_start();
include 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['patient_id']) || !isset($_SESSION['username'])) {
    echo json_encode([]);
    exit;
}

$specialization = filter_input(INPUT_GET, 'specialization', FILTER_SANITIZE_STRING);
if (!$specialization) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT id, doctorName, docFees FROM doctors WHERE specialization = ? ORDER BY doctorName");
    $stmt->bind_param("s", $specialization);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        // Ensure docFees is numeric
        $fees = is_numeric($row['docFees']) ? (int)$row['docFees'] : 0;
        $doctors[] = [
            'id' => $row['id'],
            'doctorName' => $row['doctorName'],
            'consultancyFees' => $fees // Changed from docFees to consultancyFees to match frontend
        ];
    }
    $stmt->close();
    $conn->close();

    echo json_encode($doctors);
} catch (Exception $e) {
    error_log("GetDoctors error: " . $e->getMessage());
    echo json_encode([]);
}
?>