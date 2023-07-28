<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Us - Tiba Mara Moja Company</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <!-- Contact Us Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Contact Us</h2>
                    <p>If you have any inquiries or need more information about our products and services, please don't hesitate to get in touch with us. Our dedicated team is here to assist you.</p>
                    <p>Feel free to use the contact details below or fill out the form to send us a message. We'll get back to you as soon as possible.</p>
                </div>
                <div class="col-md-6">
                    <h3>Contact Details</h3>
                    <p><i class="fas fa-envelope"></i> Email: info@tibamaramoja.com</p>
                    <p><i class="fas fa-phone"></i> Phone: +254 721 446 942</p>
                    <p><i class="fas fa-map-marker-alt"></i> Address: Ole Sangale - Keri Road, Nairobi West, Kenya</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-form-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <h3>Send Us a Message</h3>
                    <form action="contact_form_handler.php" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Your Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Your Message</label>
                            <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
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
