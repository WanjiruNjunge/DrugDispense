<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
	header("Location: ../authentication/login.php");
	exit;
}

// Check if the user is an administrator
if ($_SESSION['USER'] !== 'administrator' && $_SESSION['USER'] !== 'supervisor' &&
	$_SESSION['USER'] !== 'pharmacist') {
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

// Check if the drugId is provided in the URL
if (!isset($_GET['drugId'])) {
	header("Location: ../errors/404.php");
	exit;
}

$drugId = $_GET['drugId'];

// Retrieve the drug's existing data from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check the connection
if (!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute the query to fetch drug data
$query = "SELECT * FROM drug WHERE drugId = $drugId";
$result = mysqli_query($connection, $query);
$drug = mysqli_fetch_assoc($result);

// Check if the drug with the given ID exists in the database
if (!$drug) {
	mysqli_close($connection);
	header("Location: ../errors/404.php");
	exit;
}

// Define variables and set to existing drug's values
$scientificName = $commonName = $formula = $amount = $form = $expiryDate = $manufacturingDate = '';
$selectedContractId = $drug['contractId'];

// Process form submission when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Retrieve and sanitize form inputs
	$scientificName = sanitizeInput($_POST['scientificName']);
	$commonName = sanitizeInput($_POST['commonName']);
	$formula = sanitizeInput($_POST['formula']);
	$amount = sanitizeInput($_POST['amount']);
	$form = sanitizeInput($_POST['form']);
	$expiryDate = sanitizeInput($_POST['expiryDate']);
	$manufacturingDate = sanitizeInput($_POST['manufacturingDate']);

	// Create a database connection
	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	// Check the connection
	if (!$connection) {
		die("Connection failed: " . mysqli_connect_error());
	}

	// Prepare and execute the query to update the drug's data in the database
	$query = "UPDATE drug SET
		scientificName = '$scientificName',
		commonName = '$commonName',
		formula = '$formula',
		amount = '$amount',
		form = '$form',
		expiryDate = '$expiryDate',
		manufacturingDate = '$manufacturingDate'
		WHERE drugId = $drugId";

	if (mysqli_query($connection, $query)) {
		// Drug data updated successfully, redirect to contract.php with contractId
		mysqli_close($connection);
		header("Location: ../profiles/contract.php?contractId=$selectedContractId");
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
    <title>Edit Drug</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
	<h1 class="mb-4">Edit Drug</h1>

	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?drugId=' . $drugId); ?>">
	    <div class="mb-3">
		<label for="scientificName" class="form-label">Scientific Name:</label>
		<input type="text" id="scientificName" name="scientificName" value="<?php echo $drug['scientificName']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="commonName" class="form-label">Common Name:</label>
		<input type="text" id="commonName" name="commonName" value="<?php echo $drug['commonName']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="formula" class="form-label">Formula:</label>
		<input type="text" id="formula" name="formula" value="<?php echo $drug['formula']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="amount" class="form-label">Amount:</label>
		<input type="text" id="amount" name="amount" value="<?php echo $drug['amount']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="form" class="form-label">Form:</label>
		<input type="text" id="form" name="form" value="<?php echo $drug['form']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="expiryDate" class="form-label">Expiry Date:</label>
		<input type="date" id="expiryDate" name="expiryDate" value="<?php echo $drug['expiryDate']; ?>" class="form-control" required>
	    </div>

	    <div class="mb-3">
		<label for="manufacturingDate" class="form-label">Manufacturing Date:</label>
		<input type="date" id="manufacturingDate" name="manufacturingDate" value="<?php echo $drug['manufacturingDate']; ?>" class="form-control" required>
	    </div>

	    <button type="submit" class="btn btn-primary">Save Changes</button>
	</form>
    </div>
    <?php echo $footer; ?>
</body>
</html>
