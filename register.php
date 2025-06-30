<?php
session_start();
header('Content-Type: application/json');
require_once 'config/database.php';

// Enable error logging but suppress direct output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'logs/php_errors.log');
error_reporting(E_ALL);

// Check database connection
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Helper functions to fetch address names
function getRegionName($conn, $region_id) {
    $stmt = $conn->prepare("SELECT region_name FROM regions WHERE region_id = ?");
    $stmt->bind_param("i", $region_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['region_name'];
    }
    $stmt->close();
    return null;
}

function getProvinceName($conn, $province_id) {
    $stmt = $conn->prepare("SELECT province_name FROM provinces WHERE province_id = ?");
    $stmt->bind_param("i", $province_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['province_name'];
    }
    $stmt->close();
    return null;
}

function getCityMunicipalityName($conn, $city_mun_id) {
    $stmt = $conn->prepare("SELECT city_mun_name FROM cities_municipalities WHERE city_mun_id = ?");
    $stmt->bind_param("i", $city_mun_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['city_mun_name'];
    }
    $stmt->close();
    return null;
}

function getBarangayName($conn, $barangay_id) {
    $stmt = $conn->prepare("SELECT barangay_name FROM barangays WHERE barangay_id = ?");
    $stmt->bind_param("i", $barangay_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['barangay_name'];
    }
    $stmt->close();
    return null;
}



// Validate UserType
if (empty($_POST['UserType'])) {
    echo json_encode(['success' => false, 'message' => 'User type is required']);
    $conn->close();
    exit;
}
$userType = htmlspecialchars($_POST['UserType'], ENT_QUOTES, 'UTF-8');

