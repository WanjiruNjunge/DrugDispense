<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
    header("Location: ../authentication/login.php");
    exit;
}

// Check if the logged-in user has the necessary permission ('administrator')
if ($_SESSION['USER'] !== 'administrator' && $_SESSION['USER'] !== 'supervisor') {
    header("Location: ../errors/permission_denied.php");
    exit;
}

// Database configuration
require_once('../database.php');

// Check if the pharmaceuticalId is provided as a GET parameter
if (!isset($_GET['pharmaceuticalId'])) {
    header("Location: ../errors/not_found.php");
    exit;
}

$pharmaceuticalId = $_GET['pharmaceuticalId'];

// Create a database connection
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute the query to fetch pharmaceutical details
$query = "SELECT * FROM pharmaceutical WHERE pharmaceuticalId = '$pharmaceuticalId'";
$result = mysqli_query($connection, $query);

if (mysqli_num_rows($result) !== 1) {
    header("Location: ../errors/not_found.php");
    exit;
}

$pharmaceutical = mysqli_fetch_assoc($result);

// Prepare and execute the query to fetch associated supervisors
$query = "SELECT * FROM supervisor WHERE pharmaceuticalId = '$pharmaceuticalId'";
$supervisorResult = mysqli_query($connection, $query);

// Prepare and execute the query to fetch contracts and pharmacies associated with the pharmaceutical
$query = "SELECT contract.*, pharmacy.title AS pharmacy_name
          FROM contract
          INNER JOIN pharmacy ON contract.pharmacyId = pharmacy.pharmacyId
          WHERE contract.pharmaceuticalId = '$pharmaceuticalId'";
$contractResult = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pharmaceutical['title']; ?> Profile</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
        <h1 class="mb-4"><?php echo $pharmaceutical['title']; ?> Profile</h1>

        <!-- Pharmaceutical Details -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Pharmaceutical Details</h5>
                <p><strong>Pharmaceutical ID:</strong> <?php echo $pharmaceutical['pharmaceuticalId']; ?></p>
                <p><strong>Title:</strong> <?php echo $pharmaceutical['title']; ?></p>
                <p><strong>Location:</strong> <?php echo $pharmaceutical['location']; ?></p>
                <p><strong>Email:</strong> <a href="mailto:<?php echo $pharmaceutical['email']; ?>"><?php echo $pharmaceutical['email']; ?></a></p>
                <p><strong>Phone Number:</strong> <a href="tel:<?php echo $pharmaceutical['phoneNumber']; ?>"><?php echo $pharmaceutical['phoneNumber']; ?></a></p>
                <p><a href="../edit/edit_pharmaceutical.php?pharmaceuticalId=<?php echo $pharmaceuticalId; ?>">Edit Pharmaceutical</a></p>
            </div>
        </div>

        <!-- Associated Supervisors -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Associated Supervisors</h5>
                <?php if (mysqli_num_rows($supervisorResult) > 0) : ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Supervisor ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($supervisor = mysqli_fetch_assoc($supervisorResult)) : ?>
                                <tr>
                                    <td><a href="../profiles/supervisor.php?supervisorId=<?php echo $supervisor['supervisorId']; ?>"><?php echo $supervisor['supervisorId']; ?></a></td>
                                    <td><?php echo $supervisor['name']; ?></td>
                                    <td><a href="mailto:<?php echo $supervisor['email']; ?>"><?php echo $supervisor['email']; ?></a></td>
                                    <td><a href="tel:<?php echo $supervisor['phoneNumber']; ?>"><?php echo $supervisor['phoneNumber']; ?></a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>No associated supervisors found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contracts and Pharmacies -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Contracts and Pharmacies</h5>
                <?php if (mysqli_num_rows($contractResult) > 0) : ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Contract ID</th>
                                <th>Pharmacy</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($contract = mysqli_fetch_assoc($contractResult)) {
                                $contractId = $contract['contractId'];
                                $pharmacyName = $contract['pharmacy_name'];
                                $startDate = $contract['startDate'];
                                $endDate = $contract['endDate'];

                                // Convert dates to moment.js format
                                $formattedStartDate = date("Y-m-d", strtotime($startDate));
                                $formattedEndDate = date("Y-m-d", strtotime($endDate));

                                // Check if the contract is terminated or still active
                                $currentDate = date("Y-m-d");
                                $status = ($currentDate > $formattedEndDate) ? 'Terminated' : 'Active';
                                ?>
                                <tr>
                                    <td><a href="../profiles/contract.php?contractId=<?php echo $contractId; ?>"><?php echo $contractId; ?></a></td>
                                    <td><?php echo $pharmacyName; ?></td>
                                    <td><?php echo $formattedStartDate; ?></td>
                                    <td><?php echo $formattedEndDate; ?></td>
                                    <td><?php echo $status; ?></td>
                                    <td>
                                        <a href="../profiles/contract.php?contractId=<?php echo $contractId; ?>">View Contract</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>No contracts found for this pharmaceutical.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php echo $footer; ?>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
</body>
</html>
