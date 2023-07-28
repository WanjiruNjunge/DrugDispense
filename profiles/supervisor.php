<?php
session_start();
// Include database configuration and other necessary files
require_once('../database.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supervisor Profile</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-4">
	<div class="row">
	    <!-- Side Column -->
	    <div class="col-md-3">
<?php
// Check if supervisorId is provided in the URL
if (!isset($_GET['supervisorId'])) {
	echo '<p>Supervisor ID not provided.</p>';
	exit();
}

$supervisorId = $_GET['supervisorId'];

// Retrieve the supervisor's data from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

// Query to get the supervisor's details
$query = "SELECT supervisor.*, pharmaceutical.title FROM supervisor INNER JOIN pharmaceutical ON supervisor.pharmaceuticalId = pharmaceutical.pharmaceuticalId WHERE supervisorId = $supervisorId";
$result = mysqli_query($connection, $query);
$supervisor = mysqli_fetch_assoc($result);

// Close the database connection
mysqli_close($connection);
?>

		<div class="card">
		    <img src="../static/images/users/supervisors/<?php echo $supervisor['imageUrl']; ?>" class="card-img-top" alt="Supervisor Image">
		    <div class="card-body">
			<p class="card-text"><?php echo $supervisor['supervisorId']; ?></p>
			<h5 class="card-title"><?php echo $supervisor['name']; ?></h5>
			<p class="card-text"><?php echo $supervisor['email']; ?></p>
			<p class="card-text"><?php echo $supervisor['phoneNumber']; ?></p>
			<a href="../edit/edit_supervisor.php?supervisorId=<?php echo $supervisorId; ?>" class="btn btn-primary">Edit Profile</a>
		    </div>
		</div>
		<div class="card mt-4">
		    <div class="card-body">
			<h5 class="card-title">Pharmaceutical</h5>
			<p><a href = "../profiles/pharmaceutical.php?pharmaceuticalId=<?php echo $supervisor['pharmaceuticalId']?>"><?php echo $supervisor['title']; ?></a></p>
		    </div>
		</div>
	    </div>

	    <!-- Main Column -->
	    <div class="col-md-9">
		<!-- Add New Contract -->
		<h1>Add New Contract</h1>
<?php
// Handle form submission to add a new contract
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addContract'])) {
	$startDate = $_POST['startDate'];
	$endDate = $_POST['endDate'];
	$pharmacyId = $_POST['pharmacyId'];
	$pharmaceuticalId = $supervisor['pharmaceuticalId'];

	// Insert the new contract into the database
	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if (!$connection) {
		die("Connection failed: " . mysqli_connect_error());
	}

	$query = "INSERT INTO contract (startDate, endDate, pharmacyId, pharmaceuticalId) VALUES ('$startDate', '$endDate', $pharmacyId, $pharmaceuticalId)";
	mysqli_query($connection, $query);

	// Close the database connection
	mysqli_close($connection);

	// Refresh the page to reflect the changes
	header("Location: ../profiles/supervisor.php?supervisorId=$supervisorId");
	exit();
}
?>

		<form method="post" action="../profiles/supervisor.php?supervisorId=<?php echo $supervisorId; ?>">
		    <div class="mb-3">
			<label for="startDate" class="form-label">Start Date:</label>
			<input type="date" id="startDate" name="startDate" class="form-control" required>
		    </div>
		    <div class="mb-3">
			<label for="endDate" class="form-label">End Date:</label>
			<input type="date" id="endDate" name="endDate" class="form-control" required>
		    </div>
		    <div class="mb-3">
			<label for="pharmacyId" class="form-label">Select a Pharmacy:</label>
			<select id="pharmacyId" name="pharmacyId" class="form-select" required>
			    <option value="">Select a Pharmacy</option>
<?php
// Retrieve the list of pharmacies from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM pharmacy";
$pharmacyResult = mysqli_query($connection, $query);

// Close the database connection
mysqli_close($connection);
?>
			    <?php while ($pharmacy = mysqli_fetch_assoc($pharmacyResult)) : ?>
				<option value="<?php echo $pharmacy['pharmacyId']; ?>"><?php echo $pharmacy['title']; ?></option>
			    <?php endwhile; ?>
			</select>
		    </div>
		    <button type="submit" name="addContract" class="btn btn-primary">Add Contract</button>
		</form>

		<!-- Display Contracts -->
		<h1>Contracts</h1>
<?php
// Retrieve the contracts associated with the pharmaceutical from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT contract.*, pharmacy.title AS pharmacyTitle FROM contract
	INNER JOIN pharmacy ON contract.pharmacyId = pharmacy.pharmacyId
	WHERE contract.pharmaceuticalId = {$supervisor['pharmaceuticalId']}";
$contractResult = mysqli_query($connection, $query);
?>

		<?php if (mysqli_num_rows($contractResult) > 0) : ?>
		    <table class="table table-bordered">
			<thead>
			    <tr>
				<th>ID</th>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Pharmacy</th>
				<th>Status</th>
			    </tr>
			</thead>
			<tbody>
			    <?php while ($contract = mysqli_fetch_assoc($contractResult)) : ?>
				<tr>
				    <td><a href="../profiles/contract.php?contractId=<?php echo $contract['contractId']; ?>"><?php echo $contract['contractId']; ?></a></td>
				    <td><?php echo date('D d F, Y h:i A', strtotime($contract['startDate'])); ?></td>
				    <td><?php echo date('D d F, Y h:i A', strtotime($contract['endDate'])); ?></td>
				    <td><a href="../profiles/pharmacy.php?pharmacyId=<?php echo $contract['pharmacyId']; ?>"><?php echo $contract['pharmacyTitle']; ?></a></td>
				    <td><?php echo ($contract['endDate'] < date('Y-m-d')) ? 'Completed' : 'Active'; ?></td>
				</tr>
			    <?php endwhile; ?>
			</tbody>
		    </table>
		<?php else : ?>
		    <p>No contracts found.</p>
		<?php endif; ?>

		<!-- Include Bootstrap and moment.js JavaScript -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
	    </div>
	</div>
    </div>
    <?php echo $footer; ?>
</body>
</html>
