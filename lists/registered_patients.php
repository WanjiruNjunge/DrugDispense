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

// Calculate pagination variables
$itemsPerPage = 10; // Number of items to display per page
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_from = ($current_page - 1) * $itemsPerPage;

// Prepare and execute the query to fetch patients data with pagination
$query = "SELECT * FROM patient LIMIT $start_from, $itemsPerPage";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Patients</title>
    <!-- Include Bootstrap CSS and Moment.js -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/min/moment.min.js"></script>
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
	<h1 class="mb-4">Registered Patients</h1>

	<table class="table table-bordered">
	    <thead>
		<tr>
		    <th>ID</th>
		    <th>Name</th>
		    <th>Email</th>
		    <th>Phone Number</th>
		    <th>Date of Birth</th>
		    <th>Age</th>
		</tr>
	    </thead>
	    <tbody>
<?php
while ($row = mysqli_fetch_assoc($result)) {
	$patientId = $row['patientId'];
	$name = $row['name'];
	$email = $row['email'];
	$phoneNumber = $row['phoneNumber'];
	$dateOfBirth = $row['dateOfBirth'];

	// Calculate age using Moment.js
	$dob = new DateTime($dateOfBirth);
	$now = new DateTime();
	$age = $dob->diff($now)->y;
?>
		    <tr>
			<td><?php echo $patientId; ?></td>
			<td><?php echo $name; ?></td>
			<td><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></td>
			<td><a href="tel:<?php echo $phoneNumber; ?>"><?php echo $phoneNumber; ?></a></td>
			<td><?php echo $dateOfBirth; ?></td>
			<td><?php echo $age; ?> years</td>
		    </tr>
<?php
}
?>
	    </tbody>
	</table>

	<!-- Pagination links -->
	<nav aria-label="Page navigation">
	    <ul class="pagination">
<?php
// Count total patients in the database
$count_query = "SELECT COUNT(*) AS total FROM patient";
$count_result = mysqli_query($connection, $count_query);
$total_items = mysqli_fetch_assoc($count_result)['total'];

// Calculate total number of pages
$total_pages = ceil($total_items / $itemsPerPage);

// Display pagination links
for ($i = 1; $i <= $total_pages; $i++) {
	echo '<li class="page-item' . ($i === $current_page ? ' active' : '') . '"><a class="page-link" href="registered_patients.php?page=' . $i . '">' . $i . '</a></li>';
}
?>
	    </ul>
	</nav>
    </div>
    <?php echo $footer; ?>
</body>
</html>
<?php
// Close the database connection
mysqli_close($connection);
?>
