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

// Prepare and execute the query to fetch contracts data along with the pharmacy and pharmaceutical information
$query = "SELECT contract.*, pharmacy.title AS pharmacy_name, pharmaceutical.title AS pharmaceutical_name
	FROM contract
	INNER JOIN pharmacy ON contract.pharmacyId = pharmacy.pharmacyId
	INNER JOIN pharmaceutical ON contract.pharmaceuticalId = pharmaceutical.pharmaceuticalId";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Contracts</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
	<h1 class="mb-4">Registered Contracts</h1>

	<table class="table table-bordered">
	    <thead>
		<tr>
		    <th>ID</th>
		    <th>Pharmacy</th>
		    <th>Pharmaceutical</th>
		    <th>Start Date</th>
		    <th>End Date</th>
		    <th>Status</th>
		</tr>
	    </thead>
	    <tbody>
<?php
while ($row = mysqli_fetch_assoc($result)) {
	$contractId = $row['contractId'];
	$pharmacyName = $row['pharmacy_name'];
	$pharmaceuticalName = $row['pharmaceutical_name'];
	$startDate = $row['startDate'];
	$endDate = $row['endDate'];

	// Convert dates to moment.js format
	$formattedStartDate = date("Y-m-d", strtotime($startDate));
	$formattedEndDate = date("Y-m-d", strtotime($endDate));

	// Check if the contract is terminated or still active
	$currentDate = date("Y-m-d");
	$status = ($currentDate > $formattedEndDate) ? 'Terminated' : 'Active';
?>
		    <tr>
			<td><a href="../profiles/contract.php?contractId=<?php echo $contractId; ?>"><?php echo $contractId; ?></a></td>
			<td><a href="../profiles/pharmacy.php?pharmacyId=<?php echo $row['pharmacyId']; ?>"><?php echo $pharmacyName; ?></a></td>
			<td><a href="../profiles/pharmaceutical.php?pharmaceuticalId=<?php echo $row['pharmaceuticalId']; ?>"><?php echo $pharmaceuticalName; ?></a></td>
			<td><?php echo $formattedStartDate; ?></td>
			<td><?php echo $formattedEndDate; ?></td>
			<td><?php echo $status; ?></td>
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