if ($userType === 'patient') {
    // Patient registration logic
    $required_fields = [
        'Username', 'Password', 'ConfirmPassword', 'FamilyName', 'FirstName', 'MiddleName', 'DOB', 'Age', 'POB', 'EmailAddress',
        'Sex', 'CivilStatus', 'Nationality',
        'PermanentAddress_Region', 'PermanentAddress_Province', 'PermanentAddress_CityMunicipality', 'PermanentAddress_Barangay',
        'PermanentAddress_Street', 'PermanentAddress_ZipCode'
    ];

    // If "Same as Permanent Address" is checked, home address fields are not required
    $sameAsPermanent = isset($_POST['CheckBoxAddress']) && $_POST['CheckBoxAddress'] === 'on';
    if (!$sameAsPermanent) {
        $required_fields = array_merge($required_fields, [
            'HomeAddress_Region', 'HomeAddress_Province', 'HomeAddress_CityMunicipality', 'HomeAddress_Barangay',
            'HomeAddress_Street', 'HomeAddress_ZipCode'
        ]);
    }

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            echo json_encode(['success' => false, 'message' => "Missing or empty required field: $field"]);
            $conn->close();
            exit;
        }
    }

    // Validate password match
    if ($_POST['Password'] !== $_POST['ConfirmPassword']) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        $conn->close();
        exit;
    }

    // Validate username and password format
    $username = htmlspecialchars($_POST['Username'], ENT_QUOTES, 'UTF-8');
    $password = $_POST['Password'];
    if (!preg_match('/^[A-Za-z0-9]{4,}$/', $username)) {
        echo json_encode(['success' => false, 'message' => 'Username must be at least 4 characters, letters and numbers only']);
        $conn->close();
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        $conn->close();
        exit;
    }

    // Check for duplicate username
    $stmt = $conn->prepare("SELECT patient_id FROM patients WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Sanitize input data
    $family_name = htmlspecialchars($_POST['FamilyName'], ENT_QUOTES, 'UTF-8');
    $first_name = htmlspecialchars($_POST['FirstName'], ENT_QUOTES, 'UTF-8');
    $middle_name = htmlspecialchars($_POST['MiddleName'], ENT_QUOTES, 'UTF-8');
    $dob = htmlspecialchars($_POST['DOB'], ENT_QUOTES, 'UTF-8');
    $age = (int)$_POST['Age'];
    $pob = htmlspecialchars($_POST['POB'], ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['EmailAddress'], FILTER_SANITIZE_EMAIL);
    $sex = htmlspecialchars($_POST['Sex'], ENT_QUOTES, 'UTF-8');
    $civil_status = htmlspecialchars($_POST['CivilStatus'], ENT_QUOTES, 'UTF-8');
    $nationality = htmlspecialchars($_POST['Nationality'], ENT_QUOTES, 'UTF-8');

    // Fetch permanent address names
    $perm_region_id = (int)$_POST['PermanentAddress_Region'];
    $perm_province_id = (int)$_POST['PermanentAddress_Province'];
    $perm_city_mun_id = (int)$_POST['PermanentAddress_CityMunicipality'];
    $perm_barangay_id = (int)$_POST['PermanentAddress_Barangay'];
    
    $perm_region = getRegionName($conn, $perm_region_id);
    $perm_province = getProvinceName($conn, $perm_province_id);
    $perm_city_municipality = getCityMunicipalityName($conn, $perm_city_mun_id);
    $perm_barangay = getBarangayName($conn, $perm_barangay_id);
    
    // Validate that all address components were found
    if (!$perm_region || !$perm_province || !$perm_city_municipality || !$perm_barangay) {
        echo json_encode(['success' => false, 'message' => 'Invalid permanent address selection']);
        $conn->close();
        exit;
    }
    
    $perm_street = htmlspecialchars($_POST['PermanentAddress_Street'], ENT_QUOTES, 'UTF-8');
    $perm_zip = htmlspecialchars($_POST['PermanentAddress_ZipCode'], ENT_QUOTES, 'UTF-8');

    // Handle home address
    if ($sameAsPermanent) {
        // Copy permanent address to home address
        $home_region_id = $perm_region_id;
        $home_province_id = $perm_province_id;
        $home_city_mun_id = $perm_city_mun_id;
        $home_barangay_id = $perm_barangay_id;
        $home_region = $perm_region;
        $home_province = $perm_province;
        $home_city_municipality = $perm_city_municipality;
        $home_barangay = $perm_barangay;
        $home_street = $perm_street;
        $home_zip = $perm_zip;
    } else {
        // Fetch home address names
        $home_region_id = (int)$_POST['HomeAddress_Region'];
        $home_province_id = (int)$_POST['HomeAddress_Province'];
        $home_city_mun_id = (int)$_POST['HomeAddress_CityMunicipality'];
        $home_barangay_id = (int)$_POST['HomeAddress_Barangay'];
        
        $home_region = getRegionName($conn, $home_region_id);
        $home_province = getProvinceName($conn, $home_province_id);
        $home_city_municipality = getCityMunicipalityName($conn, $home_city_mun_id);
        $home_barangay = getBarangayName($conn, $home_barangay_id);
        
        // Validate that all address components were found
        if (!$home_region || !$home_province || !$home_city_municipality || !$home_barangay) {
            echo json_encode(['success' => false, 'message' => 'Invalid home address selection']);
            $conn->close();
            exit;
        }
        
        $home_street = htmlspecialchars($_POST['HomeAddress_Street'], ENT_QUOTES, 'UTF-8');
        $home_zip = htmlspecialchars($_POST['HomeAddress_ZipCode'], ENT_QUOTES, 'UTF-8');
    }

    // Insert patient data
    $stmt = $conn->prepare("INSERT INTO patients (
        username, password, family_name, first_name, middle_name, date_of_birth, age, place_of_birth, email_address,
        sex, civil_status, nationality, perm_region, perm_province, perm_city_municipality, perm_barangay,
        perm_street, perm_zip_code, home_region, home_province, home_city_municipality, home_barangay,
        home_street, home_zip_code
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log("Prepare failed for patient insert: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Failed to prepare patient insert query']);
        $conn->close();
        exit;
    }
    $stmt->bind_param("ssssssisssssssssssssssss", 
        $username,
        $hashed_password,
        $family_name, 
        $first_name, 
        $middle_name, 
        $dob, 
        $age, 
        $pob, 
        $email,
        $sex, 
        $civil_status, 
        $nationality,
        $perm_region,
        $perm_province,
        $perm_city_municipality,
        $perm_barangay,
        $perm_street,
        $perm_zip,
        $home_region,
        $home_province,
        $home_city_municipality,
        $home_barangay,
        $home_street,
        $home_zip
    );

    if ($stmt->execute()) {
        // Clear CSRF token after successful registration
        unset($_SESSION['csrf_token']);
        echo json_encode(['success' => true, 'message' => 'Registration successful', 'userType' => 'patient', 'identifier' => $username, 'password' => $password]);
    } else {
        error_log("Execute failed for patient insert: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $stmt->error]);
    }
    $stmt->close();
} elseif ($userType === 'doctor') {
    // Doctor registration logic
    $required_fields = [
        'DoctorUsername', 'DoctorName', 'DoctorAddress', 'Specialization', 'DocFees', 'ContactNo', 'DocEmail',
        'DoctorPassword', 'DoctorConfirmPassword'
    ];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            echo json_encode(['success' => false, 'message' => "Missing or empty required field: $field"]);
            $conn->close();
            exit;
        }
    }

    // Validate password match
    if ($_POST['DoctorPassword'] !== $_POST['DoctorConfirmPassword']) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        $conn->close();
        exit;
    }

    // Validate username, password, and contact number format
    $username = htmlspecialchars($_POST['DoctorUsername'], ENT_QUOTES, 'UTF-8');
    $password = $_POST['DoctorPassword'];
    $contactno = $_POST['ContactNo'];
    if (!preg_match('/^[A-Za-z0-9]{4,}$/', $username)) {
        echo json_encode(['success' => false, 'message' => 'Username must be at least 4 characters, letters and numbers only']);
        $conn->close();
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        $conn->close();
        exit;
    }
    if (!preg_match('/^[0-9]{10,11}$/', $contactno)) {
        echo json_encode(['success' => false, 'message' => 'Contact number must be 10-11 digits']);
        $conn->close();
        exit;
    }

    // Validate specialization exists
    $specialization = htmlspecialchars($_POST['Specialization'], ENT_QUOTES, 'UTF-8');
    $stmt = $conn->prepare("SELECT id FROM doctorspecialization WHERE specialization = ?");
    if (!$stmt) {
        error_log("Prepare failed for specialization check: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Failed to prepare specialization query']);
        $conn->close();
        exit;
    }
    $stmt->bind_param("s", $specialization);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid specialization']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Check for duplicate username or email
    $email = filter_var($_POST['DocEmail'], FILTER_SANITIZE_EMAIL);
    $stmt = $conn->prepare("SELECT id FROM doctors WHERE username = ? OR docEmail = ?");
    if (!$stmt) {
        error_log("Prepare failed for duplicate check: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Failed to prepare duplicate check query']);
        $conn->close();
        exit;
    }
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Sanitize inputs and prepare data
    $doctor_name = htmlspecialchars($_POST['DoctorName'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['DoctorAddress'], ENT_QUOTES, 'UTF-8');
    $doc_fees = (float)$_POST['DocFees'];
    $contactno = htmlspecialchars($_POST['ContactNo'], ENT_QUOTES, 'UTF-8');

    // Validate docFees is numeric and positive
    if (!is_numeric($doc_fees) || $doc_fees <= 0) {
        echo json_encode(['success' => false, 'message' => 'Consultancy fees must be a positive number']);
        $conn->close();
        exit;
    }

    // Prepare and execute the insert query for doctors
    $stmt = $conn->prepare("INSERT INTO doctors (
        username, doctorName, address, specialization, docFees, contactno, docEmail, password
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log("Prepare failed for doctor insert: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Failed to prepare doctor insert query']);
        $conn->close();
        exit;
    }
    $stmt->bind_param(
        "ssssdsss",
        $username,
        $doctor_name,
        $address,
        $specialization,
        $doc_fees,
        $contactno,
        $email,
        $hashed_password
    );

    if ($stmt->execute()) {
        // Clear CSRF token after successful registration
        unset($_SESSION['csrf_token']);
        echo json_encode([
            'success' => true,
            'message' => 'Doctor registration successful',
            'userType' => 'doctor',
            'identifier' => $username,
            'password' => $password
        ]);
    } else {
        error_log("Execute failed for doctor insert: " . $stmt->error);
        echo json_encode(['success' => false, 'message' => 'Doctor registration failed: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user type']);
}

$conn->close();
?>