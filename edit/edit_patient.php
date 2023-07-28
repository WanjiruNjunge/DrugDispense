<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
    header("Location: ../authentication/login.php");
    exit;
}

// Check if the logged-in user has the necessary permission ('administrator')
if ($_SESSION['USER'] !== 'administrator' && $_SESSION['USER'] !== 'patient') {
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

// Check if the patientId is provided in the URL
if (!isset($_GET['patientId'])) {
    header("Location: ../errors/404.php");
    exit;
}

$patientId = $_GET['patientId'];

// Retrieve the patient's existing data from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute the query to fetch patient data
$query = "SELECT * FROM patient WHERE patientId = $patientId";
$result = mysqli_query($connection, $query);
$patient = mysqli_fetch_assoc($result);

// Check if the patient with the given ID exists in the database
if (!$patient) {
    mysqli_close($connection);
    header("Location: ../errors/404.php");
    exit;
}

// Define variables and set to existing patient's values
$name = $patient['name'];
$email = $patient['email'];
$phoneNumber = $patient['phoneNumber'];
$ssn = $patient['ssn'];
$gender = $patient['gender'];
$passwordHash = $patient['passwordHash'];

// Process form submission when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phoneNumber = sanitizeInput($_POST['phoneNumber']);
    $ssn = sanitizeInput($_POST['ssn']);
    $gender = sanitizeInput($_POST['gender']);
    $passwordHash = password_hash(sanitizeInput($_POST['passwordHash'], PASSWORD_DEFAULT);

    // Create a database connection
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check the connection
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare and execute the query to update the patient's data in the database
    $query = "UPDATE patient SET 
              name = '$name',
              email = '$email',
              phoneNumber = '$phoneNumber',
              ssn = '$ssn',
              gender = '$gender',
              passwordHash = '$passwordHash'
              WHERE patientId = $patientId";

    if (mysqli_query($connection, $query)) {
        // Patient data updated successfully, redirect to patient.php with patientId
        mysqli_close($connection);
        header("Location: ../profiles/patient.php?patientId=$patientId");
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
    <title>Edit Patient</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
        <h1 class="mb-4">Edit Patient</h1>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?patientId=' . $patientId); ?>">
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
                <label for="ssn" class="form-label">SSN:</label>
                <input type="text" id="ssn" name="ssn" value="<?php echo $ssn; ?>" class="form-control" required>
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
                <label for="passwordHash" class="form-label">Password:</label>
                <input type="password" id="passwordHash" name="passwordHash" value="<?php echo $passwordHash; ?>" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
    <?php echo $footer; ?>
</body>
</html>
