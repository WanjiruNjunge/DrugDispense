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

// Check if the pharmacyId is provided as a GET parameter
if (!isset($_GET['pharmacyId'])) {
    header("Location: ../errors/not_found.php");
    exit;
}

$pharmacyId = $_GET['pharmacyId'];

// Create a database connection
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute the query to fetch pharmacy details
$query = "SELECT * FROM pharmacy WHERE pharmacyId = '$pharmacyId'";
$result = mysqli_query($connection, $query);

if (mysqli_num_rows($result) !== 1) {
    header("Location: ../errors/not_found.php");
    exit;
}

$pharmacy = mysqli_fetch_assoc($result);

// Prepare and execute the query to fetch associated pharmacists
$query = "SELECT * FROM pharmacist WHERE pharmacyId = '$pharmacyId'";
$pharmacistResult = mysqli_query($connection, $query);

// Prepare and execute the query to fetch contracts and pharmaceuticals associated with the pharmacy
$query = "SELECT contract.*, pharmaceutical.title AS pharmaceutical_name
          FROM contract
          INNER JOIN pharmaceutical ON contract.pharmaceuticalId = pharmaceutical.pharmaceuticalId
          WHERE contract.pharmacyId = '$pharmacyId'";
$contractResult = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pharmacy['title']; ?> Profile</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
        <h1 class="mb-4"><?php echo $pharmacy['title']; ?> Profile</h1>

        <!-- Pharmacy Details -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Pharmacy Details</h5>
                <p><strong>Pharmacy ID:</strong> <?php echo $pharmacy['pharmacyId']; ?></p>
                <p><strong>Title:</strong> <?php echo $pharmacy['title']; ?></p>
                <p><strong>Location:</strong> <?php echo $pharmacy['location']; ?></p>
                <p><strong>Email:</strong> <a href="mailto:<?php echo $pharmacy['email']; ?>"><?php echo $pharmacy['email']; ?></a></p>
                <p><strong>Phone Number:</strong> <a href="tel:<?php echo $pharmacy['phoneNumber']; ?>"><?php echo $pharmacy['phoneNumber']; ?></a></p>
                <p><a href="../edit/edit_pharmacy.php?pharmacyId=<?php echo $pharmacyId; ?>">Edit Pharmacy</a></p>
            </div>
        </div>

        <!-- Associated Pharmacists -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Associated Pharmacists</h5>
                <?php if (mysqli_num_rows($pharmacistResult) > 0) : ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Pharmacist ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($pharmacist = mysqli_fetch_assoc($pharmacistResult)) : ?>
                                <tr>
                                    <td><a href="../profiles/pharmacist.php?pharmacistId=<?php echo $pharmacist['pharmacistId']; ?>"><?php echo $pharmacist['pharmacistId']; ?></a></td>
                                    <td><?php echo $pharmacist['name']; ?></td>
                                    <td><a href="mailto:<?php echo $pharmacist['email']; ?>"><?php echo $pharmacist['email']; ?></a></td>
                                    <td><a href="tel:<?php echo $pharmacist['phoneNumber']; ?>"><?php echo $pharmacist['phoneNumber']; ?></a></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>No associated pharmacists found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contracts and Pharmaceuticals -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Contracts and Pharmaceuticals</h5>
                <?php if (mysqli_num_rows($contractResult) > 0) : ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Contract ID</th>
                                <th>Pharmaceutical</th>
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
                                $pharmaceuticalName = $contract['pharmaceutical_name'];
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
                                    <td><?php echo $pharmaceuticalName; ?></td>
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
                    <p>No contracts found for this pharmacy.</p>
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
