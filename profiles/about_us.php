<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>About Us - Tiba Mara Moja Company</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <!-- About Us Section -->
    <section class="about-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>About Tiba Mara Moja Company</h2>
                    <p>Tiba Mara Moja Company is a leading provider of innovative technology solutions for drug dispensing and pharmacy management. Our mission is to make drug dispensing easier and more efficient for healthcare facilities, pharmacies, and patients alike.</p>
                    <p>With our state-of-the-art software system, we enable seamless communication between healthcare providers, pharmacists, and patients, streamlining the prescription process and ensuring timely and accurate medication dispensing.</p>
                    <p>At Tiba Mara Moja, we are committed to enhancing the healthcare industry by leveraging cutting-edge technology to improve patient care, optimize pharmacy operations, and support healthcare professionals in providing the best possible medical services.</p>
                </div>
                <div class="col-md-6">
                    <img src="../static/images/image_1.webp" alt="About Us Image" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission Section -->
    <section class="mission-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="../static/images/image_2.jpg" alt="Mission Image" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <h2>Our Mission</h2>
                    <p>Our mission is to revolutionize drug dispensing and pharmacy management by providing a user-friendly and efficient software system that empowers healthcare providers to deliver exceptional patient care.</p>
                    <p>We strive to create innovative solutions that optimize medication management, enhance patient safety, and improve pharmacy workflows, ultimately contributing to better health outcomes and overall patient satisfaction.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Vision Section -->
    <section class="vision-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Our Vision</h2>
                    <p>Our vision is to be a global leader in healthcare technology, recognized for our innovative solutions and commitment to advancing pharmacy management and drug dispensing practices.</p>
                    <p>We aim to transform the healthcare industry by continuously developing cutting-edge technologies that empower healthcare professionals and pharmacies to deliver superior patient care, reduce medication errors, and improve overall efficiency.</p>
                </div>
                <div class="col-md-6">
                    <!-- Add an image representing your vision -->
                    <img src="../static/images/image_3.png" alt="Vision Image" class="img-fluid">
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
