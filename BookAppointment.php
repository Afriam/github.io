<?php
session_start();
include 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['patient_id']) || !isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$userId = $_SESSION['patient_id'];

// Retrieve and sanitize form data directly
$doctorSpecialization = isset($_POST['DoctorSpecialization']) ? trim(htmlspecialchars($_POST['DoctorSpecialization'])) : '';
$doctorId = isset($_POST['DoctorId']) ? filter_var($_POST['DoctorId'], FILTER_VALIDATE_INT) : false;
$consultancyFees = isset($_POST['ConsultancyFees']) ? trim(htmlspecialchars($_POST['ConsultancyFees'])) : '';
$appointmentDate = isset($_POST['AppointmentDate']) ? trim(htmlspecialchars($_POST['AppointmentDate'])) : '';
$appointmentTime = isset($_POST['AppointmentTime']) ? trim(htmlspecialchars($_POST['AppointmentTime'])) : '';

// Log the received data for debugging
error_log("Received form data: " . print_r([
    'DoctorSpecialization' => $doctorSpecialization,
    'DoctorId' => $doctorId,
    'ConsultancyFees' => $consultancyFees,
    'AppointmentDate' => $appointmentDate,
    'AppointmentTime' => $appointmentTime
], true));

// Validate inputs
if (empty($doctorSpecialization) || $doctorId === false || empty($appointmentDate) || empty($appointmentTime)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Validate specialization exists
$stmt = $conn->prepare("SELECT specilization FROM doctorspecilization WHERE specilization = ?");
$stmt->bind_param("s", $doctorSpecialization);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid specialization']);
    $stmt->close();
    exit;
}
$stmt->close();

// Validate doctor and fetch fees if consultancyFees is empty
$stmt = $conn->prepare("SELECT docFees FROM doctors WHERE id = ? AND specilization = ?");
$stmt->bind_param("is", $doctorId, $doctorSpecialization);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid doctor']);
    $stmt->close();
    exit;
}
$doctor = $result->fetch_assoc();
$docFees = is_numeric($doctor['docFees']) ? (int)$doctor['docFees'] : 0;

// If consultancyFees is empty, use the fetched docFees
if (empty($consultancyFees)) {
    $consultancyFees = $docFees;
} else {
    // Validate consultancyFees is numeric if provided
    if (!is_numeric($consultancyFees)) {
        echo json_encode(['success' => false, 'message' => 'Invalid consultancy fees']);
        $stmt->close();
        exit;
    }
    $consultancyFees = (int)$consultancyFees;
    // Validate the provided consultancyFees matches the doctor's fees
    if ($docFees !== $consultancyFees) {
        echo json_encode(['success' => false, 'message' => 'Consultancy fees mismatch']);
        $stmt->close();
        exit;
    }
}
$stmt->close();

// Validate date (must be today or future)
$today = date('Y-m-d');
if ($appointmentDate < $today) {
    echo json_encode(['success' => false, 'message' => 'Appointment date must be today or in the future']);
    exit;
}

// Validate time format (HH:MM)
if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/', $appointmentTime)) {
    echo json_encode(['success' => false, 'message' => 'Invalid time format']);
    exit;
}

// Check if appointment already exists
$stmt = $conn->prepare("SELECT id FROM appointment WHERE userId = ? AND doctorId = ? AND appointmentDate = ? AND appointmentTime = ? AND userStatus = 1 AND doctorStatus = 1");
$stmt->bind_param("iiss", $userId, $doctorId, $appointmentDate, $appointmentTime);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You already have an appointment with this doctor at this time']);
    $stmt->close();
    exit;
}
$stmt->close();

// Insert appointment
$stmt = $conn->prepare("INSERT INTO appointment (doctorSpecialization, doctorId, userId, consultancyFees, appointmentDate, appointmentTime, userStatus, doctorStatus) VALUES (?, ?, ?, ?, ?, ?, 1, 1)");
$stmt->bind_param("siiiss", $doctorSpecialization, $doctorId, $userId, $consultancyFees, $appointmentDate, $appointmentTime);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment booked successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to book appointment']);
}
$stmt->close();
$conn->close();
?>