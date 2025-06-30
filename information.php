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
$stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Information - DAS SMS</title>
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
                                <center>
                                    <h3>USER INFORMATION</h3>
                                </center>
                                <hr />

                                <div class="accordion" id="accordionExample">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <h2 class="mb-0">
                                                <button type="button" class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseOne">
                                                    <i class="fa fa-arrow-right"></i> Personal Information...
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <fieldset class="border p-2">
                                                    <legend class="w-auto">Personal Information</legend>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="FamilyName">Family Name:</label>
                                                                <input readOnly type="text" class="form-control" name="FamilyName" id="FamilyName" value="<?php echo htmlspecialchars($patient['family_name']); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="FirstName">First Name:</label>
                                                                <input readOnly type="text" class="form-control" name="FirstName" id="FirstName" value="<?php echo htmlspecialchars($patient['first_name']); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="MiddleName">Middle Name:</label>
                                                                <input readOnly type="text" class="form-control" name="MiddleName" id="MiddleName" value="<?php echo htmlspecialchars($patient['middle_name']); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="ExtName">Extension Name:</label>
                                                                <input readOnly type="text" class="form-control" name="ExtName" id="ExtName" value="<?php echo htmlspecialchars($patient['ext_name'] ?? ''); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="EmailAddress">Email Address:</label>
                                                                <input readOnly type="email" class="form-control" name="EmailAddress" id="EmailAddress" value="<?php echo htmlspecialchars($patient['email_address']); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="Username">Username:</label>
                                                                <input readOnly type="text" class="form-control" name="Username" id="Username" value="<?php echo htmlspecialchars($patient['username']); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div personally-identifiable-information class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="DOB">Date of Birth:</label>
                                                                <input readOnly type="date" class="form-control" name="DOB" id="DOB" value="<?php echo htmlspecialchars($patient['date_of_birth']); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="Age">Age:</label>
                                                                <input readOnly type="number" class="form-control" name="Age" id="Age" value="<?php echo htmlspecialchars($patient['age']); ?>">
                                                            </div>
                                                        </div>
                                                        <div personally-identifiable-information class="col-sm-8">
                                                            <div class="form-group">
                                                                <label for="POB">Place of Birth:</label>
                                                                <input readOnly type="text" class="form-control" name="POB" id="POB" value="<?php echo htmlspecialchars($patient['place_of_birth']); ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="HasDisability">Has Disability:</label>
                                                                <input readOnly type="text" class="form-control" name="HasDisability" id="HasDisability" value="<?php echo $patient['has_disability'] ? 'Yes' : 'No'; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="DisabilityType">Disability Type:</label>
                                                                <input readOnly type="text" class="form-control" name="DisabilityType" id="DisabilityType" value="<?php echo htmlspecialchars($patient['disability_type'] ?? 'N/A'); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="IsIndigenous">Indigenous Person:</label>
                                                                <input readOnly type="text" class="form-control" name="IsIndigenous" id="IsIndigenous" value="<?php echo $patient['is_indigenous'] ? 'Yes' : 'No'; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="IndigenousGroup">Indigenous Group:</label>
                                                                <input readOnly type="text" class="form-control" name="IndigenousGroup" id="IndigenousGroup" value="<?php echo htmlspecialchars($patient['indigenous_group'] ?? 'N/A'); ?>">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <fieldset class="border p-2">
                                                        <legend class="w-auto">Permanent Address:</legend>
                                                        <div class="row">
                                                            <div class="col-sm-2">
                                                                <div class="form-group">
                                                                    <label for="PermanentAddress_Region">Region:</label>
                                                                    <input readOnly type="text" class="form-control" name="PermanentAddress_Region" id="PermanentAddress_Region" value="<?php echo htmlspecialchars($patient['perm_region']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div class="form-group">
                                                                    <label for="PermanentAddress_Province">Province:</label>
                                                                    <input readOnly type="text" class="form-control" name="PermanentAddress_Province" id="PermanentAddress_Province" value="<?php echo htmlspecialchars($patient['perm_province']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div class="form-group">
                                                                    <label for="PermanentAddress_CityMunicipality">City/Municipality:</label>
                                                                    <input readOnly type="text" class="form-control" name="PermanentAddress_CityMunicipality" id="PermanentAddress_CityMunicipality" value="<?php echo htmlspecialchars($patient['perm_city_municipality']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div class="form-group">
                                                                    <label for="PermanentAddress_Barangay">Barangay:</label>
                                                                    <input readOnly type="text" class="form-control" name="PermanentAddress_Barangay" id="PermanentAddress_Barangay" value="<?php echo htmlspecialchars($patient['perm_barangay']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div class="form-group">
                                                                    <label for="PermanentAddress_Street">Street/Purok:</label>
                                                                    <input readOnly type="text" class="form-control" name="PermanentAddress_Street" id="PermanentAddress_Street" value="<?php echo htmlspecialchars($patient['perm_street']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <div class="form-group">
                                                                    <label for="PermanentAddress_ZipCode">Zip Code:</label>
                                                                    <input readOnly type="text" class="form-control" name="PermanentAddress_ZipCode" id="PermanentAddress_ZipCode" value="<?php echo htmlspecialchars($patient['perm_zip_code']); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>

                                                    <fieldset class="border p-2">
                                                        <legend class="w-auto">Home/Present Address:</legend>
                                                        <div class="row">
                                                            <div class="col-sm-2">
                                                                <div class="form-group">
                                                                    <label for="HomeAddress_Region">Region:</label>
                                                                    <input readOnly type="text" class="form-control" name="HomeAddress_Region" id="HomeAddress_Region" value="<?php echo htmlspecialchars($patient['home_region']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div class="form-group">
                                                                    <label for="HomeAddress_Province">Province:</label>
                                                                    <input readOnly type="text" class="form-control" name="HomeAddress_Province" id="HomeAddress_Province" value="<?php echo htmlspecialchars($patient['home_province']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div class="form-group">
                                                                    <label for="HomeAddress_CityMunicipality">City/Municipality:</label>
                                                                    <input readOnly type="text" class="form-control" name="HomeAddress_CityMunicipality" id="HomeAddress_CityMunicipality" value="<?php echo htmlspecialchars($patient['home_city_municipality']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div class="form-group">
                                                                    <label for="HomeAddress_Barangay">Barangay:</label>
                                                                    <input readOnly type="text" class="form-control" name="HomeAddress_Barangay" id="HomeAddress_Barangay" value="<?php echo htmlspecialchars($patient['home_barangay']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div class="form-group">
                                                                    <label for="HomeAddress_Street">Street/Purok:</label>
                                                                    <input readOnly type="text" class="form-control" name="HomeAddress_Street" id="HomeAddress_Street" value="<?php echo htmlspecialchars($patient['home_street']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-1">
                                                                <div class="form-group">
                                                                    <label for="HomeAddress_ZipCode">Zip Code:</label>
                                                                    <input readOnly type="text" class="form-control" name="HomeAddress_ZipCode" id="HomeAddress_ZipCode" value="<?php echo htmlspecialchars($patient['home_zip_code']); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>

                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="Sex">Sex:</label>
                                                                <input readOnly type="text" class="form-control" name="Sex" id="Sex" value="<?php echo htmlspecialchars($patient['sex']); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="CivilStatus">Civil Status:</label>
                                                                <input readOnly type="text" class="form-control" name="CivilStatus" id="CivilStatus" value="<?php echo htmlspecialchars($patient['civil_status']); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="Nationality">Nationality:</label>
                                                                <input readOnly type="text" class="form-control" name="Nationality" id="Nationality" value="<?php echo htmlspecialchars($patient['nationality']); ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="Spouse">Spouse Name (if married):</label>
                                                                <input readOnly type="text" class="form-control" name="Spouse" id="Spouse" value="<?php echo htmlspecialchars($patient['spouse_name'] ?? ''); ?>" <?php echo $patient['civil_status'] !== 'Married' ? 'disabled' : ''; ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'includes/footer.php' ?>
    </section>

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
        // Accordion toggle icons
        $(document).ready(function() {
            $(".collapse.show").each(function() {
                $(this).prev(".card-header").find(".fa").addClass("fa-arrow-down").removeClass("fa-arrow-right");
            });
            $(".collapse").on('show.bs.collapse', function() {
                $(this).prev(".card-header").find(".fa").removeClass("fa-arrow-right").addClass("fa-arrow-down");
            }).on('hide.bs.collapse', function() {
                $(this).prev(".card-header").find(".fa").removeClass("fa-arrow-down").addClass("fa-arrow-right");
            });

            // Mobile vs desktop dropdown behavior
            var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
            $('.navbar').navbarDropdown({ trigger: isMobile ? 'click' : 'mouseover' });

            // Initialize DataTables (if needed later)
            $('#example, #table2, #table3').DataTable();

            // Trigger change password modal
            $("#ChangePassword").click(function() {
                $("#ChangePasswordModal").modal("show");
            });
        });

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
            setTimeout(function() { Swal.close(); }, 2000);
        }

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
                e.preventDefault();
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
    </script>
</body>
</html>