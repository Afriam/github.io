<?php
header('Content-Type: application/json');
include 'config/database.php';

$province_id = isset($_GET['province_id']) ? intval($_GET['province_id']) : 0;
$stmt = $conn->prepare("SELECT city_mun_id, city_mun_name FROM cities_municipalities WHERE province_id = ? ORDER BY city_mun_name");
$stmt->bind_param("i", $province_id);
$stmt->execute();
$result = $stmt->get_result();
$cities = [];
while ($row = $result->fetch_assoc()) {
    $cities[] = $row;
}

echo json_encode(['success' => true, 'data' => $cities]);
$stmt->close();
$conn->close();
?>