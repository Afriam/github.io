<?php
require_once '../config/database.php';
header('Content-Type: application/json');
$doctorId = $_GET['doctor_id'] ?? 0;
$patientId = $_GET['patient_id'] ?? null;
try {
    $query = "SELECT a.id, a.appointmentDate, a.appointmentTime, a.consultancyFees, a.doctorStatus, a.userStatus, 
                     p.first_name, p.family_name, CONCAT(p.first_name, ' ', p.family_name) AS patient_name
              FROM appointment a 
              JOIN patients p ON a.userId = p.patient_id 
              WHERE a.doctorId = ?";
    if ($patientId) {
        $query .= " AND a.userId = ?";
    }
    $stmt = $conn->prepare($query);
    if ($patientId) {
        $stmt->bind_param("ii", $doctorId, $patientId);
    } else {
        $stmt->bind_param("i", $doctorId);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    echo json_encode($appointments);
    $stmt->close();
} catch (Exception $e) {
    error_log("Fetch appointments error: " . $e->getMessage());
    echo json_encode(['error' => 'Failed to fetch appointments']);
}
?>