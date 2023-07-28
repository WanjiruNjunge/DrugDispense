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

// Check if the supervisorId is provided in the URL
if (!isset($_GET['supervisorId'])) {
	header("Location: ../errors/404.php");
	exit;
}

$supervisorId = $_GET['supervisorId'];

// Retrieve the supervisor's existing data from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute the query to fetch supervisor data
$query = "SELECT * FROM supervisor WHERE supervisorId = $supervisorId";
$result = mysqli_query($connection, $query);
$supervisor = mysqli_fetch_assoc($result);

// Check if the supervisor with the given ID exists in the database
if (!$supervisor) {
	mysqli_close($connection);
	header("Location: ../errors/404.php");
	exit;
}

// Define variables and set to existing supervisor's values
$name = $email = $phoneNumber = $gender = $selectedPharmaceuticalId = '';

// Process form submission when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Retrieve and sanitize form inputs
	$name = sanitizeInput($_POST['name']);
	$email = sanitizeInput($_POST['email']);
	$phoneNumber = sanitizeInput($_POST['phoneNumber']);
	$gender = sanitizeInput($_POST['gender']);

	// Create a database connection
	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	// Check the connection
	if (!$connection) {
		die("Connection failed: " . mysqli_connect_error());
	}

	// Prepare and execute the query to update the supervisor's data in the database
	$query = "UPDATE supervisor SET
		name = '$name',
		email = '$email',
		phoneNumber = '$phoneNumber',
		gender = '$gender'
		WHERE supervisorId = $supervisorId";

	if (mysqli_query($connection, $query)) {
		// Supervisor data updated successfully, redirect to supervisor.php
		mysqli_close($connection);
		header("Location: ../profiles/supervisor.php?supervisorId=$supervisorId");
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
    <title>Edit Supervisor</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
	<h1 class="mb-4">Edit Supervisor</h1>

	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?supervisorId=' . $supervisorId); ?>">
	    <div class="mb-3">
		<label for="name" class="form-label">Name:</label>
		<input type="text" id="name" name="name" value="<?php echo $supervisor['name']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="email" class="form-label">Email:</label>
		<input type="email" id="email" name="email" value="<?php echo $supervisor['email']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="phoneNumber" class="form-label">Phone Number:</label>
		<input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo $supervisor['phoneNumber']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="gender" class="form-label">Gender:</label>
		<select id="gender" name="gender" class="form-select" required>
		    <option value="">Select Gender</option>
		    <option value="Male" <?php if ($supervisor['gender'] === 'Male') echo 'selected'; ?>>Male</option>
		    <option value="Female" <?php if ($supervisor['gender'] === 'Female') echo 'selected'; ?>>Female</option>
		    <option value="Other" <?php if ($supervisor['gender'] === 'Other') echo 'selected'; ?>>Other</option>
		</select>
	    </div>

	    <button type="submit" class="btn btn-primary">Save Changes</button>
	</form>
    </div>
    <?php echo $footer; ?>
</body>
</html>
