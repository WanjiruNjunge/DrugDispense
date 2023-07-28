<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
    header("Location: ../authentication/login.php");
    exit;
}

// Check if the logged-in user has the necessary permission ('administrator')
if ($_SESSION['USER'] !== 'administrator' && $_SESSION['USER'] !== 'doctor') {
    header("Location: ../errors/permission_denied.php");
    exit;
}

// Database configuration
require_once('../database.php');

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if the doctorId is provided in the URL
if (!isset($_GET['doctorId'])) {
    header("Location: ../errors/404.php");
    exit;
}

$doctorId = $_GET['doctorId'];

// Retrieve the doctor's existing data from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute the query to fetch doctor data
$query = "SELECT * FROM doctor WHERE doctorId = $doctorId";
$result = mysqli_query($connection, $query);
$doctor = mysqli_fetch_assoc($result);

// Check if the doctor with the given ID exists in the database
if (!$doctor) {
    mysqli_close($connection);
    header("Location: ../errors/404.php");
    exit;
}

// Define variables and set to existing doctor's values
$name = $doctor['name'];
$email = $doctor['email'];
$phoneNumber = $doctor['phoneNumber'];
$gender = $doctor['gender'];
$imageUrl = $doctor['imageUrl'];
$medicalCertificateNumber = $doctor['medicalCertificateNumber'];
$specialization = $doctor['specialization'];
$passwordHash = $doctor['passwordHash'];

// Process form submission when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phoneNumber = sanitizeInput($_POST['phoneNumber']);
    $gender = sanitizeInput($_POST['gender']);
    $medicalCertificateNumber = sanitizeInput($_POST['medicalCertificateNumber']);
    $specialization = sanitizeInput($_POST['specialization']);
    $passwordHash = password_hash(sanitizeInput($_POST['passwordHash']), PASSWORD_DEFAULT);

    // Create a database connection
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check the connection
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare and execute the query to update the doctor's data in the database
    $query = "UPDATE doctor SET 
              name = '$name',
              email = '$email',
              phoneNumber = '$phoneNumber',
              gender = '$gender',
              medicalCertificateNumber = '$medicalCertificateNumber',
              specialization = '$specialization',
              passwordHash = '$passwordHash'
              WHERE doctorId = $doctorId";

    if (mysqli_query($connection, $query)) {
        // Doctor data updated successfully, redirect to doctor.php with doctorId
        mysqli_close($connection);
        header("Location: ../profiles/doctor.php?doctorId=$doctorId");
        exit;
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connection);
    }

    // Close the database connection
    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Doctor</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
        <h1 class="mb-4">Edit Doctor</h1>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?doctorId=' . $doctorId); ?>">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $name; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="phoneNumber" class="form-label">Phone Number:</label>
                <input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo $phoneNumber; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="gender" class="form-label">Gender:</label>
                <select id="gender" name="gender" class="form-select" required>
                    <option value="Male" <?php echo $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo $gender === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="medicalCertificateNumber" class="form-label">Medical Certificate Number:</label>
                <input type="text" id="medicalCertificateNumber" name="medicalCertificateNumber" value="<?php echo $medicalCertificateNumber; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="specialization" class="form-label">Specialization:</label>
                <input type="text" id="specialization" name="specialization" value="<?php echo $specialization; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="passwordHash" class="form-label">Password:</label>
                <input type="password" id="passwordHash" name="passwordHash" value="<?php echo $passwordHash; ?>" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
    <?php echo $footer; ?>
</body>
</html>
