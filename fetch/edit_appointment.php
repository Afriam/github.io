<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

if (!isset($_SESSION['patient_id'])) {
    error_log('EditAppointment: Session patient_id not set');
    echo json_encode(['success' => false, 'message' => 'Please log in']);
    exit;
}

$appointmentId = isset($_POST['appointmentId']) ? (int)$_POST['appointmentId'] : 0;
$userId = isset($_POST['userId']) ? (int)$_POST['userId'] : 0;
$doctorSpecialization = isset($_POST['DoctorSpecialization']) ? trim($_POST['DoctorSpecialization']) : '';
$doctorId = isset($_POST['DoctorId']) ? (int)$_POST['DoctorId'] : 0;
$consultancyFees = isset($_POST['ConsultancyFees']) ? (int)$_POST['ConsultancyFees'] : 0;
$appointmentDate = isset($_POST['AppointmentDate']) ? trim($_POST['AppointmentDate']) : '';
$appointmentTime = isset($_POST['AppointmentTime']) ? trim($_POST['AppointmentTime']) : '';

$errors = [];
if ($appointmentId <= 0) {
    $errors[] = "Invalid appointment ID";
}
if ($userId <= 0 || $userId != $_SESSION['patient_id']) {
    $errors[] = "Unauthorized user";
}
if (empty($doctorSpecialization)) {
    $errors[] = "Select a specialization";
}
if ($doctorId <= 0) {
    $errors[] = "Select a doctor";
}
if ($consultancyFees < 0) {
    $errors[] = "Invalid consultancy fees";
}
if (empty($appointmentDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointmentDate)) {
    $errors[] = "Select a valid date";
}
if (empty($appointmentTime) || !preg_match('/^\d{2}:\d{2}$/', $appointmentTime)) {
    $errors[] = "Select a valid time";
}
if (strtotime($appointmentDate) < strtotime(date('Y-m-d'))) {
    $errors[] = "Date cannot be in the past";
}

if (!empty($errors)) {
    error_log('EditAppointment validation errors: ' . implode(", ", $errors));
    echo json_encode(['success' => false, 'message' => implode(", ", $errors)]);
    exit;
}

if (!$conn) {
    error_log('EditAppointment: Database connection failed');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check if appointment exists and is pending
$stmt = $conn->prepare("SELECT userStatus, doctorStatus FROM appointment WHERE id = ? AND userId = ?");
if (!$stmt) {
    error_log('EditAppointment: Query preparation failed for appointment check');
    echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
    exit;
}
$stmt->bind_param("ii", $appointmentId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();
$stmt->close();

if (!$appointment) {
    error_log('EditAppointment: Appointment not found for id=' . $appointmentId . ', userId=' . $userId);
    echo json_encode(['success' => false, 'message' => 'Appointment not found']);
    exit;
}
if ($appointment['userStatus'] != 1 || $appointment['doctorStatus'] != 1) {
    error_log('EditAppointment: Attempt to edit non-pending appointment id=' . $appointmentId);
    echo json_encode(['success' => false, 'message' => 'Only pending appointments can be edited']);
    exit;
}

// Validate doctor
$stmt = $conn->prepare("SELECT id, docFees FROM doctors WHERE id = ? AND specialization = ?");
if (!$stmt) {
    error_log('EditAppointment: Query preparation failed for doctor check');
    echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
    exit;
}
$stmt->bind_param("is", $doctorId, $doctorSpecialization);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    error_log('EditAppointment: Invalid doctor id=' . $doctorId . ', specialization=' . $doctorSpecialization);
    echo json_encode(['success' => false, 'message' => 'Invalid doctor or specialization']);
    exit;
}
$doctor = $result->fetch_assoc();
$stmt->close();

// Update appointment
$stmt = $conn->prepare("
    UPDATE appointment
    SET doctorSpecialization = ?, doctorId = ?, consultancyFees = ?, appointmentDate = ?, appointmentTime = ?, updationDate = CURRENT_TIMESTAMP
    WHERE id = ? AND userId = ?
");
if (!$stmt) {
    error_log('EditAppointment: Query preparation failed for update');
    echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
    exit;
}

$stmt->bind_param("siiissi", $doctorSpecialization, $doctorId, $consultancyFees, $appointmentDate, $appointmentTime, $appointmentId, $userId);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Appointment updated successfully']);
    } else {
        error_log('EditAppointment: No rows affected for id=' . $appointmentId . ', userId=' . $userId);
        echo json_encode(['success' => false, 'message' => 'No changes made to the appointment']);
    }
} else {
    error_log('EditAppointment: Update failed for id=' . $appointmentId . ', error=' . $stmt->error);
    echo json_encode(['success' => false, 'message' => 'Failed to update appointment: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>