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

// Prepare and execute the query to fetch pharmacists data along with the pharmacy they belong to
$query = "SELECT pharmacist.*, pharmacy.title AS pharmacy_name
          FROM pharmacist
          INNER JOIN pharmacy ON pharmacist.pharmacyId = pharmacy.pharmacyId";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Pharmacists</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
        <h1 class="mb-4">Registered Pharmacists</h1>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Pharmacy</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $pharmacistId = $row['pharmacistId'];
                    $name = $row['name'];
                    $email = $row['email'];
                    $phoneNumber = $row['phoneNumber'];
                    $pharmacyName = $row['pharmacy_name'];
                    ?>
                    <tr>
                        <td><?php echo $pharmacistId; ?></td>
                        <td><a href="../profiles/pharmacist.php?pharmacistId=<?php echo $pharmacistId; ?>"><?php echo $name; ?></a></td>
                        <td><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></td>
                        <td><a href="tel:<?php echo $phoneNumber; ?>"><?php echo $phoneNumber; ?></a></td>
                        <td><a href="../profiles/pharmacy.php?pharmacyId=<?php echo $row['pharmacyId']; ?>"><?php echo $pharmacyName; ?></a></td>
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
