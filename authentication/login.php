<?php
session_start();

// Database configuration
require_once('../database.php');

// Initialize variables
$email = '';
$password = '';
$userType = '';

// Function to sanitize input data
function sanitizeInput($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

// Function to redirect to a specific profile page based on user type
function redirectToProfile($userType, $userId) {
	$profilePage = '../profiles/' . $userType . '.php?' . $userType . 'Id=' . $userId;
	header('Location: ' . $profilePage);
	exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Retrieve and sanitize form inputs
	$email = sanitizeInput($_POST['email']);
	$password = sanitizeInput($_POST['password']);
	$userType = sanitizeInput($_POST['userType']);

	// Create a database connection
	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	// Check the connection
	if (!$connection) {
		die("Connection failed: " . mysqli_connect_error());
	}

	// Query the specific table based on user type
	$tableName = '';
	switch ($userType) {
	case 'administrator':
		$tableName = 'administrator';
		break;
	case 'pharmacist':
		$tableName = 'pharmacist';
		break;
	case 'pharmaceutical':
		$tableName = 'pharmaceutical';
		break;
	case 'supervisor':
		$tableName = 'supervisor';
		break;
	case 'patient':
		$tableName = 'patient';
		break;
	case 'doctor':
		$tableName = 'doctor';
		break;
	default:
		// Handle invalid user type here (optional)
		break;
	}

	// Prepare and execute the query to check login credentials
	$query = "SELECT * FROM $tableName WHERE email = '$email'";
	$result = mysqli_query($connection, $query);

	if ($result && mysqli_num_rows($result) > 0) {
		// Login successful, set session variables and redirect to profile
		$row = mysqli_fetch_assoc($result);
		if (password_verify($password, $row['passwordHash'])) {
			$_SESSION['USER'] = $userType;
			$_SESSION['USER_ID'] = $row[$userType . 'Id'];
			redirectToProfile($userType, $row[$userType . 'Id']);
		}
	} else {
		// Login failed, display an error message (optional)
		$errorMessage = "Invalid email or password.";
	}

	// Close the database connection
	mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
	<h1>Login</h1>
	<?php if (isset($errorMessage)) : ?>
	    <div class="alert alert-danger" role="alert">
		<?php echo $errorMessage; ?>
	    </div>
	<?php endif; ?>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
	    <div class="mb-3">
		<label for="email" class="form-label">Email:</label>
		<input type="email" id="email" name="email" class="form-control" required>
	    </div>
	    <div class="mb-3">
		<label for="password" class="form-label">Password:</label>
		<input type="password" id="password" name="password" class="form-control" required>
	    </div>
	    <div class="mb-3">
		<label for="userType" class="form-label">User Type:</label>
		<select id="userType" name="userType" class="form-select" required>
		    <option value="" disabled selected>Select User Type</option>
		    <option value="administrator">Administrator</option>
		    <option value="pharmacist">Pharmacist</option>
		    <option value="pharmaceutical">Pharmaceutical</option>
		    <option value="supervisor">Supervisor</option>
		    <option value="patient">Patient</option>
		    <option value="doctor">Doctor</option>
		</select>
	    </div>
	    <button type="submit" class="btn btn-primary">Login</button>
	</form>
    </div>
    <?php echo $footer; ?>
</body>
</html>
