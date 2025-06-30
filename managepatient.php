<?php
session_start();

// Include database connection
include "../config/database.php";

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Verify database connection
if (!$conn) {
    error_log("Database connection not established in managepatient.php");
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Handle different actions based on the request method and parameters
$action = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json');

try {
    if ($action === 'add') {
        // Add a new patient
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $middle_name = filter_input(INPUT_POST, 'middle_name', FILTER_SANITIZE_STRING);
        $family_name = filter_input(INPUT_POST, 'family_name', FILTER_SANITIZE_STRING);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email_address = filter_input(INPUT_POST, 'email_address', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $contactno = filter_input(INPUT_POST, 'contactno', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

        // Validate required fields
        if (!$first_name || !$family_name || !$username || !$email_address || !$password) {
            echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
            exit;
        }

        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT patient_id FROM patients WHERE username = ? OR email_address = ?");
        $stmt->bind_param("ss", $username, $email_address);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
            $stmt->close();
            exit;
        }
        $stmt->close();

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new patient
        $stmt = $conn->prepare("
            INSERT INTO patients (first_name, middle_name, family_name, username, email_address, password, contactno, address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssss", $first_name, $middle_name, $family_name, $username, $email_address, $hashed_password, $contactno, $address);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Patient added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add patient']);
        }
        $stmt->close();

    } elseif ($action === 'get') {
        // Fetch patient details for editing or viewing
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Patient ID is required']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT patient_id, first_name, middle_name, family_name, username, email_address, contactno, address
            FROM patients
            WHERE patient_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Patient not found']);
        }
        $stmt->close();

    } elseif ($action === 'get_appointments') {
        // Fetch patient's appointments
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Patient ID is required']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT a.id, a.doctorSpecialization, a.appointmentDate, a.appointmentTime, a.userStatus, a.doctorStatus, d.doctorName
            FROM appointment a
            LEFT JOIN doctors d ON a.doctorId = d.id
            WHERE a.userId = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $appointments]);
        $stmt->close();

    } elseif ($action === 'get_medical_history') {
        // Fetch patient's medical history
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Patient ID is required']);
            exit;
        }

        $stmt = $conn->prepare("
            SELECT ID, BloodPressure, BloodSugar, Weight, Temperature, MedicalPres
            FROM tblmedicalhistory
            WHERE PatientID = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $history]);
        $stmt->close();

    } elseif ($action === 'update') {
        // Update an existing patient
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $middle_name = filter_input(INPUT_POST, 'middle_name', FILTER_SANITIZE_STRING);
        $family_name = filter_input(INPUT_POST, 'family_name', FILTER_SANITIZE_STRING);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email_address = filter_input(INPUT_POST, 'email_address', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $contactno = filter_input(INPUT_POST, 'contactno', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

        // Validate required fields
        if (!$id || !$first_name || !$family_name || !$username || !$email_address) {
            echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
            exit;
        }

        // Check if username or email already exists for another patient
        $stmt = $conn->prepare("SELECT patient_id FROM patients WHERE (username = ? OR email_address = ?) AND patient_id != ?");
        $stmt->bind_param("ssi", $username, $email_address, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
            $stmt->close();
            exit;
        }
        $stmt->close();

        if ($password) {
            // Update with new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("
                UPDATE patients
                SET first_name = ?, middle_name = ?, family_name = ?, username = ?, email_address = ?, password = ?, contactno = ?, address = ?
                WHERE patient_id = ?
            ");
            $stmt->bind_param("ssssssssi", $first_name, $middle_name, $family_name, $username, $email_address, $hashed_password, $contactno, $address, $id);
        } else {
            // Update without changing password
            $stmt = $conn->prepare("
                UPDATE patients
                SET first_name = ?, middle_name = ?, family_name = ?, username = ?, email_address = ?, contactno = ?, address = ?
                WHERE patient_id = ?
            ");
            $stmt->bind_param("sssssssi", $first_name, $middle_name, $family_name, $username, $email_address, $contactno, $address, $id);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Patient updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update patient']);
        }
        $stmt->close();

    } elseif ($action === 'delete') {
        // Delete a patient
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Patient ID is required']);
            exit;
        }

        // Check if patient has appointments or medical history
        $stmt = $conn->prepare("SELECT id FROM appointment WHERE userId = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete patient with existing appointments']);
            $stmt->close();
            exit;
        }
        $stmt->close();

        $stmt = $conn->prepare("SELECT ID FROM tblmedicalhistory WHERE PatientID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete patient with existing medical history']);
            $stmt->close();
            exit;
        }
        $stmt->close();

        // Delete the patient
        $stmt = $conn->prepare("DELETE FROM patients WHERE patient_id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Patient deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete patient']);
        }
        $stmt->close();

    } elseif ($action === 'bulk_delete') {
        // Bulk delete patients
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $ids = isset($_POST['ids']) ? $_POST['ids'] : [];
        if (!is_array($ids) || empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'No patients selected for deletion']);
            exit;
        }

        // Sanitize IDs
        $ids = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // Check for dependencies (appointments or medical history)
        $stmt = $conn->prepare("SELECT userId FROM appointment WHERE userId IN ($placeholders)");
        foreach ($ids as $k => $id) {
            $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete patients with existing appointments']);
            $stmt->close();
            exit;
        }
        $stmt->close();

        $stmt = $conn->prepare("SELECT PatientID FROM tblmedicalhistory WHERE PatientID IN ($placeholders)");
        foreach ($ids as $k => $id) {
            $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete patients with existing medical history']);
            $stmt->close();
            exit;
        }
        $stmt->close();

        // Delete the patients
        $stmt = $conn->prepare("DELETE FROM patients WHERE patient_id IN ($placeholders)");
        foreach ($ids as $k => $id) {
            $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
        }
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Patients deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete patients']);
        }
        $stmt->close();

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("managepatient.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}

$conn->close();
?>