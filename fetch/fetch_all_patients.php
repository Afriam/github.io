<?php
require_once '../config/database.php';
header('Content-Type: application/json');
try {
    $stmt = $conn->prepare("SELECT patient_id, first_name, family_name, middle_name, email_address, date_of_birth, age, sex, civil_status, nationality, perm_region, perm_province, perm_city_municipality, perm_barangay, perm_street, perm_zip_code, home_region, home_province, home_city_municipality, home_barangay, home_street, home_zip_code, place_of_birth FROM patients");
    $stmt->execute();
    $result = $stmt->get_result();
    $patients = [];
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
    echo json_encode($patients);
    $stmt->close();
} catch (Exception $e) {
    error_log("Fetch all patients error: " . $e->getMessage());
    echo json_encode(['error' => 'Failed to fetch patients']);
}
?>