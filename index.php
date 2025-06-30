<?php
// Start the session
session_start();

// Include the database connection
include 'config/database.php';

// Fetch the number of doctors
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as doctor_count FROM doctors");
    $stmt->execute();
    $result = $stmt->get_result();
    $doctor_data = $result->fetch_assoc();
    $doctor_count = $doctor_data['doctor_count'] ?? 0;
    $stmt->close();
} catch (Exception $e) {
    error_log("Error fetching doctor count: " . $e->getMessage());
    $doctor_count = 0; // Fallback value
}

// Fetch the number of patients
try {
    $stmt = $conn->prepare("SELECT COUNT(*) as patient_count FROM patients");
    $stmt->execute();
    $result = $stmt->get_result();
    $patient_data = $result->fetch_assoc();
    $patient_count = $patient_data['patient_count'] ?? 0;
    $stmt->close();
} catch (Exception $e) {
    error_log("Error fetching patient count: " . $e->getMessage());
    $patient_count = 0; // Fallback value
}

// Close the database connection
$conn->close();

// Handle logout confirmation
$logout_message = '';
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    // Ensure the alert is shown only once by redirecting to clear the query parameter
    $logout_message = '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "success",
                title: "Logged Out",
                text: "You have been successfully logged out.",
                confirmButtonText: "Okay"
            }).then(() => {
                // Redirect to index.php without the logout parameter to prevent re-showing the alert on refresh
                window.history.replaceState({}, document.title, "index.php");
            });
        });
    </script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MP Doctor Appointment System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <!-- Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #28a745;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            position: relative;
        }

        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px 0;
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .nav-link {
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--secondary-color) !important;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 120px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') center/cover;
            opacity: 0.1;
        }

        .hero-section img {
            border-radius: 15px;
            max-height: 400px;
            object-fit: cover;
        }

        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }

        .appointment-section {
            background-color: white;
            padding: 80px 0;
        }

        .appointment-section img {
            border-radius: 15px;
            max-height: 350px;
            object-fit: cover;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .stats-section {
            background-color: var(--primary-color);
            color: white;
            padding: 80px 0;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--secondary-color);
            transition: opacity 0.3s ease;
        }

        .stat-number.loading {
            opacity: 0.5;
        }

        .testimonial-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
        }

        .testimonial-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 50px 0;
        }

        .social-icon {
            color: white;
            font-size: 1.5rem;
            margin: 0 10px;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .social-icon:hover {
            color: var(--secondary-color);
            transform: scale(1.2);
        }

        /* Back to Top Button */
        #back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            z-index: 1000;
        }

        #back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        #back-to-top:hover {
            background-color: var(--primary-color);
        }

        /* Fade-in animation for sections */
        .fade-in-section {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in-section.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-section {
                padding: 80px 0;
            }

            .hero-section img {
                margin-top: 30px;
                max-height: 300px;
            }

            .feature-card {
                margin: 15px 0;
            }

            .appointment-section {
                padding: 60px 0;
            }

            .appointment-section img {
                margin-top: 30px;
                max-height: 250px;
            }

            .stats-section {
                padding: 60px 0;
            }

            .stat-number {
                font-size: 2rem;
            }

            .testimonial-card {
                margin: 15px 0;
            }

            footer {
                padding: 30px 0;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.2rem;
            }

            .hero-section h1 {
                font-size: 2rem;
            }

            .hero-section p {
                font-size: 1rem;
            }

            .btn-primary {
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="image/mylogo.jpg" alt="MP DAS Logo" class="rounded-circle">
                MP Doctor Appointment System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#appointment">Book Appointment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section fade-in-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Your Health, Our Priority</h1>
                    <p class="lead mb-4">Book appointments with top doctors in your area. Fast, secure, and convenient healthcare access at your fingertips.</p>
                    <a href="login.php" class="btn btn-primary btn-lg">Get Started</a>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" 
                         alt="Healthcare" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5 fade-in-section">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Our System</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-calendar-check feature-icon"></i>
                        <h3>Easy Booking</h3>
                        <p>Schedule appointments with just a few clicks. Choose your preferred doctor and time slot.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-user-md feature-icon"></i>
                        <h3>Expert Doctors</h3>
                        <p>Access to a network of qualified and experienced healthcare professionals.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-shield-alt feature-icon"></i>
                        <h3>Secure Platform</h3>
                        <p>Your health information is protected with advanced security measures.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section fade-in-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="stat-number loading" id="doctor-count"><?php echo htmlspecialchars($doctor_count); ?></div>
                    <p>Doctors</p>
                </div>
                <div class="col-md-3">
                    <div class="stat-number loading" id="patient-count"><?php echo htmlspecialchars($patient_count); ?></div>
                    <p>Patients</p>
                </div>
                <div class="col-md-3">
                    <div class="stat-number">24/7</div>
                    <p>Support</p>
                </div>
                <div class="col-md-3">
                    <div class="stat-number">99%</div>
                    <p>Satisfaction</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Appointment Section -->
    <section id="appointment" class="appointment-section fade-in-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4">Book Your Appointment Today</h2>
                    <p class="lead mb-4">Take control of your health. Schedule an appointment with our expert doctors.</p>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Choose your preferred doctor</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Select convenient time slot</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Receive instant confirmation</li>
                    </ul>
                    <a href="login.php" class="btn btn-primary">Book Now</a>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" 
                         alt="Appointment" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5 fade-in-section">
        <div class="container">
            <h2 class="text-center mb-5">What Our Patients Say</h2>
            <div class="row">
                <?php if (!empty($testimonials)): ?>
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="col-md-4">
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?php echo htmlspecialchars($testimonial['image_url'] ?? 'https://via.placeholder.com/60'); ?>" alt="Patient" class="testimonial-img me-3">
                                    <div>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($testimonial['patient_name']); ?></h5>
                                        <small><?php echo htmlspecialchars($testimonial['patient_status']); ?></small>
                                    </div>
                                </div>
                                <p><?php echo htmlspecialchars($testimonial['testimonial_text']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>No testimonials available at this time. Check back later!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="fade-in-section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>MP Doctor Appointment System</h5>
                    <p>Providing accessible healthcare solutions for everyone.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-white">Features</a></li>
                        <li><a href="#appointment" class="text-white">Book Appointment</a></li>
                        <li><a href="#testimonials" class="text-white">Testimonials</a></li>
                        <li><a href="login.php" class="text-white">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <p>
                        <i class="fas fa-phone me-2"></i> +1 234 567 890<br>
                        <i class="fas fa-envelope me-2"></i> info@mpdas.com
                    </p>
                    <div class="mt-3">
                        <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4">
            <div class="text-center">
                <p class="mb-0">© 2025 MP Doctor Appointment System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" title="Back to Top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="js/sweetalert2.min.js"></script>
    <!-- Smooth Scroll and Animations -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Fade-in animation on scroll
        const fadeInSections = document.querySelectorAll('.fade-in-section');
        const observerOptions = {
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        fadeInSections.forEach(section => {
            observer.observe(section);
        });

        // Back to Top button functionality
        const backToTopButton = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });

        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Simulate loading for stats
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.querySelectorAll('.stat-number.loading').forEach(stat => {
                    stat.classList.remove('loading');
                });
            }, 500);
        });
    </script>
    <?php echo $logout_message; ?>
</body>
</html>