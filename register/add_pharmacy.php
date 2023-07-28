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

// Define variables and set to empty values
$title = $location = $email = $phoneNumber = $imageUrl = '';

// Function to sanitize input data
function sanitizeInput($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

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

	// Prepare and execute the query to insert the pharmacy data into the database
	$query = "INSERT INTO pharmacy (title, location, email, phoneNumber)
		VALUES ('$title', '$location', '$email', '$phoneNumber')";

	if (mysqli_query($connection, $query)) {
		$successMessage = "Pharmacy added successfully!";
		// Clear form inputs after successful submission
		$title = $location = $email = $phoneNumber = '';
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
    <title>Add Pharmacy</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
	<h1 class="mb-4">Add Pharmacy</h1>

	<?php if (isset($successMessage)) : ?>
	    <div class="alert alert-success" role="alert">
		<?php echo $successMessage; ?>
	    </div>
	<?php endif; ?>

	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
	    <div class="mb-3">
		<label for="title" class="form-label">Title:</label>
		<input type="text" id="title" name="title" value="<?php echo $title; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="location" class="form-label">Location:</label>
		<input type="text" id="location" name="location" value="<?php echo $location; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="email" class="form-label">Email:</label>
		<input type="email" id="email" name="email" value="<?php echo $email; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="phoneNumber" class="form-label">Phone Number:</label>
		<input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo $phoneNumber; ?>" class="form-control" required>
	    </div>
	    <button type="submit" class="btn btn-primary">Submit</button>
	</form>
    </div>
    <?php echo $footer; ?>
</body>
</html>
