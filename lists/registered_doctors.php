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

// Prepare and execute the query to fetch doctors data
$query = "SELECT * FROM doctor";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Doctors</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
        <h1 class="mb-4">Registered Doctors</h1>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Medical Certificate Number</th>
                    <th>Specialization</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $doctorId = $row['doctorId'];
                    $name = $row['name'];
                    $gender = $row['gender'];
                    $email = $row['email'];
                    $phoneNumber = $row['phoneNumber'];
                    $medicalCertificateNumber = $row['medicalCertificateNumber'];
                    $specialization = $row['specialization'];
                    ?>
                    <tr>
                        <td><?php echo $doctorId; ?></td>
                        <td><a href="../profiles/doctor.php?doctorId=<?php echo $doctorId; ?>"><?php echo $name; ?></a></td>
                        <td><?php echo $gender; ?></td>
                        <td><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></td>
                        <td><a href="tel:<?php echo $phoneNumber; ?>"><?php echo $phoneNumber; ?></a></td>
                        <td><?php echo $medicalCertificateNumber; ?></td>
                        <td><?php echo $specialization; ?></td>
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
