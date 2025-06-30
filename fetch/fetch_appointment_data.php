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

// Fetch appointment data grouped by month, converting appointmentDate from varchar to date
$stmt = $conn->prepare("
    SELECT DATE_FORMAT(STR_TO_DATE(appointmentDate, '%Y-%m-%d'), '%Y-%m') AS month, COUNT(*) AS count
    FROM appointment
    WHERE userId = ?
    GROUP BY DATE_FORMAT(STR_TO_DATE(appointmentDate, '%Y-%m-%d'), '%Y-%m')
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
    $currentMonth = new DateTime('2025-04-01'); // Based on sample data
    for ($i = 5; $i >= 0; $i--) {
        $month = (clone $currentMonth)->modify("-$i months");
        $labels[] = $month->format('Y-m');
        $counts[] = 0;
    }
} else {
    // Ensure we have the last 6 months, filling in gaps with zeros
    $currentMonth = new DateTime('2025-04-01'); // Based on sample data
    $tempLabels = $labels;
    $tempCounts = $counts;
    $labels = [];
    $counts = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = (clone $currentMonth)->modify("-$i months");
        $monthStr = $month->format('Y-m');
        $index = array_search($monthStr, $tempLabels);
        if ($index !== false) {
            $labels[] = $monthStr;
            $counts[] = $tempCounts[$index];
        } else {
            $labels[] = $monthStr;
            $counts[] = 0;
        }
    }
}

echo json_encode([
    'success' => true,
    'labels' => $labels,
    'counts' => $counts
]);
?>