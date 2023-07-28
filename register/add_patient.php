<?php
require_once('../database.php');

// Define variables and set to empty values
$name = $email = $phoneNumber = $ssn = $gender = $dateOfBirth = $password = '';
$successMessage = '';

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Process form submission when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phoneNumber = sanitizeInput($_POST['phoneNumber']);
    $ssn = sanitizeInput($_POST['ssn']);
    $gender = sanitizeInput($_POST['gender']);
    $dateOfBirth = sanitizeInput($_POST['dateOfBirth']);
    $password = password_hash(sanitizeInput($_POST['password']), PASSWORD_DEFAULT);

    // File handling for image upload
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../static/images/users/patients/';
        $fileName = basename($_FILES['profileImage']['name']);
        $targetFile = $uploadDir . $fileName;
        $imageUrl = $fileName;

        // Move uploaded file to destination
        move_uploaded_file($_FILES['profileImage']['tmp_name'], $targetFile);
    }

    // Create a database connection
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check the connection
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare and execute the query to insert the patient data into the database
    $query = "INSERT INTO patient (name, email, phoneNumber, ssn, gender, dateOfBirth, passwordHash)
              VALUES ('$imageUrl''$name', '$email', '$phoneNumber', '$ssn', '$gender', '$dateOfBirth', '$password')";

    if (mysqli_query($connection, $query)) {
        $successMessage = "Patient added successfully!";
        // Clear form inputs after successful submission
        $name = $email = $imageUrl = $phoneNumber = $ssn = $gender = $dateOfBirth = $password = '';
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
    <title>Add Patient</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
        <h1 class="mb-4">Add Patient</h1>

        <?php if (!empty($successMessage)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
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
                    <option value="">Select Gender</option>
                    <option value="Male" <?php if ($gender === 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($gender === 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($gender === 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="dateOfBirth" class="form-label">Date of Birth:</label>
                <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo $dateOfBirth; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" value="<?php echo $password; ?>" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <?php echo $footer; ?>
</body>
</html>
