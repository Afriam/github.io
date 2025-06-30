<?php
// Start the session to access logged-in doctor's details
session_start();

// Check if the doctor is logged in
if (!isset($_SESSION['doctor_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mpdoc";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch doctor's details using doctor_id from session
    $stmt = $conn->prepare("SELECT doctorName, username FROM doctors WHERE id = :doctor_id");
    $stmt->bindParam(':doctor_id', $_SESSION['doctor_id']);
    $stmt->execute();
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Initialize variables
    $doctor_name = $doctor['doctorName'] ?? 'Doctor';
    $doctor_username = $doctor['username'] ?? 'Unknown';
    
} catch (PDOException $e) {
    // Handle database errors gracefully
    $doctor_name = 'Doctor';
    $doctor_username = 'Unknown';
    error_log("Database error: " . $e->getMessage());
}

// Close the connection
$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="http://localhost/new%20DOC%20APPOINTMENT/">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - Doctor DAS</title>
    <link rel="icon" href="image/mylogo.jpg" />

    <!-- Consolidated CSS Dependencies -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <link rel="stylesheet" href="css/sweetalert_dark.css">
    <link href="https://cdn.jsdelivr.net/gh/fontenele/bootstrap-navbar-dropdowns@5.0.2/dist/css/bootstrap-navbar-dropdowns.min.css" rel="stylesheet">

    <!-- Chart.js for Bar Graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <!-- Consolidated JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>
    <script src="js/sweetalert2.min.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>
    <script src="js/cleave.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/fontenele/bootstrap-navbar-dropdowns@5.0.2/dist/js/bootstrap-navbar-dropdowns.min.js"></script>

    <style type="text/css">
        .bg-dark {
            background-color: #0e7ebf !important;
            color: black;
        }
        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 10rem;
            padding: .5rem 0;
            margin: .125rem 0 0;
            font-size: 1rem;
            color: #080808;
            text-align: left;
            list-style: none;
            background-color: #0e7ebf;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: .25rem;
        }
        .dropdown-item {
            display: block;
            width: 100%;
            padding: .25rem 1.5rem;
            clear: both;
            font-weight: 400;
            color: #fbfbfb;
            text-align: inherit;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
        }
        #text_deco, .text_deco1 {
            text-decoration: none !important;
        }
        #text_deco:hover, .text_deco1:hover {
            color: #ffffff;
        }
        a {
            color: #ffffff;
            text-decoration: none;
            background-color: transparent;
            -webkit-text-decoration-skip: objects;
        }
        .nv-dropdown-bs4 .dropdown-item:focus, .nv-dropdown-bs4 .dropdown-item:hover {
            color: #ffffff;
            text-decoration: none;
            background-color: #193d52;
        }
        .modal {
            overflow-y: auto;
        }
        .weekly-overview-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .weekly-overview-day {
            flex: 1 1 120px;
            min-width: 120px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            padding: 10px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .weekly-overview-day:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .weekly-overview-day.highlighted {
            background-color: #d1e7dd;
        }
        .weekly-overview-day.today {
            border: 2px solid #0e7ebf;
        }
        .weekly-overview-day.sunday {
            background-color: #ffe6e6; /* Light red for Sunday */
            color: #cc0000;
        }
        .weekly-overview-day.weekday {
            background-color: #e6f3ff; /* Light blue for Monday-Friday */
            color: #0033cc;
        }
        .weekly-overview-day.saturday {
            background-color: #fff5e6; /* Light orange for Saturday */
            color: #e68a00;
        }
        .weekly-overview-day .day-name {
            font-size: 1rem;
            font-weight: bold;
        }
        .weekly-overview-day .day-date {
            font-size: 0.9rem;
        }
        .weekly-overview-day .appointments-count {
            font-size: 0.85rem;
            color: #19bc9d;
        }
        .week-navigation {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        @media (max-width:1024px) {
            .navbar-header {
                width: 100%;
                text-align: center;
            }
            .navbar-brand, #img_navbar-brand {
                display: none;
            }
            .navbar-brand1, #img_navbar-brand1 {
                display: block;
                float: none;
            }
        }
        @media (min-width: 1024px) {
            .navbar-brand, #img_navbar-brand {
                display: block;
                float: none;
            }
            .navbar-brand1, #img_navbar-brand1 {
                display: none;
            }
        }
        .checkbox {
            -ms-transform: scale(2);
            -moz-transform: scale(2);
            -webkit-transform: scale(2);
            -o-transform: scale(2);
            transform: scale(2);
            padding: 10px;
        }
        .checkboxtext {
            font-size: 110%;
        }
        button:hover {
            opacity: 1;
        }
        .container {
            padding: 16px;
        }
        .modal1 {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: #474e5d;
            padding-top: 50px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto 15% auto;
            border: 1px solid #888;
            width: 80%;
        }
        hr {
            border: 1px solid #f1f1f1;
            margin-bottom: 25px;
        }
        .close {
            position: absolute;
            right: 35px;
            top: 15px;
            font-size: 40px;
            font-weight: bold;
            color: #f1f1f1;
        }
        .close:hover, .close:focus {
            color: #f44336;
            cursor: pointer;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        @media screen and (max-width: 300px) {
            .cancelbtn, .signupbtn {
                width: 100%;
            }
        }
        .contact-form {
            padding: 50px;
            margin: 30px 0;
            background: white;
        }
        .contact-form h1 {
            color: #19bc9d;
            font-weight: bold;
            margin: 0 0 15px;
        }
        .contact-form .form-control, .contact-form .btn {
            min-height: 38px;
            border-radius: 2px;
        }
        .contact-form .form-control:focus {
            border-color: #19bc9d;
        }
        .contact-form .btn-primary, .contact-form .btn-primary:active {
            color: #fff;
            min-width: 150px;
            font-size: 16px;
            background: #19bc9d !important;
            border: none;
        }
        .contact-form .btn-primary:hover {
            background: #15a487 !important;
        }
        .contact-form .btn i {
            margin-right: 5px;
        }
        .contact-form label {
            opacity: 0.7;
        }
        .contact-form textarea {
            resize: vertical;
        }
        .hint-text {
            font-size: 15px;
            padding-bottom: 20px;
            opacity: 0.6;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
        }
        .welcome-message {
            margin-bottom: 20px;
        }
        .dashboard-box {
            background: aliceblue;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            height: 300px;
            overflow-y: auto;
        }
        canvas {
            max-height: 200px;
        }
    </style>
</head>
<body>
    <?php include "../doctor/includes/navbar.php"; ?>

    <section id="content" style="margin: 20px; margin-top: 80px;">
        <div class="card text-info bg-primary mb-12">
            <div class="card-body">
                <div class="card border-dark mb-12">
                    <div class="card-body text-dark">
                        <div class="row">
                            <div class="col-md-12 welcome-message">
                                <h4>Welcome, Dr. <?php echo htmlspecialchars($doctor_name); ?>!</h4>
                            </div>
                        </div>
                        <!-- Weekly Overview -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="dashboard-box">
                                    <h5>Weekly Overview</h5>
                                    <div class="weekly-overview-container" id="weeklyOverview"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Pending Appointments -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="dashboard-box">
                                    <h5>New and Not Approved Appointments</h5>
                                    <div class="form-group">
                                        <input type="text" id="appointmentSearch" class="form-control mb-2" placeholder="Search by patient name or appointment ID">
                                    </div>
                                    <table id="pendingAppointments" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Appointment ID</th>
                                                <th>Patient</th>
                                                <th>Schedule</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../includes/footer.php'; ?>
    </section>

    <!-- Appointment Details Modal -->
    <div id="appointmentDetailsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Appointment Details</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body" id="appointmentDetailsContent">
                    <!-- Appointment details will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Appointment Modal -->
    <div id="approveAppointmentModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <form id="approveAppointmentForm">
                        <input type="hidden" id="approveAppointmentId" name="appointmentId">
                        <p>Are you sure you want to approve this appointment?</p>
                        <button type="submit" class="btn btn-primary">Approve</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Prescription Modal -->
    <div id="addPrescriptionModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Prescription</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <form id="addPrescriptionForm">
                        <input type="hidden" id="prescriptionAppointmentId" name="appointmentId">
                        <input type="hidden" id="prescriptionPatientId" name="patientId">
                        <div class="form-group">
                            <label for="bloodPressure">Blood Pressure:</label>
                            <input type="text" class="form-control" id="bloodPressure" name="bloodPressure" placeholder="e.g., 120/80">
                        </div>
                        <div class="form-group">
                            <label for="bloodSugar">Blood Sugar:</label>
                            <input type="text" class="form-control" id="bloodSugar" name="bloodSugar" placeholder="e.g., 90 mg/dL" required>
                        </div>
                        <div class="form-group">
                            <label for="weight">Weight:</label>
                            <input type="text" class="form-control" id="weight" name="weight" placeholder="e.g., 70 kg">
                        </div>
                        <div class="form-group">
                            <label for="temperature">Temperature:</label>
                            <input type="text" class="form-control" id="temperature" name="temperature" placeholder="e.g., 36.6°C">
                        </div>
                        <div class="form-group">
                            <label for="medicalPrescription">Medical Prescription:</label>
                            <textarea class="form-control" id="medicalPrescription" name="medicalPrescription" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Prescription</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="ChangePasswordModal" data-keyboard="false" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <form id="ChangePasswordForm" method="post">
                        <label for="CurrentPassword">Current Password:</label>
                        <div class="input-group">
                            <input type="password" name="CurrentPassword" id="CurrentPassword" class="form-control" required placeholder="Current Password">
                            <div class="input-group-append">
                                <button id="CurrentPasswordButton" type="button" class="btn btn-default" style="background: #f3f0f0;">
                                    <span id="spanCurrentPassword" class="fa fa-eye-slash" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                        <label for="NewPassword">New Password:</label>
                        <div class="input-group">
                            <input type="password" name="NewPassword" id="NewPassword" class="form-control" required placeholder="New Password">
                            <div class="input-group-append">
                                <button id="NewPasswordButton" type="button" class="btn btn-default" style="background: #f3f0f0;">
                                    <span id="spanNewPassword" class="fa fa-eye-slash" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                        <label for="VerifyPassword">Verify Password:</label>
                        <div class="input-group">
                            <input type="password" name="VerifyPassword" id="VerifyPassword" class="form-control" required placeholder="Verify Password">
                            <div class="input-group-append">
                                <button id="VerifyPasswordButton" type="button" class="btn btn-default" style="background: #f3f0f0;">
                                    <span id="spanVerifyPassword" class="fa fa-eye-slash" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                        <hr />
                        <button type="submit" id="AddUserButton" class="btn btn-success">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggles
        $("#CurrentPasswordButton").click(function() {
            var x = $("#CurrentPassword");
            var span = $("#spanCurrentPassword");
            if (x.attr("type") === "password") {
                span.removeClass("fa-eye-slash").addClass("fa-eye");
                x.attr("type", "text");
            } else {
                span.removeClass("fa-eye").addClass("fa-eye-slash");
                x.attr("type", "password");
            }
        });

        $("#NewPasswordButton").click(function() {
            var x = $("#NewPassword");
            var span = $("#spanNewPassword");
            if (x.attr("type") === "password") {
                span.removeClass("fa-eye-slash").addClass("fa-eye");
                x.attr("type", "text");
            } else {
                span.removeClass("fa-eye").addClass("fa-eye-slash");
                x.attr("type", "password");
            }
        });

        $("#VerifyPasswordButton").click(function() {
            var x = $("#VerifyPassword");
            var span = $("#spanVerifyPassword");
            if (x.attr("type") === "password") {
                span.removeClass("fa-eye-slash").addClass("fa-eye");
                x.attr("type", "text");
            } else {
                span.removeClass("fa-eye").addClass("fa-eye-slash");
                x.attr("type", "password");
            }
        });

        // Change password form submission
        $("#ChangePasswordForm").submit(function(e) {
            e.preventDefault();
            var vForm = $(this);
            if (vForm[0].checkValidity() === false) {
                e.stopPropagation();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Please fill out all fields'
                });
            } else {
                showload();
                if ($("#NewPassword").val() === $("#VerifyPassword").val()) {
                    $.ajax({
                        type: "POST",
                        url: "change_password.php",
                        data: vForm.serialize(),
                        dataType: 'json',
                        success: function(response) {
                            hideload();
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Password successfully updated'
                                });
                                $("#ChangePasswordModal").modal("hide");
                                vForm[0].reset();
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            hideload();
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred: ' + error
                            });
                        }
                    });
                } else {
                    hideload();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'New password and verify password do not match'
                    });
                }
            }
            vForm.addClass('was-validated');
        });

        // Weekly Overview
        $(document).ready(function() {
            let currentWeekStart = null;

            // Function to populate Weekly Overview
            function populateWeeklyOverview(weekStartDate) {
                var today = new Date();
                var todayStr = today.toISOString().split('T')[0];
                var weekStart = new Date(weekStartDate);
                
                // Ensure week starts on Sunday
                weekStart.setDate(weekStart.getDate() - weekStart.getDay());

                var container = $('#weeklyOverview');
                container.empty();

                // Add navigation buttons
                container.append(`
                    <div class="week-navigation">
                        <button id="prevWeek" class="btn btn-secondary">Previous Week</button>
                        <button id="nextWeek" class="btn btn-secondary">Next Week</button>
                    </div>
                `);

                // Create days container
                container.append('<div class="weekly-overview-days" style="display: flex; justify-content: space-between;"></div>');
                var daysContainer = $('.weekly-overview-days');

                // Array of day names
                var dayNames = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];

                // Loop through the week (Sunday to Saturday)
                for (var i = 0; i < 7; i++) {
                    var date = new Date(weekStart);
                    date.setDate(weekStart.getDate() + i);
                    var dateStr = date.toISOString().split('T')[0];
                    var isToday = (dateStr === todayStr) ? 'today' : '';
                    var dayColorClass = '';
                    var currentDayName = dayNames[i]; // Store the day name for this iteration
                    
                    // Apply color coding
                    if (i === 0) {
                        dayColorClass = 'sunday';
                    } else if (i === 6) {
                        dayColorClass = 'saturday';
                    } else {
                        dayColorClass = 'weekday';
                    }

                    // Perform AJAX call to fetch appointments
                    $.ajax({
                        url: 'fetch_appointments_by_date.php',
                        type: 'GET',
                        data: { date: dateStr, doctorId: <?php echo $_SESSION['doctor_id']; ?> },
                        dataType: 'json',
                        context: {
                            dateStr: dateStr,
                            dayColorClass: dayColorClass,
                            isToday: isToday,
                            dayName: currentDayName
                        },
                        success: function(response) {
                            var count = response.length;
                            var hasAppointments = count > 0 ? 'highlighted' : '';
                            var dayDiv = `
                                <div class="weekly-overview-day ${hasAppointments} ${this.isToday} ${this.dayColorClass}" data-date="${this.dateStr}">
                                    <div class="day-name">${this.dayName}</div>
                                    <div class="day-date">${this.dateStr}</div>
                                    <div class="appointments-count">${count} appointment(s)</div>
                                </div>`;
                            daysContainer.append(dayDiv);
                        },
                        error: function() {
                            var dayDiv = `
                                <div class="weekly-overview-day ${this.isToday} ${this.dayColorClass}" data-date="${this.dateStr}">
                                    <div class="day-name">${this.dayName}</div>
                                    <div class="day-date">${this.dateStr}</div>
                                    <div class="appointments-count">0 appointment(s)</div>
                                </div>`;
                            daysContainer.append(dayDiv);
                        }
                    });
                }

                // Store current week start
                currentWeekStart = new Date(weekStart);
            }

            // Initialize with current week
            populateWeeklyOverview(new Date());

            // Navigation button handlers
            $(document).on('click', '#prevWeek', function() {
                var prevWeek = new Date(currentWeekStart);
                prevWeek.setDate(currentWeekStart.getDate() - 7);
                populateWeeklyOverview(prevWeek);
            });

            $(document).on('click', '#nextWeek', function() {
                var nextWeek = new Date(currentWeekStart);
                nextWeek.setDate(currentWeekStart.getDate() + 7);
                populateWeeklyOverview(nextWeek);
            });

            // Click handler for day details
            $('#weeklyOverview').on('click', '.weekly-overview-day', function() {
                var date = $(this).data('date');
                $.ajax({
                    url: 'fetch_appointment_details.php',
                    type: 'GET',
                    data: { date: date, doctorId: <?php echo $_SESSION['doctor_id']; ?> },
                    dataType: 'json',
                    success: function(response) {
                        var content = '<ul>';
                        response.forEach(function(appointment) {
                            content += `<li>Appointment ID: ${appointment.id}, Patient: ${appointment.patient_name}, Time: ${appointment.appointmentTime}</li>`;
                        });
                        content += '</ul>';
                        $('#appointmentDetailsContent').html(content);
                        $('#appointmentDetailsModal').modal('show');
                    },
                    error: function() {
                        $('#appointmentDetailsContent').html('<p>No details available.</p>');
                        $('#appointmentDetailsModal').modal('show');
                    }
                });
            });

            // Initialize DataTable for Pending Appointments
            var pendingAppointmentsTable = $('#pendingAppointments').DataTable({
                ajax: {
                    url: 'fetch_pending_appointments.php',
                    dataSrc: ''
                },
                columns: [
                    { data: 'id' },
                    { data: 'patient_name' },
                    {
                        data: null,
                        render: function(data) {
                            return data.appointmentDate + ' ' + data.appointmentTime;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return '<button class="btn btn-success btn-sm approve-btn" data-id="' + data.id + '">Approve</button> ' +
                                   '<button class="btn btn-danger btn-sm cancel-btn" data-id="' + data.id + '">Cancel</button>';
                        }
                    }
                ]
            });

            // Search functionality for pending appointments
            $('#appointmentSearch').on('keyup', function() {
                pendingAppointmentsTable.search(this.value).draw();
            });

            // Approve appointment
            $('#pendingAppointments').on('click', '.approve-btn', function() {
                var appointmentId = $(this).data('id');
                $('#approveAppointmentId').val(appointmentId);
                $('#approveAppointmentModal').modal('show');
            });

            $('#approveAppointmentForm').submit(function(e) {
                e.preventDefault();
                var appointmentId = $('#approveAppointmentId').val();
                $.ajax({
                    type: 'POST',
                    url: 'approve_appointment.php',
                    data: { appointmentId: appointmentId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Appointment Approved'
                            });
                            $('#approveAppointmentModal').modal('hide');
                            pendingAppointmentsTable.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred'
                        });
                    }
                });
            });

            // Cancel appointment
            $('#pendingAppointments').on('click', '.cancel-btn', function() {
                var appointmentId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to cancel this appointment?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, cancel it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: 'cancel_appointment.php',
                            data: { appointmentId: appointmentId },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Appointment Cancelled'
                                    });
                                    pendingAppointmentsTable.ajax.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred'
                                });
                            }
                        });
                    }
                });
            });

            // Placeholder for showload and hideload functions
            function showload() {
                // Implement loading spinner
            }
            function hideload() {
                // Hide loading spinner
            }
        });
    </script>
</body>
</html>