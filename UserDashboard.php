<?php
session_start();
include 'config/database.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

// Fetch user information
$patient_id = $_SESSION['patient_id'];
$stmt = $conn->prepare("SELECT family_name, first_name, middle_name FROM patients WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Prepare full name
$full_name = htmlspecialchars($user['first_name'] . ' ' . (empty($user['middle_name']) ? '' : $user['middle_name'][0] . '. ') . $user['family_name']);
$username = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <base href="http://localhost/new%20DOC%20APPOINTMENT/"> <!-- Adjust if your project path differs -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - User DAS</title>
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
    <?php include "includes/navbar.php" ?>
    
    <section id="content" style="margin: 20px; margin-top: 80px;">
        <div class="card text-info bg-primary mb-12">
            <div class="card-body">
                <div class="card border-dark mb-12">
                    <div class="card-body text-dark">
                        <div class="row">
                            <div class="col-md-12 welcome-message">
                                <h4>Welcome, <?php echo $full_name; ?>!</h4>
                            </div>
                        </div>
                        <div class="row">
                            <!-- First Box: Available Doctors -->
                            <div class="col-md-12">
                                <div class="dashboard-box">
                                    <h5>Available Doctors</h5>
                                    <!-- Add Search and Filter -->
                                    <div class="form-group">
                                        <input type="text" id="doctorSearch" class="form-control mb-2" placeholder="Search by name or specialization">
                                    </div>
                                    <table id="doctorsTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Specialization</th>
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
        <?php include 'includes/footer.php' ?>
    </section>
    
    <!-- Doctor Details Modal -->
    <div id="doctorDetailsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Doctor Details</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <div id="doctorDetailsContent">
                        <p><strong>Name:</strong> <span id="doctorName"></span></p>
                        <p><strong>Specialization:</strong> <span id="doctorSpecialization"></span></p>
                        <p><strong>Address:</strong> <span id="doctorAddress"></span></p>
                        <p><strong>Contact Number:</strong> <span id="doctorContact"></span></p>
                        <p><strong>Email:</strong> <span id="doctorEmail"></span></p>
                        <p><strong>Consultancy Fees:</strong> <span id="doctorFees"></span></p>
                    </div>
                    <hr />
                    <div class="col-md-12">
                        <div class="text-right">
                            <button type="button" class="btn btn-info btn-lg" id="bookAppointmentBtn">
                                <i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i> Book Appointment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Book Appointment Modal -->
    <div id="bookAppointmentModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <form id="bookAppointmentForm">
                        <input type="hidden" id="DoctorId" name="DoctorId">
                        <input type="hidden" id="DoctorSpecialization" name="DoctorSpecialization">
                        <input type="hidden" id="ConsultancyFees" name="ConsultancyFees">
                        <div class="form-group">
                            <label for="AppointmentDate">Appointment Date:</label>
                            <input type="date" class="form-control" id="AppointmentDate" name="AppointmentDate" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="AppointmentTime">Appointment Time:</label>
                            <input type="time" class="form-control" id="AppointmentTime" name="AppointmentTime" required min="09:00" max="17:00">
                        </div>
                        <button type="submit" class="btn btn-primary">Confirm Booking</button>
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

    // Initialize DataTables and Charts using AJAX
    $(document).ready(function() {
        var patientId = <?php echo $patient_id; ?>;

        // AJAX call to fetch and show doctors
        var doctorsTable = $('#doctorsTable').DataTable({
            "ajax": {
                "url": "fetch/fetch_doctors.php",
                "type": "GET",
                "dataSrc": "",
                "error": function(xhr, error, thrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load doctors list: ' + (xhr.responseText || thrown)
                    });
                }
            },
            "columns": [
                { "data": "doctorName" },
                { "data": "specialization" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return '<button class="btn btn-info btn-sm viewDoctorBtn" data-id="' + row.id + '">View</button>';
                    }
                }
            ],
            "pageLength": 5,
            "searching": false, // Disable DataTables default search
            "lengthChange": false
        });

        // Custom search functionality
        $('#doctorSearch').on('keyup', function() {
            doctorsTable.search(this.value).draw();
        });

        // Override DataTables search to include both name and specialization
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var searchTerm = $('#doctorSearch').val().toLowerCase();
                var name = data[0].toLowerCase(); // doctorName
                var specialization = data[1].toLowerCase(); // specialization
                return name.includes(searchTerm) || specialization.includes(searchTerm);
            }
        );

        // Handle click on View button to show doctor details in modal
        $('#doctorsTable tbody').on('click', '.viewDoctorBtn', function() {
            var data = doctorsTable.row($(this).parents('tr')).data();
            console.log("Doctor Data: ", data); // Debug the data object
            $('#doctorName').text(data.doctorName || 'N/A');
            $('#doctorSpecialization').text(data.specialization || 'N/A');
            $('#doctorAddress').text(data.address || 'N/A');
            $('#doctorContact').text(data.contactno || 'N/A');
            $('#doctorEmail').text(data.docEmail || 'N/A');
            $('#doctorFees').text(data.docFees || 'N/A');
            $('#DoctorId').val(data.id || -1);
            $('#DoctorSpecialization').val(data.specialization || '');
            $('#ConsultancyFees').val(data.docFees || 0);
            $('#doctorDetailsModal').modal('show');
        });

        // Handle click on Book Appointment button to show booking modal
        $('#bookAppointmentBtn').click(function() {
            $('#doctorDetailsModal').modal('hide');
            $('#bookAppointmentModal').modal('show');
        });

        // Handle booking form submission
        $('#bookAppointmentForm').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var appointmentTime = $('#AppointmentTime').val();
            // Validate time is between 09:00 and 17:00
            if (appointmentTime < '09:00' || appointmentTime > '17:00') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Time',
                    text: 'Please select a time between 09:00 and 17:00.'
                });
                return;
            }
            if (form[0].checkValidity() === false) {
                e.stopPropagation();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Please fill out all fields correctly.'
                });
            } else {
                showload();
                var formData = form.serialize() + '&userId=' + patientId;
                console.log("AJAX URL: fetch/BookAppointment.php");
                console.log("Data being sent: ", formData);
                $.ajax({
                    type: "POST",
                    url: "fetch/BookAppointment.php",
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        hideload();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Appointment Booked',
                                text: 'Your appointment has been successfully booked!'
                            });
                            $('#bookAppointmentModal').modal('hide');
                            form[0].reset();
                            // Reload the appointment bar graph
                            $.ajax({
                                url: 'fetch/fetch_appointment_data.php',
                                type: 'GET',
                                data: { patient_id: patientId },
                                dataType: 'json',
                                success: function(data) {
                                    if (data.success) {
                                        // Destroy existing chart if it exists
                                        var chartInstance = Chart.getChart('appointmentChart');
                                        if (chartInstance) {
                                            chartInstance.destroy();
                                        }
                                        var ctx = document.getElementById('appointmentChart').getContext('2d');
                                        new Chart(ctx, {
                                            type: 'bar',
                                            data: {
                                                labels: data.labels,
                                                datasets: [{
                                                    label: 'Appointments',
                                                    data: data.counts,
                                                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                                    borderColor: 'rgba(255, 99, 132, 1)',
                                                    borderWidth: 1
                                                }]
                                            },
                                            options: {
                                                scales: {
                                                    y: {
                                                        beginAtZero: true,
                                                        title: {
                                                            display: true,
                                                            text: 'Count'
                                                        }
                                                    },
                                                    x: {
                                                        title: {
                                                            display: true,
                                                            text: 'Month'
                                                        }
                                                    }
                                                },
                                                plugins: {
                                                    legend: {
                                                        display: false
                                                    }
                                                }
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.error || 'Failed to load appointment data after booking.'
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Failed to fetch appointment data: ' + (xhr.responseText || error)
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to book appointment.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        hideload();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to book appointment. Status: ' + status + ', Error: ' + error + ', Response: ' + (xhr.responseText || 'No response')
                        });
                    }
                });
            }
            form.addClass('was-validated');
        });

        // AJAX call to fetch Appointment data for bar graph
        $.ajax({
            url: 'fetch/fetch_appointment_data.php',
            type: 'GET',
            data: { patient_id: patientId },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    var ctx = document.getElementById('appointmentChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Appointments',
                                data: data.counts,
                                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Count'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Month'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Failed to load appointment data.'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch appointment data: ' + (xhr.responseText || error)
                });
            }
        });
    });

    // Loading Functions
    function showload() {
        Swal.fire({
            html: 'Please wait...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    function hideload() {
        setTimeout(function() { Swal.close(); }, 2000);
    }
    </script>
</body>
</html>