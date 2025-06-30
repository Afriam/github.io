<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mpdoc";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $appointmentId = $_POST['appointmentId'];
    $stmt = $conn->prepare("UPDATE appointment SET doctorStatus = 1 WHERE id = :id AND doctorId = :doctorId");
    $stmt->bindParam(':id', $appointmentId);
    $stmt->bindParam(':doctorId', $_SESSION['doctor_id']);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>