<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
    header("Location: ../authentication/login.php");
    exit;
}

// Check if the user is an administrator
if ($_SESSION['USER'] !== 'administrator' && $_SESSION['USER'] !== 'supervisor') {
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

// Check if the contractId is provided in the URL
if (!isset($_GET['contractId'])) {
    header("Location: ../errors/404.php");
    exit;
}

$contractId = $_GET['contractId'];

// Retrieve the contract's existing data from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute the query to fetch contract data
$query = "SELECT * FROM contract WHERE contractId = $contractId";
$result = mysqli_query($connection, $query);
$contract = mysqli_fetch_assoc($result);

// Check if the contract with the given ID exists in the database
if (!$contract) {
    mysqli_close($connection);
    header("Location: ../errors/404.php");
    exit;
}

// Define variables and set to existing contract's values
$dateCreated = $lastUpdated = $startDate = $endDate = '';
$selectedPharmacyId = $contract['pharmacyId'];
$selectedPharmaceuticalId = $contract['pharmaceuticalId'];

// Process form submission when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $dateCreated = sanitizeInput($_POST['dateCreated']);
    $lastUpdated = sanitizeInput($_POST['lastUpdated']);
    $startDate = sanitizeInput($_POST['startDate']);
    $endDate = sanitizeInput($_POST['endDate']);

    // Create a database connection
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    // Check the connection
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare and execute the query to update the contract's data in the database
    $query = "UPDATE contract SET 
              dateCreated = '$dateCreated',
              lastUpdated = '$lastUpdated',
              startDate = '$startDate',
              endDate = '$endDate',
              pharmacyId = $selectedPharmacyId,
              pharmaceuticalId = $selectedPharmaceuticalId
              WHERE contractId = $contractId";

    if (mysqli_query($connection, $query)) {
        // Contract data updated successfully, redirect to contract.php with contractId
        mysqli_close($connection);
        header("Location: ../profiles/contract.php?contractId=$contractId");
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
    <title>Edit Contract</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
        <h1 class="mb-4">Edit Contract</h1>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?contractId=' . $contractId); ?>">
            <div class="mb-3">
                <label for="dateCreated" class="form-label">Date Created:</label>
                <input type="date" id="dateCreated" name="dateCreated" value="<?php echo $contract['dateCreated']; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="lastUpdated" class="form-label">Last Updated:</label>
                <input type="date" id="lastUpdated" name="lastUpdated" value="<?php echo $contract['lastUpdated']; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="startDate" class="form-label">Start Date:</label>
                <input type="date" id="startDate" name="startDate" value="<?php echo $contract['startDate']; ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="endDate" class="form-label">End Date:</label>
                <input type="date" id="endDate" name="endDate" value="<?php echo $contract['endDate']; ?>" class="form-control" required>
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

            <div class="mb-3">
                <label for="pharmaceuticalId" class="form-label">Pharmaceutical:</label>
                <select id="pharmaceuticalId" name="pharmaceuticalId" class="form-select" required>
                    <option value="">Select Pharmaceutical</option>
                    <?php
                    // Fetch all pharmaceuticals from the database
                    $query = "SELECT pharmaceuticalId, title FROM pharmaceutical";
                    $result = mysqli_query($connection, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $pharmaceuticalId = $row['pharmaceuticalId'];
                        $title = $row['title'];
                        // Set the selected option if it matches the existing pharmaceuticalId
                        $selected = ($pharmaceuticalId == $selectedPharmaceuticalId) ? 'selected' : '';
                        echo "<option value='$pharmaceuticalId' $selected>$title</option>";
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
