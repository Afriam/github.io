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

    $doctorId = $_SESSION['doctor_id'];
    $stmt = $conn->prepare("SELECT a.*, u.name AS patient_name FROM appointment a JOIN users u ON a.userId = u.id WHERE a.doctorId = :doctorId AND a.doctorStatus = 0");
    $stmt->bindParam(':doctorId', $doctorId);
    $stmt->execute();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($appointments);
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode([]);
}
?>