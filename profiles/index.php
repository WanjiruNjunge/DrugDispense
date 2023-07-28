<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tiba Mara Moja Company</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1>Welcome to Tiba Mara Moja Company</h1>
            <p>Your Health, Our Priority</p>
            <a href="#" class="btn btn-primary">Learn More</a>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container text-center">
            <h2>Experience Quality Healthcare</h2>
            <p>Discover the best medical services tailored to your needs.</p>
            <a href="#" class="btn btn-success">Get Started</a>
        </div>
    </section>

    <!-- Services Offered Section -->
    <section class="services-section">
        <div class="container text-center">
            <h2>Our Services</h2>
            <div class="row">
                <div class="col-md-4">
                    <i class="fas fa-hospital-alt fa-3x mb-3"></i>
                    <h4>Medical Care</h4>
                    <p>Comprehensive medical care provided by experienced professionals.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-stethoscope fa-3x mb-3"></i>
                    <h4>Health Checkups</h4>
                    <p>Regular health checkups to keep you in the best shape.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-pills fa-3x mb-3"></i>
                    <h4>Pharmacy Services</h4>
                    <p>Efficient pharmacy services for all your medication needs.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Our Application Section -->
    <section class="why-choose-section">
        <div class="container">
            <h2>Why Choose Us</h2>
            <div class="row">
                <div class="col-md-6">
                    <h4>Quality Healthcare</h4>
                    <p>We prioritize your health and provide top-quality medical services.</p>
                </div>
                <div class="col-md-6">
                    <h4>Experienced Professionals</h4>
                    <p>Our team of experienced doctors and healthcare providers ensures the best care for you.</p>
                </div>
                <div class="col-md-6">
                    <h4>Personalized Treatment</h4>
                    <p>We offer personalized treatment plans tailored to your specific needs.</p>
                </div>
                <div class="col-md-6">
                    <h4>State-of-the-art Facilities</h4>
                    <p>Our modern facilities are equipped with advanced medical technology.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits of Using Our Application Section -->
    <section class="benefits-section">
        <div class="container text-center">
            <h2>Benefits of Using Our Application</h2>
            <div class="row">
                <div class="col-md-4">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h4>Convenient Access</h4>
                    <p>Access your health records and appointments anytime, anywhere.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-heartbeat fa-3x mb-3"></i>
                    <h4>Health Monitoring</h4>
                    <p>Monitor your health and track your progress with our app.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-clock fa-3x mb-3"></i>
                    <h4>Time-saving</h4>
                    <p>Save time by booking appointments and managing prescriptions online.</p>
                </div>
            </div>
        </div>
    </section>
    <?php echo $footer; ?>

    <!-- Include Bootstrap and Font Awesome JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>
