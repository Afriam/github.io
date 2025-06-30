<?php
header('Content-Type: application/json');
include 'config/database.php';

$city_mun_id = isset($_GET['city_mun_id']) ? intval($_GET['city_mun_id']) : 0;
$stmt = $conn->prepare("SELECT barangay_id, barangay_name FROM barangays WHERE city_mun_id = ? ORDER BY barangay_name");
$stmt->bind_param("i", $city_mun_id);
$stmt->execute();
$result = $stmt->get_result();
$barangays = [];
while ($row = $result->fetch_assoc()) {
    $barangays[] = $row;
}

echo json_encode(['success' => true, 'data' => $barangays]);
$stmt->close();
$conn->close();
?>