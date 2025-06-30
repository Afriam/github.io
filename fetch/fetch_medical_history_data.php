<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php'; // Should resolve to C:\htdocs\new DOC APPOINTMENT\config\database.php

// Check if the user is logged in
if (!isset($_SESSION['patient_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Check database connection
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$patient_id = (int)$_SESSION['patient_id'];

// Fetch medical history data grouped by month
$stmt = $conn->prepare("
    SELECT DATE_FORMAT(CreationDate, '%Y-%m') AS month, COUNT(*) AS count
    FROM tblmedicalhistory
    WHERE PatientID = ?
    GROUP BY DATE_FORMAT(CreationDate, '%Y-%m')
    ORDER BY month
");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$counts = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['month'];
    $counts[] = (int)$row['count'];
}

$stmt->close();
$conn->close();

// If no data, provide default months (last 6 months) with zero counts
if (empty($labels)) {
    $currentMonth = new DateTime('2025-04-01'); // Based on current date context
    for ($i = 5; $i >= 0; $i--) {
        $month = (clone $currentMonth)->modify("-$i months");
        $labels[] = $month->format('Y-m');
        $counts[] = 0;
    }
}

echo json_encode([
    'success' => true,
    'labels' => $labels,
    'counts' => $counts
]);
?>