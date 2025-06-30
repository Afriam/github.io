<?php
session_start();
header('Content-Type: application/json');
require_once 'config/database.php';

// Check if the user is logged in
if (!isset($_SESSION['patient_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Check database connection
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get PatientID from session
$patient_id = (int)$_SESSION['patient_id'];

if ($patient_id <= 0) {
    echo json_encode(['error' => 'Invalid Patient ID']);
    exit;
}

// Fetch patient details
$stmt = $conn->prepare("
    SELECT 
        first_name, middle_name, family_name, ext_name, age,
        CONCAT(perm_street, ', ', perm_barangay, ', ', perm_city_municipality, ', ', perm_province, ', ', perm_region, ', ', perm_zip_code) AS permanent_address
    FROM patients 
    WHERE patient_id = ?
");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Construct full name
    $full_name = trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['family_name'] . ' ' . ($row['ext_name'] ? $row['ext_name'] : ''));
    echo json_encode([
        'success' => true,
        'full_name' => $full_name,
        'age' => $row['age'],
        'address' => $row['permanent_address']
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Patient not found']);
}

$stmt->close();
$conn->close();
?>