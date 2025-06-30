<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['patient_id']) || !isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

// Fetch user information
require_once 'config/database.php';
$patient_id = (int)$_SESSION['patient_id'];
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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - DAS SMS</title>
    <link rel="icon" href="image/mylogo.jpg" />

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>

    <!-- SweetAlert -->
    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <link rel="stylesheet" href="css/sweetalert_dark.css">
    <script src="js/sweetalert2.min.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- FullCalendar (if needed, though not used here) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>

    <!-- Select2 (if needed, though not used here) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

    <!-- jsPDF for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

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
        }
        .nv-dropdown-bs4 .dropdown-item:focus, .nv-dropdown-bs4 .dropdown-item:hover {
            color: #ffffff;
            text-decoration: none;
            background-color: #193d52;
        }
        .modal {
            overflow-y: auto;
        }
        @media (max-width: 1024px) {
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
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php" ?>

    <section id="content" style="margin: 20px; margin-top: 80px;">
        <div class="row">
            <div class="col-sm-12">
                <div class="card text-info bg-primary mb-12">
                    <div class="card-body">
                        <div class="card border-dark mb-12">
                            <div class="card-body text-dark">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="card-title" id="card-title">Patient Medical History</h3>
                                    </div>
                                    <hr />
                                </div>
                                <fieldset style="border: 1px solid #007bff !important; background: aliceblue;" class="border p-2">
                                    <legend class="w-auto"></legend>
                                    <table width='100%'>
                                        <tr>
                                            <td width="100"><strong>Name: </strong><font id="name"><?php echo $full_name; ?></font></td>
                                            <td width="100"><strong>Age:</strong> <font id="age"></font></td>
                                        </tr>
                                        <tr>
                                            <td colspan='2'><strong>Address: </strong><font id="patiendAddress"></font></td>
                                        </tr>
                                    </table>
                                    <div id="DIV_SOA"></div>
                                </fieldset>
                                <!-- Medical History Table -->
                                <div class="table-responsive mt-3">
                                    <table id="medicalHistoryTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Blood Pressure</th>
                                                <th>Blood Sugar</th>
                                                <th>Weight</th>
                                                <th>Temperature</th>
                                                <th>Medical Prescription</th>
                                                <th>Creation Date</th>
                                                <th>Actions</th>
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
    </section>

    <!-- View Medical History Modal -->
    <div id="viewMedicalHistoryModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Medical History Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="medicalHistoryDetails">
                        <p><strong>Blood Pressure:</strong> <span id="detailBloodPressure"></span></p>
                        <p><strong>Blood Sugar:</strong> <span id="detailBloodSugar"></span></p>
                        <p><strong>Weight:</strong> <span id="detailWeight"></span></p>
                        <p><strong>Temperature:</strong> <span id="detailTemperature"></span></p>
                        <p><strong>Medical Prescription:</strong> <span id="detailMedicalPres"></span></p>
                        <p><strong>Creation Date:</strong> <span id="detailCreationDate"></span></p>
                    </div>
                    <button class="btn btn-primary" id="downloadPDF">Download PDF</button>
                    <button class="btn btn-secondary" id="printRecord">Print</button>
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

    <!-- Scripts -->
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Password visibility toggles
        $("#CurrentPasswordButton").click(function () {
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

        $("#NewPasswordButton").click(function () {
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

        $("#VerifyPasswordButton").click(function () {
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

        // Initialize DataTable for Medical History and Fetch Patient Details
        $(document).ready(function () {
            // Get PatientID from PHP session
            var patientId = <?php echo isset($_SESSION['patient_id']) ? (int)$_SESSION['patient_id'] : 0; ?>;
            if (patientId <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid patient ID. Please log in.'
                });
                return;
            }

            // Fetch patient details
            $.ajax({
                url: 'fetch_patient_details.php?patient_id=' + patientId,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#name').text(response.full_name);
                        $('#age').text(response.age || 'N/A');
                        $('#patiendAddress').text(response.address || 'N/A');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error || 'Failed to load patient details.'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch patient details: ' + (xhr.responseText || error)
                    });
                }
            });

            // Initialize Medical History Table
            var table = $('#medicalHistoryTable').DataTable({
                "ajax": {
                    "url": "fetch_medical_history.php?patient_id=" + patientId,
                    "dataSrc": "",
                    "error": function (xhr, error, thrown) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load medical history records: ' + (xhr.responseText || thrown)
                        });
                    }
                },
                "columns": [
                    { "data": "BloodPressure", "defaultContent": "N/A" },
                    { "data": "BloodSugar", "defaultContent": "N/A" },
                    { "data": "Weight", "defaultContent": "N/A" },
                    { "data": "Temperature", "defaultContent": "N/A" },
                    { "data": "MedicalPres", "defaultContent": "N/A" },
                    { "data": "CreationDate" },
                    {
                        "data": null,
                        "render": function (data, type, row) {
                            return '<button class="btn btn-info btn-sm view-btn" data-id="' + row.ID + '">View</button> ' +
                                   '<button class="btn btn-danger btn-sm delete-btn" data-id="' + row.ID + '">Delete</button>';
                        }
                    }
                ],
                "pageLength": 10,
                "language": {
                    "emptyTable": "No medical history records found."
                }
            });


          

            // View Medical History
            $('#medicalHistoryTable tbody').on('click', '.view-btn', function () {
                var data = table.row($(this).parents('tr')).data();
                $('#detailBloodPressure').text(data.BloodPressure || 'N/A');
                $('#detailBloodSugar').text(data.BloodSugar || 'N/A');
                $('#detailWeight').text(data.Weight || 'N/A');
                $('#detailTemperature').text(data.Temperature || 'N/A');
                $('#detailMedicalPres').text(data.MedicalPres || 'N/A');
                $('#detailCreationDate').text(data.CreationDate || 'N/A');

                // Download PDF
                $('#downloadPDF').off('click').on('click', function () {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();

                    doc.setFontSize(16);
                    doc.text("Medical History Record", 20, 20);
                    doc.setFontSize(12);
                    doc.text("Blood Pressure: " + (data.BloodPressure || 'N/A'), 20, 40);
                    doc.text("Blood Sugar: " + (data.BloodSugar || 'N/A'), 20, 50);
                    doc.text("Weight: " + (data.Weight || 'N/A'), 20, 60);
                    doc.text("Temperature: " + (data.Temperature || 'N/A'), 20, 70);
                    doc.text("Medical Prescription: " + (data.MedicalPres || 'N/A'), 20, 80, { maxWidth: 170 });
                    doc.text("Creation Date: " + (data.CreationDate || 'N/A'), 20, 100);

                    doc.save('medical_history_' + data.ID + '.pdf');
                });

                // Print Record
                $('#printRecord').off('click').on('click', function () {
                    var printContents = $('#medicalHistoryDetails').html();
                    var newWin = window.open('', 'Print-Window');
                    newWin.document.open();
                    newWin.document.write('<html><body onload="window.print()">' + printContents + '</body></html>');
                    newWin.document.close();
                    setTimeout(function () { newWin.close(); }, 10);
                });

                $('#viewMedicalHistoryModal').modal('show');
            });

            // Delete Medical History
            $('#medicalHistoryTable tbody').on('click', '.delete-btn', function () {
                var recordId = $(this).data('id');
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
                        $.ajax({
                            url: 'fetch/delete_medical_history.php',
                            type: 'POST',
                            data: { id: recordId },
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', 'The record has been deleted.', 'success');
                                    table.ajax.reload();
                                } else {
                                    Swal.fire('Error!', response.error || 'Failed to delete the record.', 'error');
                                }
                            },
                            error: function (xhr, status, error) {
                                Swal.fire('Error!', 'Failed to delete the record: ' + (xhr.responseText || error), 'error');
                            }
                        });
                    }
                });
            });

            // Change Password Form Submission
            $("#ChangePasswordForm").submit(function (e) {
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
                            url: "fetch/change_password.php",
                            data: vForm.serialize(),
                            dataType: 'json',
                            success: function (response) {
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
                                        text: response.error || 'Current Password Incorrect'
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                hideload();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred: ' + (xhr.responseText || error)
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
            setTimeout(function () { Swal.close(); }, 2000);
        }
    </script>
</body>
</html>