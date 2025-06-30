<?php
header('Content-Type: application/json');
include 'config/database.php';

$region_id = isset($_GET['region_id']) ? intval($_GET['region_id']) : 0;
$stmt = $conn->prepare("SELECT province_id, province_name FROM provinces WHERE region_id = ? ORDER BY province_name");
$stmt->bind_param("i", $region_id);
$stmt->execute();
$result = $stmt->get_result();
$provinces = [];
while ($row = $result->fetch_assoc()) {
    $provinces[] = $row;
}

echo json_encode(['success' => true, 'data' => $provinces]);
$stmt->close();
$conn->close();
?>