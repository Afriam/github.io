<?php
session_start();
include 'config/database.php';

header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'logs/php_errors.log');
error_reporting(E_ALL);

try {
    // Check database connection
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    $stmt = $conn->prepare("SELECT DISTINCT specialization FROM doctorspecialization WHERE specialization IS NOT NULL AND specialization != '' ORDER BY specialization");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $specializations = [];
    while ($row = $result->fetch_assoc()) {
        $specializations[] = $row['specialization']; // Store as strings
    }
    $stmt->close();
    $conn->close();

    // Return the expected JSON format
    echo json_encode([
        'success' => true,
        'data' => $specializations,
        'message' => count($specializations) > 0 ? 'Specializations retrieved successfully' : 'No specializations found'
    ]);
} catch (Exception $e) {
    error_log("GetSpecializations error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'data' => [],
        'message' => 'Failed to retrieve specializations: ' . $e->getMessage()
    ]);
}
?>