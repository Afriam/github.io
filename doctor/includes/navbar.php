<div class="navbar navbar-expand-md navbar-dark bg-dark mb-4 fixed-top" role="navigation">
    <img id="img_navbar-brand" src="image/mylogo.jpg" width="3%" class="rounded-circle" alt="SLCB Logo">
    <a class="navbar-brand" href="#"> MP Doctor Appointment System</a>
    <img id="img_navbar-brand1" src="image/mylogo.jpg" width="10%" class="rounded-circle" alt="SLCB Logo">
    <a class="navbar-brand1" href="#"> MP DAS</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="DoctorDashboard.php">Dashboard
                    <span class="sr-only">(current)</span>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="dropdown1" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">Manage Patient</a>
                <ul class="dropdown-menu" aria-labelledby="dropdown1">
                    <li class="dropdown-item"><a id="text_deco" href="patients.php">Patients</a></li>
                    <li class="dropdown-item"><a id="text_deco" href="add_patient.php">Add Patient</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="dropdown2" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">Records</a>
                <ul class="dropdown-menu" aria-labelledby="dropdown2">
                    <li class="dropdown-item"><a id="text_deco" href="medical_records.php">Medical Records</a></li>
                    <li class="dropdown-item"><a id="text_deco" href="appointments.php">Appointments</a></li>
                </ul>
            </li>
        </ul>
        <div class="navbar-nav ml-auto">
            <div class="nav-item dropdown">
                <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle user-action">
                    <?php echo htmlspecialchars($doctor_name ?? 'Guest'); ?>
                    <b class="caret"></b>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="#" class="dropdown-item" data-toggle="modal" data-target="#ChangePasswordModal"><i class="fa fa-sliders"></i> Change Password</a>
                    <div class="dropdown-divider"></div>
                    <a href="/new Doc Appointment/logoutury.php" class="dropdown-item"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .dropdown-menu {
        background-color: #343a40; /* Darker background for contrast */
        border: 1px solid #495057;
        min-width: 12rem; /* Slightly wider dropdown */
    }
    .dropdown-menu-right {
        right: 0;
        left: auto; /* Aligns dropdown to the right */
    }
    .dropdown-item {
        color: #ffffff; /* White text for readability */
        padding: 0.5rem 1.5rem; /* Increased padding for spacing */
        transition: background-color 0.2s ease;
    }
    .dropdown-item:hover, .dropdown-item:focus {
        background-color: #495057; /* Slightly lighter hover effect */
        color: #ffffff;
        text-decoration: none;
    }
    .dropdown-divider {
        border-top: 1px solid #495057; /* Consistent divider color */
    }
    #text_deco {
        text-decoration: none !important;
        color: #ffffff;
    }
    #text_deco:hover {
        color: #e9ecef; /* Slightly lighter on hover */
    }
</style>