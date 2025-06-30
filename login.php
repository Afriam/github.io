<?php
session_start();
include "config/database.php";

// Generate CSRF token and store it in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login Page - MP Doctor Appointment System</title>
    <link rel="icon" href="image/mylogo.jpg" />
    
    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <link rel="stylesheet" href="css/sweetalert2_dark.css">

    <!-- JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="js/sweetalert2.min.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>
    <script src="js/cleave.js"></script>

    <style>
        .container { padding: 16px; }
        .contact-form { padding: 50px; margin: 30px 0; background: white; }
        .contact-form h1 { color: #19bc9d; font-weight: bold; margin: 0 0 15px; }
        .contact-form .form-control, .contact-form .btn { min-height: 38px; border-radius: 2px; }
        .contact-form .form-control:focus { border-color: #19bc9d; }
        .contact-form label { opacity: 0.7; }
        .contact-form textarea { resize: vertical; }
        .hint-text { font-size: 15px; padding-bottom: 20px; opacity: 0.6; }
        @media (max-width:1024px) {
            .navbar-header { width: 100%; text-align: center; }
            .navbar-brand { display: none; color: white; }
            .navbar-brand1 { display: block; color: white; float: none; }
        }
        @media (min-width: 1024px) {
            .navbar-brand { display: block; color: white; float: none; }
            .navbar-brand1 { display: none; color: white; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4 fixed-top" role="navigation">
        <a class="navbar-brand" href="index.php"><img src="image/mylogo.jpg" class="rounded-circle" alt="MP DAS Logo"> MP Doctor Appointment System</a>
        <a class="navbar-brand1" href="index.php"><img src███ src="image/mylogo.jpg" class="rounded-circle" alt="MP DAS Logo"> MP DAS</a>        
        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navbarCollapse" class="collapse navbar-collapse justify-content-start">        
            <div class="navbar-nav ml-auto">
                <div class="nav-item dropdown">
                    <i class="far fa-calendar-alt"></i>
                    Today is: <?php echo date('l, F d, Y'); ?>
                </div>
            </div>
        </div>
    </nav>
    <br/><br/><br/>

    <section id="content"> 
        <div class="login-form container">
            <form class="needs-validation" id="Login_user" method="post" novalidate aria-label="Login Form">
                <h2 class="text-center">Log in</h2>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <span class="fa fa-user"></span>
                            </span>
                        </div>
                        <input type="text" class="form-control" name="username" placeholder="Username/Email" required aria-label="Username or Email">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-lock"></i>
                            </span>
                        </div>
                        <input type="password" class="form-control" name="password" placeholder="Password" required aria-label="Password">
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary login-btn btn-block">Sign in</button>
                </div>
                <div class="clearfix">
                    <a href="#" class="float-right" data-toggle="modal" data-target="#ForgotPasswordModal">Forgot Password?</a>
                </div> 
                <hr />
                <p class="text-muted small">Don't have an account? <a href="#" data-toggle="modal" data-target="#RegisterAccountModal">Register Account</a></p>
            </form>
        </div>

        <!-- Registration Modal -->
        <div id="RegisterAccountModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">USER REGISTRATION FORM</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="Registration" method="post" class="needs-validation" novalidate aria-label="Registration Form">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <div class="form-group">
                                <label for="UserType">Register as:</label>
                                <select class="form-control" name="UserType" id="UserType" required aria-label="User Type">
                                    <option value="">Select User Type</option>
                                    <option value="patient">Patient</option>
                                    <option value="doctor">Doctor</option>
                                </select>
                            </div>

                            <!-- Patient Registration Fields -->
                            <div id="patientFields" style="display: none;">
                                <fieldset class="border p-2">
                                    <legend class="w-auto">Personal Information</legend>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="FamilyName">Family Name: </label>
                                                <input type="text" class="form-control" onkeyup="ToUpperCase(this)" name="FamilyName" id="FamilyName" required aria-label="Family Name">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="FirstName">First Name: </label>
                                                <input type="text" class="form-control" onkeyup="ToUpperCase(this)" name="FirstName" id="FirstName" required aria-label="First Name">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="MiddleName">Middle Name: </label>
                                                <input type="text" class="form-control" onkeyup="ToUpperCase(this)" name="MiddleName" id="MiddleName" required aria-label="Middle Name">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="ExtName">Ext. Name: </label>
                                                <input type="text" class="form-control" onkeyup="ToUpperCase(this)" name="ExtName" id="ExtName" aria-label="Extension Name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="Username">Username: </label>
                                                <input type="text" class="form-control" name="Username" id="Username" pattern="[A-Za-z0-9]{4,}" title="Username must be at least 4 characters, letters and numbers only" required aria-label="Username">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="Password">Password: </label>
                                                <input type="password" class="form-control" name="Password" id="Password" minlength="6" title="Password must be at least 6 characters" required aria-label="Password">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="ConfirmPassword">Confirm Password: </label>
                                                <input type="password" class="form-control" name="ConfirmPassword" id="ConfirmPassword" minlength="6" title="Password must be at least 6 characters" required aria-label="Confirm Password">
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="EmailAddress">Email Address: </label>
                                                <input type="email" class="form-control" name="EmailAddress" id="EmailAddress" required aria-label="Email Address">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="DOB">Date of Birth: </label>
                                                <input type="date" class="form-control" name="DOB" id="DOB" required aria-label="Date of Birth">
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="Age">Age: </label>
                                                <input type="number" class="form-control" name="Age" id="Age" readonly aria-label="Age">
                                            </div>
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <label for="POB">Place of Birth: </label>
                                                <input type="text" onkeyup="ToUpperCase(this)" class="form-control" name="POB" id="POB" required aria-label="Place of Birth">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="disability">Do you have a disability?: </label><br/>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" class="custom-control-input" id="yes" name="disability" value="1" onclick="toggleDisabilityType()" aria-label="Disability Yes">
                                                    <label class="custom-control-label" for="yes">Yes</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" checked class="custom-control-input" id="no" name="disability" value="0" onclick="toggleDisabilityType()" aria-label="Disability No">
                                                    <label class="custom-control-label" for="no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="disabilityTypeSelect">Select your disability type: <i>(If Yes)</i> </label>
                                                <select class="form-control" id="disabilityTypeSelect" name="disabilityType" disabled aria-label="Disability Type">
                                                    <option value="">Select...</option>
                                                    <option value="visual">Visual Impairment</option>
                                                    <option value="hearing">Hearing Impairment</option>
                                                    <option value="physical">Physical Disability</option>
                                                    <option value="learning">Learning Disability</option>
                                                    <option value="mental">Mental Health Condition</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="indigenous">Are you part of an Indigenous group?</label><br/>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" class="custom-control-input" id="indigenous-yes" name="indigenous" value="1" onclick="toggleIndigenousGroup()" aria-label="Indigenous Yes">
                                                    <label class="custom-control-label" for="indigenous-yes">Yes</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" checked class="custom-control-input" id="indigenous-no" name="indigenous" value="0" onclick="toggleIndigenousGroup()" aria-label="Indigenous No">
                                                    <label class="custom-control-label" for="indigenous-no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="indigenousGroupTextbox">If yes, please specify your Indigenous group:</label>
                                                <input type="text" class="form-control" id="indigenousGroupTextbox" name="indigenousGroup" disabled aria-label="Indigenous Group">
                                            </div>
                                        </div>
                                    </div>

                                    <fieldset class="border p-2">
                                        <legend class="w-auto">Permanent Address: </legend>
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="PermanentAddress_Region">Region: </label>
                                                    <select class="form-control" name="PermanentAddress_Region" id="PermanentAddress_Region" required aria-label="Permanent Region">
                                                        <option value="">Select Region</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="PermanentAddress_Province">Province: </label>
                                                    <select class="form-control" name="PermanentAddress_Province" id="PermanentAddress_Province" required aria-label="Permanent Province">
                                                        <option value="">Select Province</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="PermanentAddress_CityMunicipality">City/Municipality: </label>
                                                    <select class="form-control" name="PermanentAddress_CityMunicipality" id="PermanentAddress_CityMunicipality" required aria-label="Permanent City/Municipality">
                                                        <option value="">Select City/Municipality</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="PermanentAddress_Barangay">Barangay: </label>
                                                    <select class="form-control" name="PermanentAddress_Barangay" id="PermanentAddress_Barangay" required aria-label="Permanent Barangay">
                                                        <option value="">Select Barangay</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="PermanentAddress_Street">Street/Purok: </label>
                                                    <input type="text" onkeyup="ToUpperCase(this)" class="form-control" name="PermanentAddress_Street" id="PermanentAddress_Street" required aria-label="Permanent Street">
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <div class="form-group">
                                                    <label for="PermanentAddress_ZipCode">Zip Code: </label>
                                                    <input type="text" onkeyup="ToUpperCase(this)" class="form-control" name="PermanentAddress_ZipCode" id="PermanentAddress_ZipCode" required aria-label="Permanent Zip Code">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <fieldset class="border p-2">
                                        <legend class="w-auto">Home/Present Address: </legend>
                                        <input type="checkbox" id="CheckBoxAddress" name="CheckBoxAddress" aria-label="Same as Permanent Address"> <label for="CheckBoxAddress">Same as Permanent Address</label>
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="HomeAddress_Region">Region: </label>
                                                    <select class="form-control" name="HomeAddress_Region" id="HomeAddress_Region" required aria-label="Home Region">
                                                        <option value="">Select Region</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="HomeAddress_Province">Province: </label>
                                                    <select class="form-control" name="HomeAddress_Province" id="HomeAddress_Province" required aria-label="Home Province">
                                                        <option value="">Select Province</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="HomeAddress_CityMunicipality">City/Municipality: </label>
                                                    <select class="form-control" name="HomeAddress_CityMunicipality" id="HomeAddress_CityMunicipality" required aria-label="Home City/Municipality">
                                                        <option value="">Select City/Municipality</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="HomeAddress_Barangay">Barangay: </label>
                                                    <select class="form-control" name="HomeAddress_Barangay" id="HomeAddress_Barangay" required aria-label="Home Barangay">
                                                        <option value="">Select Barangay</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="HomeAddress_Street">Street/Purok: </label>
                                                    <input type="text" onkeyup="ToUpperCase(this)" class="form-control" name="HomeAddress_Street" id="HomeAddress_Street" required aria-label="Home Street">
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <div class="form-group">
                                                    <label for="HomeAddress_ZipCode">Zip Code: </label>
                                                    <input type="text" onkeyup="ToUpperCase(this)" class="form-control" name="HomeAddress_ZipCode" id="HomeAddress_ZipCode" required aria-label="Home Zip Code">
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="Sex">Sex: </label>
                                                <select class="form-control" name="Sex" id="Sex" required aria-label="Sex">
                                                    <option value="">Select Sex</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="CivilStatus">Civil Status: </label>
                                                <select class="form-control" name="CivilStatus" id="CivilStatus" required aria-label="Civil Status">
                                                    <option value="">Select Status</option>
                                                    <option value="Single">Single</option>
                                                    <option value="Married">Married</option>
                                                    <option value="Separated">Separated</option>
                                                    <option value="Widow/Widower">Widow/Widower</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="Nationality">Nationality: </label>
                                                <input type="text" onkeyup="ToUpperCase(this)" class="form-control" name="Nationality" id="Nationality" required aria-label="Nationality">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="Spouse">Name of Husband/Wife if married: </label>
                                                <input type="text" onkeyup="ToUpperCase(this)" class="form-control" name="Spouse" id="Spouse" disabled aria-label="Spouse Name">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <!-- Doctor Registration Fields -->
                            <div id="doctorFields" style="display: none;">
                                <fieldset class="border p-2">
                                    <legend class="w-auto">Doctor Information</legend>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="DoctorUsername">Username: </label>
                                                <input type="text" class="form-control" name="DoctorUsername" id="DoctorUsername" pattern="[A-Za-z0-9]{4,}" title="Username must be at least 4 characters, letters and numbers only" required aria-label="Doctor Username">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="DoctorName">Full Name: </label>
                                                <input type="text" class="form-control" onkeyup="ToUpperCase(this)" name="DoctorName" id="DoctorName" required aria-label="Doctor Full Name">
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="DoctorAddress">Address: </label>
                                                <input type="text" class="form-control" onkeyup="ToUpperCase(this)" name="DoctorAddress" id="DoctorAddress" required aria-label="Doctor Address">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="Specialization">Specialization: </label>
                                                <select class="form-control" name="Specialization" id="Specialization" required aria-label="Specialization">
                                                    <option value="">Select Specialization</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="DocFees">Consultancy Fees: </label>
                                                <input type="number" class="form-control" name="DocFees" id="DocFees" min="0" required aria-label="Consultancy Fees">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="ContactNo">Contact Number: </label>
                                                <input type="text" class="form-control" name="ContactNo" id="ContactNo" pattern="[0-9]{10,11}" title="Contact number must be 10-11 digits" required aria-label="Contact Number">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="DocEmail">Email Address: </label>
                                                <input type="email" class="form-control" name="DocEmail" id="DocEmail" required aria-label="Doctor Email">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="DoctorPassword">Password: </label>
                                                <input type="password" class="form-control" name="DoctorPassword" id="DoctorPassword" minlength="6" title="Password must be at least 6 characters" required aria-label="Doctor Password">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="DoctorConfirmPassword">Confirm Password: </label>
                                                <input type="password" class="form-control" name="DoctorConfirmPassword" id="DoctorConfirmPassword" minlength="6" title="Password must be at least 6 characters" required aria-label="Doctor Confirm Password">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forgot Password Modal -->
        <div id="ForgotPasswordModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Forgot Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                    </div>
                    <div class="modal-body">
                        <p>Please contact the administrator at <a href="mailto:info@mpdas.com">info@mpdas.com</a> to reset your password.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    // Convert input to uppercase
    function ToUpperCase(element) {
        element.value = element.value.toUpperCase();
    }

    // Toggle disability type select
    function toggleDisabilityType() {
        const disabilityYes = document.getElementById('yes').checked;
        document.getElementById('disabilityTypeSelect').disabled = !disabilityYes;
    }

    // Toggle indigenous group textbox
    function toggleIndigenousGroup() {
        const indigenousYes = document.getElementById('indigenous-yes').checked;
        document.getElementById('indigenousGroupTextbox').disabled = !indigenousYes;
    }

    // Toggle registration fields based on user type
    $('#UserType').change(function() {
        const userType = $(this).val();
        $('#patientFields').toggle(userType === 'patient');
        $('#doctorFields').toggle(userType === 'doctor');

        // Update required fields
        $('#patientFields input, #patientFields select').prop('required', userType === 'patient');
        $('#doctorFields input, #doctorFields select').prop('required', userType === 'doctor');

        // Load specializations for doctor
        if (userType === 'doctor') {
            loadSpecializations();
        }
    });

    // Calculate age based on DOB
    $('#DOB').change(function() {
        const dob = new Date($(this).val());
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        $('#Age').val(age);
    });

    // Enable/disable spouse field based on civil status
    $('#CivilStatus').change(function() {
        const status = $(this).val();
        const spouseField = $('#Spouse');
        spouseField.prop('disabled', status !== 'Married');
        spouseField.prop('required', status === 'Married');
    });

    // Flag to track if address AJAX calls are complete
    let isAddressLoading = false;

    // Same as permanent address checkbox
    $('#CheckBoxAddress').change(function() {
        const isChecked = this.checked;
        const homeFields = [
            '#HomeAddress_Region',
            '#HomeAddress_Province',
            '#HomeAddress_CityMunicipality',
            '#HomeAddress_Barangay',
            '#HomeAddress_Street',
            '#HomeAddress_ZipCode'
        ];
        const permFields = [
            '#PermanentAddress_Region',
            '#PermanentAddress_Province',
            '#PermanentAddress_CityMunicipality',
            '#PermanentAddress_Barangay',
            '#PermanentAddress_Street',
            '#PermanentAddress_ZipCode'
        ];

        if (isChecked) {
            // Copy permanent address values to home address
            homeFields.forEach((field, index) => {
                $(field).val($(permFields[index]).val());
                $(field).prop('disabled', true);
            });

            // Validate that permanent address fields are populated
            const regionId = $('#PermanentAddress_Region').val();
            const provinceId = $('#PermanentAddress_Province').val();
            const cityMunId = $('#PermanentAddress_CityMunicipality').val();

            if (!regionId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Address',
                    text: 'Please select a region in the permanent address before checking "Same as Permanent Address"'
                });
                this.checked = false;
                homeFields.forEach(field => $(field).prop('disabled', false));
                return;
            }

            isAddressLoading = true;

            // Chain AJAX calls to populate home address dropdowns
            $.ajax({
                url: 'get_provinces.php',
                type: 'GET',
                data: { region_id: regionId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        populateDropdown('#HomeAddress_Province', response.data, 'province_id', 'province_name');
                        $('#HomeAddress_Province').val(provinceId);

                        if (provinceId) {
                            $.ajax({
                                url: 'get_cities_municipalities.php',
                                type: 'GET',
                                data: { province_id: provinceId },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        populateDropdown('#HomeAddress_CityMunicipality', response.data, 'city_mun_id', 'city_mun_name');
                                        $('#HomeAddress_CityMunicipality').val(cityMunId);

                                        if (cityMunId) {
                                            $.ajax({
                                                url: 'get_barangays.php',
                                                type: 'GET',
                                                data: { city_mun_id: cityMunId },
                                                dataType: 'json',
                                                success: function(response) {
                                                    if (response.success) {
                                                        populateDropdown('#HomeAddress_Barangay', response.data, 'barangay_id', 'barangay_name');
                                                        $('#HomeAddress_Barangay').val($('#PermanentAddress_Barangay').val());
                                                        isAddressLoading = false;
                                                    } else {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Error',
                                                            text: 'Failed to load barangays'
                                                        });
                                                        isAddressLoading = false;
                                                    }
                                                },
                                                error: function(xhr, status, error) {
                                                    console.error('get_barangays error:', xhr.responseText, status, error);
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: 'Failed to load barangays: ' + error
                                                    });
                                                    isAddressLoading = false;
                                                }
                                            });
                                        } else {
                                            isAddressLoading = false;
                                        }
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Failed to load cities/municipalities'
                                        });
                                        isAddressLoading = false;
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('get_cities_municipalities error:', xhr.responseText, status, error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Failed to load cities/municipalities: ' + error
                                    });
                                    isAddressLoading = false;
                                }
                            });
                        } else {
                            isAddressLoading = false;
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load provinces'
                        });
                        isAddressLoading = false;
                    }
                },
                error: function(xhr, status, error) {
                    console.error('get_provinces error:', xhr.responseText, status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load provinces: ' + error
                    });
                    isAddressLoading = false;
                }
            });
        } else {
            // Enable home address fields when unchecked
            homeFields.forEach(field => $(field).prop('disabled', false));
            isAddressLoading = false;
        }
    });

    // Populate address dropdowns
    function populateDropdown(selector, data, valueKey, textKey) {
        $(selector).empty().append('<option value="">Select...</option>');
        if (data && data.length > 0) {
            data.forEach(item => {
                $(selector).append(`<option value="${item[valueKey]}">${item[textKey]}</option>`);
            });
        } else {
            $(selector).append('<option value="">No options available</option>');
        }
    }

    // Load specializations for doctor registration
    function loadSpecializations() {
        $.ajax({
            url: 'GetSpecializations.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    $('#Specialization').empty().append('<option value="">Select Specialization</option>');
                    response.data.forEach(spec => {
                        $('#Specialization').append(`<option value="${spec}">${spec}</option>`);
                    });
                } else {
                    $('#Specialization').html('<option value="">No specializations available</option>');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No specializations available'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('GetSpecializations error:', xhr.responseText, status, error);
                $('#Specialization').html('<option value="">Failed to load specializations</option>');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load specializations: ' + error
                });
            }
        });
    }

    // Load regions on page load
    $(document).ready(function() {
        // Load regions for permanent and home address
        $.ajax({
            url: 'get_regions.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    populateDropdown('#PermanentAddress_Region', response.data, 'region_id', 'region_name');
                    populateDropdown('#HomeAddress_Region', response.data, 'region_id', 'region_name');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to load regions'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('get_regions error:', xhr.responseText, status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load regions: ' + error
                });
            }
        });

        // Load provinces when region is selected
        $('#PermanentAddress_Region, #HomeAddress_Region').change(function() {
            const regionId = $(this).val();
            const isPermanent = $(this).attr('id').includes('Permanent');
            const provinceSelector = isPermanent ? '#PermanentAddress_Province' : '#HomeAddress_Province';
            const citySelector = isPermanent ? '#PermanentAddress_CityMunicipality' : '#HomeAddress_CityMunicipality';
            const barangaySelector = isPermanent ? '#PermanentAddress_Barangay' : '#HomeAddress_Barangay';
            if (regionId) {
                $.ajax({
                    url: 'get_provinces.php',
                    type: 'GET',
                    data: { region_id: regionId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            populateDropdown(provinceSelector, response.data, 'province_id', 'province_name');
                            $(citySelector).empty().append('<option value="">Select City/Municipality</option>');
                            $(barangaySelector).empty().append('<option value="">Select Barangay</option>');

                            // If "Same as Permanent Address" is checked, update home address
                            if ($('#CheckBoxAddress').is(':checked') && isPermanent) {
                                $('#HomeAddress_Province').val($(provinceSelector).val());
                                $('#HomeAddress_Province').trigger('change');
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to load provinces'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('get_provinces error:', xhr.responseText, status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load provinces: ' + error
                        });
                    }
                });
            } else {
                $(provinceSelector).empty().append('<option value="">Select Province</option>');
                $(citySelector).empty().append('<option value="">Select City/Municipality</option>');
                $(barangaySelector).empty().append('<option value="">Select Barangay</option>');
            }
        });

        // Load cities/municipalities when province is selected
        $('#PermanentAddress_Province, #HomeAddress_Province').change(function() {
            const provinceId = $(this).val();
            const isPermanent = $(this).attr('id').includes('Permanent');
            const citySelector = isPermanent ? '#PermanentAddress_CityMunicipality' : '#HomeAddress_CityMunicipality';
            const barangaySelector = isPermanent ? '#PermanentAddress_Barangay' : '#HomeAddress_Barangay';
            if (provinceId) {
                $.ajax({
                    url: 'get_cities_municipalities.php',
                    type: 'GET',
                    data: { province_id: provinceId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            populateDropdown(citySelector, response.data, 'city_mun_id', 'city_mun_name');
                            $(barangaySelector).empty().append('<option value="">Select Barangay</option>');

                            // If "Same as Permanent Address" is checked, update home address
                            if ($('#CheckBoxAddress').is(':checked') && isPermanent) {
                                $('#HomeAddress_CityMunicipality').val($(citySelector).val());
                                $('#HomeAddress_CityMunicipality').trigger('change');
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to load cities/municipalities'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('get_cities_municipalities error:', xhr.responseText, status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load cities/municipalities: ' + error
                        });
                    }
                });
            } else {
                $(citySelector).empty().append('<option value="">Select City/Municipality</option>');
                $(barangaySelector).empty().append('<option value="">Select Barangay</option>');
            }
        });

        // Load barangays when city/municipality is selected
        $('#PermanentAddress_CityMunicipality, #HomeAddress_CityMunicipality').change(function() {
            const cityMunId = $(this).val();
            const isPermanent = $(this).attr('id').includes('Permanent');
            const barangaySelector = isPermanent ? '#PermanentAddress_Barangay' : '#HomeAddress_Barangay';
            if (cityMunId) {
                $.ajax({
                    url: 'get_barangays.php',
                    type: 'GET',
                    data: { city_mun_id: cityMunId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            populateDropdown(barangaySelector, response.data, 'barangay_id', 'barangay_name');

                            // If "Same as Permanent Address" is checked, update home address
                            if ($('#CheckBoxAddress').is(':checked') && isPermanent) {
                                $('#HomeAddress_Barangay').val($(barangaySelector).val());
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to load barangays'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('get_barangays error:', xhr.responseText, status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load barangays: ' + error
                        });
                    }
                });
            } else {
                $(barangaySelector).empty().append('<option value="">Select Barangay</option>');
            }
        });

        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please fill in all required fields correctly.'
                            });
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Validate password and confirm password match, and ensure address loading is complete
        $('#Registration').submit(function(e) {
            e.preventDefault();

            // Check if address loading is still in progress
            if (isAddressLoading) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Processing Address',
                    text: 'Please wait until the address fields are fully loaded.'
                });
                return false;
            }

            const userType = $('#UserType').val();
            let password, confirmPassword;
            if (userType === 'patient') {
                password = $('#Password').val();
                confirmPassword = $('#ConfirmPassword').val();
            } else if (userType === 'doctor') {
                password = $('#DoctorPassword').val();
                confirmPassword = $('#DoctorConfirmPassword').val();
            }
            if (!userType) {
                Swal.fire('Error', 'Please select a user type', 'error');
                return false;
            }
            if (password !== confirmPassword) {
                Swal.fire('Error', 'Passwords do not match', 'error');
                return false;
            }

            // Re-enable disabled home address fields to ensure they are included in form data
            const homeFields = [
                '#HomeAddress_Region',
                '#HomeAddress_Province',
                '#HomeAddress_CityMunicipality',
                '#HomeAddress_Barangay',
                '#HomeAddress_Street',
                '#HomeAddress_ZipCode'
            ];
            homeFields.forEach(field => $(field).prop('disabled', false));

            // Validate that all home address fields are populated if checkbox is checked
            if ($('#CheckBoxAddress').is(':checked')) {
                const permFields = [
                    '#PermanentAddress_Region',
                    '#PermanentAddress_Province',
                    '#PermanentAddress_CityMunicipality',
                    '#PermanentAddress_Barangay',
                    '#PermanentAddress_Street',
                    '#PermanentAddress_ZipCode'
                ];
                let allFieldsValid = true;
                homeFields.forEach((field, index) => {
                    if (!$(field).val()) {
                        allFieldsValid = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Missing Address Field',
                            text: `Home address field ${field.replace('#HomeAddress_', '')} is empty.`
                        });
                    }
                });
                if (!allFieldsValid) {
                    // Re-disable fields to maintain UI state
                    homeFields.forEach(field => $(field).prop('disabled', true));
                    return false;
                }
            }

            // AJAX for registration
            $.ajax({
                url: 'register.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            html: `Your ${response.userType === 'patient' ? 'username' : 'username'} is: <strong>${response.identifier}</strong><br>Your password is: <strong>${response.password}</strong>`,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#RegisterAccountModal').modal('hide');
                            $('#Registration')[0].reset();
                            $('#patientFields').hide();
                            $('#doctorFields').hide();
                            $('#UserType').val('');
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'An error occurred during registration: ' + error, 'error');
                    console.log('AJAX Error (register.php):', xhr, status, error);
                }
            });
        });

        // AJAX for login
        $('#Login_user').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: 'loginval.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful!',
                            text: 'Redirecting to dashboard...',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('loginval error:', xhr.responseText, status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred during login: ' + error
                    });
                }
            });
        });
    });
    </script>
</body>
</html>