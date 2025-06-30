<?php
session_start();
include 'config/database.php'; // Include database connection

// Set response header to JSON
header('Content-Type: application/json');

// Initialize response array
$response = ['success' => false, 'message' => ''];

// Check if user is logged in
if (!isset($_SESSION['patient_id'])) {
    $response['message'] = 'You must be logged in to change your password';
    echo json_encode($response);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Get POST data
$currentPassword = isset($_POST['CurrentPassword']) ? trim($_POST['CurrentPassword']) : '';
$newPassword = isset($_POST['NewPassword']) ? trim($_POST['NewPassword']) : '';
$verifyPassword = isset($_POST['VerifyPassword']) ? trim($_POST['VerifyPassword']) : '';
$patient_id = $_SESSION['patient_id'];

// Validate inputs
if (empty($currentPassword) || empty($newPassword) || empty($verifyPassword)) {
    $response['message'] = 'All fields are required';
    echo json_encode($response);
    exit;
}

// Validate new password length (minimum 8 characters)
if (strlen($newPassword) < 8) {
    $response['message'] = 'New password must be at least 8 characters long';
    echo json_encode($response);
    exit;
}

// Validate new password matches verify password
if ($newPassword !== $verifyPassword) {
    $response['message'] = 'New password and verify password do not match';
    echo json_encode($response);
    exit;
}

try {
    // Fetch current password from database
    $stmt = $conn->prepare("SELECT password FROM patients WHERE patient_id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $response['message'] = 'User not found';
        echo json_encode($response);
        $stmt->close();
        exit;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
        $response['message'] = 'Current password is incorrect';
        echo json_encode($response);
        exit;
    }

    // Hash new password
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password in database
    $stmt = $conn->prepare("UPDATE patients SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE patient_id = ?");
    $stmt->bind_param("si", $newPasswordHash, $patient_id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Password successfully updated';
    } else {
        $response['message'] = 'Failed to update password';
    }
    
    $stmt->close();
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>