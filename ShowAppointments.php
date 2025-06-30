<?php
session_start();
include 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['patient_id']) || !isset($_SESSION['username'])) {
    echo json_encode([]);
    exit;
}

$patient_id = filter_input(INPUT_GET, 'patient_id', FILTER_VALIDATE_INT);
if ($patient_id !== $_SESSION['patient_id']) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT a.doctorSpecialization, a.doctorId, a.consultancyFees, a.appointmentDate, a.appointmentTime,
               a.userStatus, a.doctorStatus, d.doctorName
        FROM appointment a
        LEFT JOIN doctors d ON a.doctorId = d.id
        WHERE a.userId = ?
        ORDER BY a.appointmentDate DESC, a.appointmentTime DESC
    ");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $status = 'Pending';
        if ($row['userStatus'] == 1 && $row['doctorStatus'] == 1) {
            $status = 'Confirmed';
        } elseif ($row['userStatus'] == 0 || $row['doctorStatus'] == 0) {
            $status = 'Cancelled';
        }
        $appointments[] = [
            'doctorName' => $row['doctorName'] ?? 'TBA',
            'doctorSpecialization' => $row['doctorSpecialization'] ?? 'N/A',
            'consultancyFees' => $row['consultancyFees'] ?? '0.00',
            'appointmentDate' => $row['appointmentDate'] ?? 'TBA',
            'appointmentTime' => $row['appointmentTime'] ?? '',
            'status' => $status
        ];
    }
    $stmt->close();
    $conn->close();

    echo json_encode($appointments);
} catch (Exception $e) {
    error_log("ShowAppointments error: " . $e->getMessage());
    echo json_encode([]);
}
?>