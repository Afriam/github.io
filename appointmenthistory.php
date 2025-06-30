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

// Define base URL
$base_url = rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - DAS SMS</title>
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
        .button-container {
    display: flex;
    justify-content: space-between;
    gap: 10px; /* Reduced gap for overall spacing */
}
.button-container .btn-warning, .button-container .btn-danger {
    margin-left: 5px; /* Minimal spacing between Edit and Cancel */
}
.button-container .btn-secondary {
    margin-right: auto; /* Push Close to the left */
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
        .is-invalid ~ .invalid-feedback {
            display: block;
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
                            <div class="col-md-12">
                                <h3 class="card-title" id="card-title">Dashboard for the year (2024-2025)</h3>
                            </div>
                            <div class="col-md-12">
                                <div class="text-right">
                                    <button type="button" title='Book Now' class="btn btn-info btn-lg" data-toggle="modal" data-target="#BookAppointmentModal">
                                        <i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i> BOOK APPOINTMENT
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <fieldset style="border: 1px solid #007bff!important; background: aliceblue;" class="border p-2">
                                    <legend class="w-auto">
                                        <font style="font-size: 15px;">Appointments & Schedules</font>
                                    </legend>
                                    <div class="table-responsive">
                                        <table width="100%" style="font-size: 12px;" border="1" id="AppointmentTable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:center;">Doctor Name</th>
                                                    <th style="text-align:center;">Specialization</th>
                                                    <th style="text-align:center;">Consultation Fee</th>
                                                    <th style="text-align:center;">Schedule</th>
                                                    <th style="text-align:center;">Status</th>
                                                    <th style="text-align:center;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php' ?>
    </section>

    <!-- Book Appointment Modal -->
    <div id="BookAppointmentModal" data-keyboard="false" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Book Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <form id="BookAppointmentForm" method="post" class="contact-form">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="DoctorSpecialization">Doctor Specialization:</label>
                                    <select class="form-control" name="DoctorSpecialization" id="DoctorSpecialization" required>
                                        <option value="">Select Specialization</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a specialization.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="DoctorId">Doctor:</label>
                                    <select class="form-control" name="DoctorId" id="DoctorId" required>
                                        <option value="">Select Doctor</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a doctor.</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="ConsultancyFees">Consultancy Fees:</label>
                                    <input type="text" class="form-control" name="ConsultancyFees" id="ConsultancyFees" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="AppointmentDate">Appointment Date:</label>
                                    <input type="date" class="form-control" name="AppointmentDate" id="AppointmentDate" required>
                                    <div class="invalid-feedback">Please select a date.</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="AppointmentTime">Appointment Time:</label>
                                    <select class="form-control" name="AppointmentTime" id="AppointmentTime" required>
                                        <option value="">Select Time</option>
                                        <?php
                                        $start = strtotime('09:00');
                                        $end = strtotime('17:00');
                                        $interval = 30 * 60;
                                        for ($time = $start; $time <= $end; $time += $interval) {
                                            $timeValue = date('H:i', $time);
                                            $timeDisplay = date('h:i A', $time);
                                            echo "<option value=\"$timeValue\">$timeDisplay</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a time.</div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="button-container">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Book Appointment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Appointment Modal -->
    <div id="ViewAppointmentModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Appointment Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="appointmentDetails">
                        <p><strong>Doctor Name:</strong> <span id="detailDoctorName"></span></p>
                        <p><strong>Specialization:</strong> <span id="detailSpecialization"></span></p>
                        <p><strong>Consultancy Fees:</strong> <span id="detailConsultancyFees"></span></p>
                        <p><strong>Appointment Date:</strong> <span id="detailAppointmentDate"></span></p>
                        <p><strong>Appointment Time:</strong> <span id="detailAppointmentTime"></span></p>
                        <p><strong>Status:</strong> <span id="detailStatus"></span></p>
                    </div>
                    <div class="button-container">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button class="btn btn-warning" id="editAppointmentBtn" style="display: none;" >Edit</button>
                        <button class="btn btn-danger" id="cancelAppointmentBtn" style="display: none;">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Appointment Modal -->
    <div id="EditAppointmentModal" data-keyboard="false" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <form id="EditAppointmentForm" method="post" class="contact-form">
                        <input type="hidden" name="appointmentId" id="editAppointmentId">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="editDoctorSpecialization">Doctor Specialization:</label>
                                    <select class="form-control" name="DoctorSpecialization" id="editDoctorSpecialization" required>
                                        <option value="">Select Specialization</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a specialization.</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="editDoctorId">Doctor:</label>
                                    <select class="form-control" name="DoctorId" id="editDoctorId" required>
                                        <option value="">Select Doctor</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a doctor.</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="editConsultancyFees">Consultancy Fees:</label>
                                    <input type="text" class="form-control" name="ConsultancyFees" id="editConsultancyFees" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="editAppointmentDate">Appointment Date:</label>
                                    <input type="date" class="form-control" name="AppointmentDate" id="editAppointmentDate" required>
                                    <div class="invalid-feedback">Please select a date.</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="editAppointmentTime">Appointment Time:</label>
                                    <select class="form-control" name="AppointmentTime" id="editAppointmentTime" required>
                                        <option value="">Select Time</option>
                                        <?php
                                        $start = strtotime('09:00');
                                        $end = strtotime('17:00');
                                        $interval = 30 * 60;
                                        for ($time = $start; $time <= $end; $time += $interval) {
                                            $timeValue = date('H:i', $time);
                                            $timeDisplay = date('h:i A', $time);
                                            echo "<option value=\"$timeValue\">$timeDisplay</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a time.</div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <div class="button-container">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Show loading
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

    // Hide loading
    function hideload() {
        setTimeout(function() { Swal.close(); }, 1000);
    }

    // Check patient status
    function CheckData() {
        $.ajax({
            url: '<?php echo $base_url; ?>CheckPatientStatus.php',
            dataType: 'json',
            success: function(response) {
                if (response && response.status == "0") {
                    Swal.fire({
                        title: 'Update Information',
                        text: "Your information will be used by the consultant.",
                        icon: 'info',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Okay',
                        allowEscapeKey: false,
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire('Info', 'Please update your profile in the settings.', 'info');
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('CheckData error:', xhr.responseText || error);
            }
        });
    }

    // Initialize DataTable for Appointments
    $(document).ready(function() {
        console.log("UserID: <?php echo $patient_id; ?>");
        var baseUrl = '<?php echo $base_url; ?>';
        var appointmentTable = $('#AppointmentTable').DataTable({
            "ajax": {
                "url": baseUrl + "fetch/ShowAppointments.php?userId=<?php echo $patient_id; ?>",
                "dataSrc": "",
                "error": function(xhr, error, thrown) {
                    console.error('DataTable AJAX error:', xhr.responseText || thrown);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to load appointments. Please try again later.'
                    });
                }
            },
            "columns": [
                { "data": "doctorName", "defaultContent": "TBA" },
                { "data": "doctorSpecialization", "defaultContent": "N/A" },
                { "data": "consultancyFees", "defaultContent": "0" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return (row.appointmentDate || 'N/A') + ' ' + (row.appointmentTime || 'N/A');
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        if (row.userStatus == 1 && row.doctorStatus == 1) {
                            return 'Pending';
                        } else if (row.userStatus == 1 && row.doctorStatus == 2) {
                            return 'Approved';
                        } else {
                            return 'Canceled';
                        }
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return '<button class="btn btn-info btn-sm view-btn" data-id="' + row.id + '">View</button> ' +
                               '<button class="btn btn-danger btn-sm delete-btn" data-id="' + row.id + '">Delete</button>';
                    }
                }
            ],
            "pageLength": 10,
            "language": {
                "emptyTable": "No appointments available"
            }
        });

        // Load specializations
        function loadSpecializations(targetId) {
            $.ajax({
                url: baseUrl + 'fetch/GetSpecializations.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    console.log('Specializations:', data);
                    var html = '<option value="">Select Specialization</option>';
                    if (data && data.length > 0) {
                        $.each(data, function(i, item) {
                            html += '<option value="' + item.specialization + '">' + item.specialization + '</option>';
                        });
                    } else {
                        html += '<option value="">No specializations available</option>';
                    }
                    $(targetId).html(html);
                },
                error: function(xhr, status, error) {
                    console.error('GetSpecializations error:', xhr.responseText || error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to load specializations. Please try again.'
                    });
                }
            });
        }

        // Load doctors based on specialization
        function loadDoctors(specialization, targetId, feesId) {
            $.ajax({
                url: baseUrl + 'fetch/GetDoctors.php',
                type: 'GET',
                data: { specialization: specialization },
                dataType: 'json',
                success: function(data) {
                    console.log('Doctors:', data);
                    var html = '<option value="">Select Doctor</option>';
                    if (data && data.length > 0) {
                        $.each(data, function(i, item) {
                            html += '<option value="' + item.id + '" data-fees="' + item.docFees + '">' + item.doctorName + '</option>';
                        });
                    } else {
                        html += '<option value="">No doctors available</option>';
                    }
                    $(targetId).html(html);
                    $(feesId).val('');
                },
                error: function(xhr, status, error) {
                    console.error('GetDoctors error:', xhr.responseText || error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to load doctors. Please try again.'
                    });
                }
            });
        }

        // Book appointment form submission
        $("#BookAppointmentForm").submit(function(e) {
            e.preventDefault();
            var vForm = $(this);
            if (vForm[0].checkValidity() === false) {
                e.stopPropagation();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Please fill out all required fields.'
                });
            } else {
                showload();
                $.ajax({
                    type: "POST",
                    url: baseUrl + "fetch/BookAppointment.php",
                    data: vForm.serialize() + '&userId=<?php echo $patient_id; ?>',
                    dataType: 'json',
                    success: function(response) {
                        hideload();
                        console.log('BookAppointment response:', response);
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Appointment Booked',
                                text: 'Your appointment has been successfully booked.'
                            });
                            $("#BookAppointmentModal").modal("hide");
                            vForm[0].reset();
                            $("#ConsultancyFees").val('');
                            $("#DoctorId").html('<option value="">Select Doctor</option>');
                            appointmentTable.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Error',
                                text: response.message || 'Failed to book appointment.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        hideload();
                        console.error('BookAppointment error:', xhr.responseText || error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while booking. Please try again.'
                        });
                    }
                });
            }
            vForm.addClass('was-validated');
        });

        // Edit appointment form submission
        $("#EditAppointmentForm").submit(function(e) {
            e.preventDefault();
            var vForm = $(this);
            if (vForm[0].checkValidity() === false) {
                e.stopPropagation();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Please fill out all required fields.'
                });
            } else {
                showload();
                $.ajax({
                    type: "POST",
                    url: baseUrl + "fetch/edit_appointment.php",
                    data: vForm.serialize() + '&userId=<?php echo $patient_id; ?>',
                    dataType: 'json',
                    success: function(response) {
                        hideload();
                        console.log('EditAppointment response:', response);
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Appointment Updated',
                                text: 'Your appointment has been successfully updated.'
                            });
                            $("#EditAppointmentModal").modal("hide");
                            vForm[0].reset();
                            $("#editConsultancyFees").val('');
                            $("#editDoctorId").html('<option value="">Select Doctor</option>');
                            appointmentTable.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Error',
                                text: response.message || 'Failed to update appointment.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        hideload();
                        console.error('EditAppointment error:', xhr.responseText || error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating. Please try again.'
                        });
                    }
                });
            }
            vForm.addClass('was-validated');
        });

        // View appointment
        $('#AppointmentTable tbody').on('click', '.view-btn', function() {
            var data = appointmentTable.row($(this).parents('tr')).data();
            $('#detailDoctorName').text(data.doctorName || 'TBA');
            $('#detailSpecialization').text(data.doctorSpecialization || 'N/A');
            $('#detailConsultancyFees').text(data.consultancyFees || '0');
            $('#detailAppointmentDate').text(data.appointmentDate || 'N/A');
            $('#detailAppointmentTime').text(data.appointmentTime || 'N/A');
            $('#detailStatus').text(
                data.userStatus == 1 && data.doctorStatus == 1 ? 'Pending' :
                data.userStatus == 1 && data.doctorStatus == 2 ? 'Approved' : 'Canceled'
            );

            // Show/hide edit and cancel buttons based on status
            if (data.userStatus == 1 && data.doctorStatus == 1) {
                $('#editAppointmentBtn').show().data('id', data.id);
                $('#cancelAppointmentBtn').show().data('id', data.id);
            } else {
                $('#editAppointmentBtn').hide();
                $('#cancelAppointmentBtn').hide();
            }

            // Cancel appointment
            $('#cancelAppointmentBtn').off('click').on('click', function() {
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
                        showload();
                        $.ajax({
                            url: baseUrl + 'fetch/cancel_appointment.php',
                            type: 'POST',
                            data: { id: appointmentId, userId: <?php echo $patient_id; ?> },
                            dataType: 'json',
                            success: function(response) {
                                hideload();
                                console.log('CancelAppointment response:', response);
                                if (response.success) {
                                    Swal.fire('Canceled!', 'The appointment has been canceled.', 'success');
                                    $('#ViewAppointmentModal').modal('hide');
                                    appointmentTable.ajax.reload();
                                } else {
                                    Swal.fire('Error!', response.message || 'Failed to cancel the appointment.', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                hideload();
                                console.error('CancelAppointment error:', xhr.responseText || error);
                                Swal.fire('Error!', 'Failed to cancel the appointment. Please try again.', 'error');
                            }
                        });
                    }
                });
            });

            // Edit appointment
            $('#editAppointmentBtn').off('click').on('click', function() {
                var appointmentId = $(this).data('id');
                $('#editAppointmentId').val(appointmentId);
                $('#editDoctorSpecialization').val(data.doctorSpecialization);
                $('#editAppointmentDate').val(data.appointmentDate);
                $('#editAppointmentTime').val(data.appointmentTime);
                loadSpecializations('#editDoctorSpecialization');
                loadDoctors(data.doctorSpecialization, '#editDoctorId', '#editConsultancyFees');
                setTimeout(function() {
                    $('#editDoctorId').val(data.doctorId);
                    $('#editConsultancyFees').val(data.consultancyFees);
                }, 500);
                $('#ViewAppointmentModal').modal('hide');
                $('#EditAppointmentModal').modal('show');
            });

            $('#ViewAppointmentModal').modal('show');
        });

        // Delete appointment
        $('#AppointmentTable tbody').on('click', '.delete-btn', function() {
            var appointmentId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    showload();
                    $.ajax({
                        url: baseUrl + 'fetch/delete_appointment.php',
                        type: 'POST',
                        data: { id: appointmentId, userId: <?php echo $patient_id; ?> },
                        dataType: 'json',
                        success: function(response) {
                            hideload();
                            console.log('DeleteAppointment response:', response);
                            if (response.success) {
                                Swal.fire('Deleted!', 'The appointment has been deleted.', 'success');
                                appointmentTable.ajax.reload();
                            } else {
                                Swal.fire('Error!', response.message || 'Failed to delete the appointment.', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            hideload();
                            console.error('DeleteAppointment error:', xhr.responseText || error);
                            Swal.fire('Error!', 'Failed to delete the appointment. Please try again.', 'error');
                        }
                    });
                }
            });
        });

        // Load specializations for booking
        loadSpecializations('#DoctorSpecialization');

        // Load doctors when specialization changes (booking)
        $("#DoctorSpecialization").change(function() {
            var specialization = $(this).val();
            if (specialization) {
                loadDoctors(specialization, '#DoctorId', '#ConsultancyFees');
            } else {
                $("#DoctorId").html('<option value="">Select Doctor</option>');
                $("#ConsultancyFees").val('');
            }
        });

        // Load doctors when specialization changes (edit)
        $("#editDoctorSpecialization").change(function() {
            var specialization = $(this).val();
            if (specialization) {
                loadDoctors(specialization, '#editDoctorId', '#editConsultancyFees');
            } else {
                $("#editDoctorId").html('<option value="">Select Doctor</option>');
                $("#editConsultancyFees").val('');
            }
        });

        // Update consultancy fees when doctor changes (booking)
        $("#DoctorId").change(function() {
            var fees = $(this).find(':selected').data('fees') || '';
            $("#ConsultancyFees").val(fees);
        });

        // Update consultancy fees when doctor changes (edit)
        $("#editDoctorId").change(function() {
            var fees = $(this).find(':selected').data('fees') || '';
            $("#editConsultancyFees").val(fees);
        });

        // Initialize date picker
        $("#AppointmentDate, #editAppointmentDate").attr('min', new Date().toISOString().split('T')[0]);

        // Reset booking form when modal is closed
        $('#BookAppointmentModal').on('hidden.bs.modal', function() {
            $("#BookAppointmentForm")[0].reset();
            $("#ConsultancyFees").val('');
            $("#DoctorId").html('<option value="">Select Doctor</option>');
            $("#DoctorSpecialization").val('');
            $("#BookAppointmentForm").removeClass('was-validated');
        });

        // Reset edit form when modal is closed
        $('#EditAppointmentModal').on('hidden.bs.modal', function() {
            $("#EditAppointmentForm")[0].reset();
            $("#editConsultancyFees").val('');
            $("#editDoctorId").html('<option value="">Select Doctor</option>');
            $("#editDoctorSpecialization").val('');
            $("#EditAppointmentForm").removeClass('was-validated');
        });

        // Check patient status
        CheckData();

        // Initialize mobile vs desktop dropdown behavior
        var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        $('.navbar').navbarDropdown({ trigger: isMobile ? 'click' : 'mouseover' });
    });
    </script>
</body>
</html>