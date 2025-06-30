<?php
session_start();
include 'config/database.php';

header('Content-Type: application/json');

// Verify database connection
if (!$conn) {
    error_log("Database connection not established in loginval.php");
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

if (!$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'Username/Email and password are required']);
    exit;
}

try {
    // Check patient login
    $stmt = $conn->prepare("SELECT patient_id, username, password FROM patients WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        if (password_verify($password, $patient['password'])) {
            $_SESSION['patient_id'] = $patient['patient_id'];
            $_SESSION['username'] = $patient['username'];
            $_SESSION['user_type'] = 'patient';
            echo json_encode(['success' => true, 'redirect' => 'UserDashboard.php']);
            $stmt->close();
            $conn->close();
            exit;
        }
    }
    $stmt->close();

    // Check doctor login
    $stmt = $conn->prepare("SELECT id, username, password FROM doctors WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
        if (password_verify($password, $doctor['password'])) {
            $_SESSION['doctor_id'] = $doctor['id'];
            $_SESSION['username'] = $doctor['username'];
            $_SESSION['user_type'] = 'doctor';
            echo json_encode(['success' => true, 'redirect' => 'doctor/DoctorDashboard.php']);
            $stmt->close();
            $conn->close();
            exit;
        }
    }
    $stmt->close();

    // Check admin login
    $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        error_log("Admin login attempt - Username: {$username}, Password from DB: {$admin['password']}, Provided Password: {$password}");

        // Check if the password is hashed (starts with $2y$ indicates a bcrypt hash)
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['user_type'] = 'admin';
            echo json_encode(['success' => true, 'redirect' => 'admin/AdminDashboard.php']);
            $stmt->close();
            $conn->close();
            exit;
        } else {
            // Fallback for plaintext password (temporary for debugging)
            if ($admin['password'] === $password) {
                error_log("Admin logged in with plaintext password - please hash the password in the database!");
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['user_type'] = 'admin';
                echo json_encode(['success' => true, 'redirect' => 'admin/AdminDashboard.php']);
                $stmt->close();
                $conn->close();
                exit;
            }
            error_log("Admin password verification failed.");
        }
    } else {
        error_log("Admin username not found: {$username}");
    }
    $stmt->close();

    echo json_encode(['success' => false, 'message' => 'Invalid username/email or password']);
} catch (Exception $e) {
    error_log("Loginval error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred during login']);
}

$conn->close();
?>