<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
	header("Location: ../authentication/login.php");
	exit;
}

// Check if the logged-in user has the necessary permission ('administrator')
if ($_SESSION['USER'] !== 'administrator' && $_SESSION['USER'] !== 'patient') {
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

// Check if the pharmacyId is provided in the URL
if (!isset($_GET['pharmacyId'])) {
	header("Location: ../errors/404.php");
	exit;
}

$pharmacyId = $_GET['pharmacyId'];

// Retrieve the pharmacy's existing data from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute the query to fetch pharmacy data
$query = "SELECT * FROM pharmacy WHERE pharmacyId = $pharmacyId";
$result = mysqli_query($connection, $query);
$pharmacy = mysqli_fetch_assoc($result);

// Check if the pharmacy with the given ID exists in the database
if (!$pharmacy) {
	mysqli_close($connection);
	header("Location: ../errors/404.php");
	exit;
}

// Define variables and set to existing pharmacy's values
$title = $location = $email = $phoneNumber = '';

// Process form submission when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Retrieve and sanitize form inputs
	$title = sanitizeInput($_POST['title']);
	$location = sanitizeInput($_POST['location']);
	$email = sanitizeInput($_POST['email']);
	$phoneNumber = sanitizeInput($_POST['phoneNumber']);

	// Create a database connection
	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	// Check the connection
	if (!$connection) {
		die("Connection failed: " . mysqli_connect_error());
	}

	// Prepare and execute the query to update the pharmacy's data in the database
	$query = "UPDATE pharmacy SET
		title = '$title',
		location = '$location',
		email = '$email',
		phoneNumber = '$phoneNumber'
		WHERE pharmacyId = $pharmacyId";

	if (mysqli_query($connection, $query)) {
		// Pharmacy data updated successfully, redirect to pharmacy.php with pharmacyId
		mysqli_close($connection);
		header("Location: ../profiles/pharmacy.php?pharmacyId=$pharmacyId");
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
    <title>Edit Pharmacy</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
	<h1 class="mb-4">Edit Pharmacy</h1>

	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?pharmacyId=' . $pharmacyId); ?>" enctype="multipart/form-data">
	    <div class="mb-3">
		<label for="title" class="form-label">Title:</label>
		<input type="text" id="title" name="title" value="<?php echo $pharmacy['title']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="location" class="form-label">Location:</label>
		<input type="text" id="location" name="location" value="<?php echo $pharmacy['location']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="email" class="form-label">Email:</label>
		<input type="email" id="email" name="email" value="<?php echo $pharmacy['email']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="phoneNumber" class="form-label">Phone Number:</label>
		<input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo $pharmacy['phoneNumber']; ?>" class="form-control" required>
	    </div>
	    <button type="submit" class="btn btn-primary">Save Changes</button>
	</form>
    </div>
    <?php echo $footer; ?>
</body>
</html>
