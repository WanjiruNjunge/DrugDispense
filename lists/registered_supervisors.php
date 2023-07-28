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

// Prepare and execute the query to fetch supervisors data along with the pharmaceutical they belong to
$query = "SELECT supervisor.*, pharmaceutical.title AS pharmaceutical_name
	FROM supervisor
	INNER JOIN pharmaceutical ON supervisor.pharmaceuticalId = pharmaceutical.pharmaceuticalId";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Supervisors</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
	<h1 class="mb-4">Registered Supervisors</h1>

	<table class="table table-bordered">
	    <thead>
		<tr>
		    <th>ID</th>
		    <th>Name</th>
		    <th>Email</th>
		    <th>Phone Number</th>
		    <th>Pharmacy</th>
		</tr>
	    </thead>
	    <tbody>
<?php
if ($result){
	while ($row = mysqli_fetch_assoc($result)) {
		$supervisorId = $row['supervisorId'];
		$name = $row['name'];
		$email = $row['email'];
		$phoneNumber = $row['phoneNumber'];
		$pharmaceuticalName = $row['pharmaceutical_name'];
		$pharmaceuticalId = $row['pharmaceuticalId'];
	}
?>

		    <tr>
			<td><?php echo $supervisorId; ?></td>
			<td><a href="../profiles/supervisor.php?supervisorId=<?php echo $supervisorId; ?>"><?php echo $name; ?></a></td>
			<td><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></td>
			<td><a href="tel:<?php echo $phoneNumber; ?>"><?php echo $phoneNumber; ?></a></td>
			<td><a href="../profiles/pharmaceutical.php?pharmaceuticalId=<?php echo $pharmaceuticalId; ?>"><?php echo $pharmaceuticalName; ?></a></td>
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
