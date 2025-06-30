<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

if (!isset($_SESSION['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in']);
    exit;
}

$userId = isset($_POST['userId']) ? (int)$_POST['userId'] : 0;
$doctorSpecialization = isset($_POST['DoctorSpecialization']) ? trim($_POST['DoctorSpecialization']) : '';
$doctorId = isset($_POST['DoctorId']) ? (int)$_POST['DoctorId'] : 0;
$consultancyFees = isset($_POST['ConsultancyFees']) ? (int)$_POST['ConsultancyFees'] : 0;
$appointmentDate = isset($_POST['AppointmentDate']) ? trim($_POST['AppointmentDate']) : '';
$appointmentTime = isset($_POST['AppointmentTime']) ? trim($_POST['AppointmentTime']) : '';

$errors = [];
if ($userId <= 0 || $userId != $_SESSION['patient_id']) {
    $errors[] = "Invalid user ID";
}
if (empty($doctorSpecialization)) {
    $errors[] = "Select a specialization";
}
if ($doctorId <= 0) {
    $errors[] = "Select a doctor";
}
if ($consultancyFees < 0) {
    $errors[] = "Invalid fees";
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
    echo json_encode(['success' => false, 'message' => implode(", ", $errors)]);
    exit;
}

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$stmt = $conn->prepare("SELECT id, docFees FROM doctors WHERE id = ? AND specialization = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
    exit;
}
$stmt->bind_param("is", $doctorId, $doctorSpecialization);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid doctor or specialization']);
    exit;
}
$doctor = $result->fetch_assoc();
if ($consultancyFees != $doctor['docFees']) {
    echo json_encode(['success' => false, 'message' => 'Fees mismatch']);
    exit;
}
$stmt->close();

$stmt = $conn->prepare("
    INSERT INTO appointment (doctorSpecialization, doctorId, userId, consultancyFees, appointmentDate, appointmentTime, userStatus, doctorStatus)
    VALUES (?, ?, ?, ?, ?, ?, 1, 1)
");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed']);
    exit;
}

$stmt->bind_param("siiiss", $doctorSpecialization, $doctorId, $userId, $consultancyFees, $appointmentDate, $appointmentTime);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment booked successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to book appointment: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>