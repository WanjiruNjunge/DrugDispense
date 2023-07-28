<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
    header("Location: ../authentication/login.php");
    exit;
}

// Check if the logged-in user has the necessary permission ('administrator')
if ($_SESSION['USER'] !== 'administrator' && $_SESSION['USER'] !== 'pharmacist') {
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

// Check if the pharmacistId is provided in the URL
if (!isset($_GET['pharmacistId'])) {
    header("Location: ../errors/404.php");
    exit;
}

$pharmacistId = $_GET['pharmacistId'];

// Retrieve the pharmacist's existing data from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute the query to fetch pharmacist data
$query = "SELECT * FROM pharmacist WHERE pharmacistId = $pharmacistId";
$result = mysqli_query($connection, $query);
$pharmacist = mysqli_fetch_assoc($result);

// Check if the pharmacist with the given ID exists in the database
if (!$pharmacist) {
    mysqli_close($connection);
    header("Location: ../errors/404.php");
    exit;
}

// Define variables and set to existing pharmacist's values
$name = $email = $phoneNumber = $imageUrl = '';
$selectedPharmacyId = $pharmacist['pharmacyId'];

// Process form submission when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phoneNumber = sanitizeInput($_POST['phoneNumber']);

    // File handling for pharmacist image upload
    if (isset($_FILES['pharmacistImage']) && $_FILES['pharmacistImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../static/images/users/pharmacists/';
        $fileName = basename($_FILES['pharmacistImage']['name']);
        $targetFile = $uploadDir . $fileName;
        $imageUrl = $fileName;

        // Move uploaded file to destination
        move_uploaded_file($_FILES['pharmacistImage']['tmp_name'], $targetFile);
    }

    // Create a database connection
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check the connection
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare and execute the query to update the pharmacist's data in the database
    $query = "UPDATE pharmacist SET 
              name = '$name',
              email = '$email',
              phoneNumber = '$phoneNumber',
              imageUrl = '$imageUrl',
              pharmacyId = $selectedPharmacyId
              WHERE pharmacistId = $pharmacistId";

    if (mysqli_query($connection, $query)) {
        // Pharmacist data updated successfully, redirect to pharmacist.php with pharmacistId
        mysqli_close($connection);
        header("Location: ../profiles/pharmacist.php?pharmacistId=$pharmacistId");
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
    <title>Edit Pharmacist</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
        <h1 class="mb-4">Edit Pharmacist</h1>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?pharmacistId=' . $pharmacistId); ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $pharmacist['name']; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $pharmacist['email']; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="phoneNumber" class="form-label">Phone Number:</label>
                <input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo $pharmacist['phoneNumber']; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="pharmacistImage" class="form-label">Pharmacist Image:</label>
                <input type="file" id="pharmacistImage" name="pharmacistImage" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label for="pharmacyId" class="form-label">Pharmacy:</label>
                <select id="pharmacyId" name="pharmacyId" class="form-select" required>
                    <option value="">Select Pharmacy</option>
                    <?php
                    // Fetch all pharmacies from the database
                    $query = "SELECT pharmacyId, title FROM pharmacy";
                    $result = mysqli_query($connection, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $pharmacyId = $row['pharmacyId'];
                        $title = $row['title'];
                        // Set the selected option if it matches the existing pharmacyId
                        $selected = ($pharmacyId == $selectedPharmacyId) ? 'selected' : '';
                        echo "<option value='$pharmacyId' $selected>$title</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
    <?php echo $footer; ?>
</body>
</html>
