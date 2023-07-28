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

// Function to sanitize input data
function sanitizeInput($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

// Check if the pharmaceuticalId is provided in the URL
if (!isset($_GET['pharmaceuticalId'])) {
	header("Location: ../errors/404.php");
	exit;
}

$pharmaceuticalId = $_GET['pharmaceuticalId'];

// Retrieve the pharmaceutical's existing data from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute the query to fetch pharmaceutical data
$query = "SELECT * FROM pharmaceutical WHERE pharmaceuticalId = $pharmaceuticalId";
$result = mysqli_query($connection, $query);
$pharmaceutical = mysqli_fetch_assoc($result);

// Check if the pharmaceutical with the given ID exists in the database
if (!$pharmaceutical) {
	mysqli_close($connection);
	header("Location: ../errors/404.php");
	exit;
}

// Define variables and set to existing pharmaceutical's values
$title = $location = $email = $phoneNumber = $pharmaceuticalImage = '';

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

	// Prepare and execute the query to update the pharmaceutical's data in the database
	$query = "UPDATE pharmaceutical SET
		title = '$title',
		location = '$location',
		email = '$email',
		phoneNumber = '$phoneNumber'
		WHERE pharmaceuticalId = $pharmaceuticalId";

	if (mysqli_query($connection, $query)) {
		// Pharmaceutical data updated successfully, redirect to pharmaceutical.php with pharmaceuticalId
		mysqli_close($connection);
		header("Location: ../profiles/pharmaceutical.php?pharmaceuticalId=$pharmaceuticalId");
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
    <title>Edit Pharmaceutical</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
	<h1 class="mb-4">Edit Pharmaceutical</h1>

	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?pharmaceuticalId=' . $pharmaceuticalId); ?>" enctype="multipart/form-data">
	    <div class="mb-3">
		<label for="title" class="form-label">Title:</label>
		<input type="text" id="title" name="title" value="<?php echo $pharmaceutical['title']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="location" class="form-label">Location:</label>
		<input type="text" id="location" name="location" value="<?php echo $pharmaceutical['location']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="email" class="form-label">Email:</label>
		<input type="email" id="email" name="email" value="<?php echo $pharmaceutical['email']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="phoneNumber" class="form-label">Phone Number:</label>
		<input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo $pharmaceutical['phoneNumber']; ?>" class="form-control" required>
	    </div>

	    <button type="submit" class="btn btn-primary">Save Changes</button>
	</form>
    </div>
    <?php echo $footer; ?>
</body>
</html>
