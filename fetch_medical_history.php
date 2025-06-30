<?php
header('Content-Type: application/json');
require_once 'config/database.php';

// Check database connection
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get PatientID (assumed to be passed via GET or session; adjust as needed)
$patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;

if ($patient_id <= 0) {
    echo json_encode(['error' => 'Invalid Patient ID']);
    exit;
}

// Fetch medical history records
$stmt = $conn->prepare("SELECT ID, BloodPressure, BloodSugar, Weight, Temperature, MedicalPres, CreationDate FROM tblmedicalhistory WHERE PatientID = ? ORDER BY CreationDate DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}

echo json_encode($records);

$stmt->close();
$conn->close();
?>