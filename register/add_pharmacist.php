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
$name = $email = $phoneNumber = $imageUrl = $password = $pharmacyId = '';

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
	$name = sanitizeInput($_POST['name']);
	$email = sanitizeInput($_POST['email']);
	$phoneNumber = sanitizeInput($_POST['phoneNumber']);
	$password = password_hash(sanitizeInput($_POST['password']), PASSWORD_DEFAULT);
	$pharmacyId = $_POST['pharmacyId'];

	// File handling for image upload
	if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
		$uploadDir = '../static/images/users/pharmacists/';
		$fileName = basename($_FILES['profileImage']['name']);
		$targetFile = $uploadDir . $fileName;
		$imageUrl = '../static/images/users/pharmacists/' . $fileName;

		// Move uploaded file to destination
		move_uploaded_file($_FILES['profileImage']['tmp_name'], $targetFile);
	}

	// Create a database connection
	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	// Check the connection
	if (!$connection) {
		die("Connection failed: " . mysqli_connect_error());
	}

	// Prepare and execute the query to insert the pharmacist data into the database
	$query = "INSERT INTO pharmacist (name, email, phoneNumber, imageUrl, passwordhash, pharmacyId)
		VALUES ('$name', '$email', '$phoneNumber', '$imageUrl', '$password', '$pharmacyId')";

	if (mysqli_query($connection, $query)) {
		$successMessage = "Pharmacist added successfully!";
		// Clear form inputs after successful submission
		$name = $email = $phoneNumber = $imageUrl = $password = '';
	} else {
		echo "Error: " . $query . "<br>" . mysqli_error($connection);
	}

	// Close the database connection
	mysqli_close($connection);
}

// Get the list of all registered pharmacies for the select field
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$query = "SELECT pharmacyId, title FROM pharmacy";
$result = mysqli_query($connection, $query);
$pharmacies = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($connection);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Pharmacist</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
	<h1 class="mb-4">Add Pharmacist</h1>

	<?php if (isset($successMessage)) : ?>
	    <div class="alert alert-success" role="alert">
		<?php echo $successMessage; ?>
	    </div>
	<?php endif; ?>

	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
	    <div class="mb-3">
		<label for="name" class="form-label">Name:</label>
		<input type="text" id="name" name="name" value="<?php echo $name; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="email" class="form-label">Email:</label>
		<input type="email" id="email" name="email" value="<?php echo $email; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="phoneNumber" class="form-label">Phone Number:</label>
		<input type="text" id="phoneNumber" name="phoneNumber" value="<?php echo $phoneNumber; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="password" class="form-label">Password:</label>
		<input type="password" id="password" name="password" value="<?php echo $password; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="pharmacyId" class="form-label">Pharmacy:</label>
		<select id="pharmacyId" name="pharmacyId" class="form-select" required>
		    <option value="" disabled selected>Select Pharmacy</option>
		    <?php foreach ($pharmacies as $pharmacy) : ?>
			<option value="<?php echo $pharmacy['pharmacyId']; ?>"><?php echo $pharmacy['title']; ?></option>
		    <?php endforeach; ?>
		</select>
	    </div>

	    <div class="mb-3">
		<label for="profileImage" class="form-label">Profile Image:</label>
		<input type="file" id="profileImage" name="profileImage" class="form-control" required>
	    </div>

	    <button type="submit" class="btn btn-primary">Submit</button>
	</form>
    </div>
    <?php echo $footer; ?>
</body>
</html>
