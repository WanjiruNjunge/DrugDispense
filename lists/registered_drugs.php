<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
    header("Location: ../authentication/login.php");
    exit;
}

// Check if the logged-in user has the necessary permission ('administrator')
if ($_SESSION['USER'] !== 'administrator') {
    header("Location: ../errors/permission_denied.php");
    exit;
}

// Database configuration
require_once('../database.php');


// Create a database connection
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute the query to fetch drugs data along with the pharmaceutical and pharmacy information
$query = "SELECT drug.*, pharmaceutical.title AS pharmaceutical_name, pharmacy.title AS pharmacy_name
          FROM drug
          INNER JOIN contract ON drug.contractId = contract.contractId
          INNER JOIN pharmaceutical ON contract.pharmaceuticalId = pharmaceutical.pharmaceuticalId
          INNER JOIN pharmacy ON contract.pharmacyId = pharmacy.pharmacyId";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Drugs</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
        <h1 class="mb-4">All Drugs</h1>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Scientific Name</th>
                    <th>Common Name</th>
                    <th>Pharmaceutical</th>
                    <th>Pharmacy</th>
                    <th>Contract ID</th>
                    <th>Expiry Date</th>
                    <th>Manufacturing Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $drugId = $row['drugId'];
                    $scientificName = $row['scientificName'];
                    $commonName = $row['commonName'];
                    $pharmaceuticalName = $row['pharmaceutical_name'];
                    $pharmacyName = $row['pharmacy_name'];
                    $contractId = $row['contractId'];
                    $expiryDate = $row['expiryDate'];
                    $manufacturingDate = $row['manufacturingDate'];

                    // Convert dates to moment.js format
                    $formattedExpiryDate = date("Y-m-d", strtotime($expiryDate));
                    $formattedManufacturingDate = date("Y-m-d", strtotime($manufacturingDate));
                    ?>
                    <tr>
                        <td><?php echo $drugId; ?></td>
                        <td><a href="../edit/edit_drug.php?drugId=<?php echo $drugId; ?>"><?php echo $scientificName; ?></a></td>
                        <td><?php echo $commonName; ?></td>
                        <td><?php echo $pharmaceuticalName; ?></td>
                        <td><?php echo $pharmacyName; ?></td>
                        <td><?php echo $contractId; ?></td>
                        <td><?php echo $formattedExpiryDate; ?></td>
                        <td><?php echo $formattedManufacturingDate; ?></td>
                        <td>
                            <a href="../edit/edit_drug.php?drugId=<?php echo $drugId; ?>">Edit</a> |
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php echo $footer; ?>
</body>
</html>
<?php
// Close the database connection
mysqli_close($connection);
?>
