<?php
header('Content-Type: application/json');
include 'config/database.php';

$result = $conn->query("SELECT region_id, region_name FROM regions ORDER BY region_name");
$regions = [];
while ($row = $result->fetch_assoc()) {
    $regions[] = $row;
}

echo json_encode(['success' => true, 'data' => $regions]);
$conn->close();
?>