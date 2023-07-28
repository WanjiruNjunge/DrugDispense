<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
	header("Location: ../authentication/login.php");
	exit;
}

// Check if the logged-in user has the necessary permission ('administrator')
if ($_SESSION['USER'] !== 'administrator' && $_SESSION['USER'] !== 'pharmacist') {
	header("Location: ../errors/permission_denied.php");
	exit;
}

// Database configuration
require_once('../database.php');
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$pharmacistId = $_GET['pharmacistId'];

// Fetch pharmacist details and associated pharmacy
$query = "SELECT pharmacist.*, pharmacy.title AS pharmacy_name
	FROM pharmacist
	INNER JOIN pharmacy ON pharmacist.pharmacyId = pharmacy.pharmacyId
	WHERE pharmacist.pharmacistId = '$pharmacistId'";
$result = mysqli_query($connection, $query);

if (mysqli_num_rows($result) !== 1) {
	header("Location: ../errors/not_found.php");
	exit;
}

$pharmacist = mysqli_fetch_assoc($result);

// Handle updating the isGiven value of prescription records if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prescriptionId']) && isset($_POST['isGiven'])) {
	$prescriptionId = $_POST['prescriptionId'];
	$isGiven = $_POST['isGiven'];

	// Prepare and execute the query to update the isGiven value in the database
	$query = "UPDATE prescription SET isGiven = $isGiven WHERE prescriptionId = $prescriptionId";
	if (mysqli_query($connection, $query)) {
		// Refresh the page to view the changes after updating
		header("Location: ../profiles/pharmacist.php?pharmacistId=$pharmacistId");
		exit;
	} else {
		echo "Error updating record: " . mysqli_error($connection);
	}
}

// Fetch all prescriptions assigned to the pharmacist's pharmacy
$query = "SELECT prescription.*, doctor.name AS doctor_name
	FROM prescription
	INNER JOIN patient_doctor ON prescription.patientDoctorId = patient_doctor.patientDoctorid
	INNER JOIN doctor ON patient_doctor.doctorId = doctor.doctorId
	INNER JOIN drug ON drug.drugId = prescription.drugId
	INNER JOIN contract ON contract.contractId = drug.contractId
	WHERE contract.pharmacyId = '{$pharmacist['pharmacyId']}'";
$prescriptionResult = mysqli_query($connection, $query);
if (!$prescriptionResult) {
		die("Error: " . mysqli_error($connection));
}

// Pagination variables
$perPage = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $perPage;
$totalPrescriptions = mysqli_num_rows($prescriptionResult);

$totalPages = ceil($totalPrescriptions / $perPage);

// Fetch prescriptions with pagination
$query .= " LIMIT $start, $perPage";
$prescriptionResult = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacist Profile</title>
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
	    <div class="col-md-3 mb-4">
		<div class="card">
		    <img src="../static/images/users/pharmacists/<?php echo $pharmacist['imageUrl']; ?>" class="card-img-top" alt="Pharmacist Image">
		    <div class="card-body">
			<h5 class="card-title"><?php echo $pharmacist['name']; ?></h5>
			<p class="card-text">Email: <?php echo $pharmacist['email']; ?></p>
			<p class="card-text">Phone Number: <?php echo $pharmacist['phoneNumber']; ?></p>
			<p class="card-text">Pharmacy: <a href="../profiles/pharmacy.php?pharmacyId=<?php echo $pharmacist['pharmacyId']; ?>"><?php echo $pharmacist['pharmacy_name']; ?></a></p>
			<a href="../edit/edit_pharmacist.php?pharmacistId=<?php echo $pharmacistId; ?>" class="btn btn-primary">Edit Pharmacist</a>
		    </div>
		</div>
	    </div>

	    <!-- Main Column -->
	    <div class="col-md-9">
		<h1 class="mb-4">Prescriptions</h1>
		<?php if (mysqli_num_rows($prescriptionResult) > 0) : ?>
		    <table class="table table-bordered">
			<thead>
			    <tr>
				<th>Prescription ID</th>
				<th>Doctor</th>
				<th>Dosage</th>
				<th>Frequency</th>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Dispense</th>
			    </tr>
			</thead>
			<tbody>
			    <?php while ($prescription = mysqli_fetch_assoc($prescriptionResult)) : ?>
				<tr>
				    <td><?php echo $prescription['prescriptionId']; ?></td>
				    <td><?php echo $prescription['doctor_name']; ?></td>
				    <td><?php echo $prescription['dosage']; ?></td>
				    <td><?php echo $prescription['frequency']; ?></td>
				    <td><?php echo date("D d F, Y h:i A", strtotime($prescription['startDate'])); ?></td>
				    <td><?php echo date("D d F, Y h:i A", strtotime($prescription['endDate'])); ?></td>
				    <td>
					<form method="post" action="">
					    <input type="hidden" name="prescriptionId" value="<?php echo $prescription['prescriptionId']; ?>">
					    <input type="hidden" name="isGiven" value="1">
					    <button type="submit" class="btn btn-success" <?php echo ($prescription['isGiven']) ? 'disabled' : ''; ?>>Dispense Drug</button>
					</form>
				    </td>
				</tr>
			    <?php endwhile; ?>
			</tbody>
		    </table>

		    <!-- Pagination -->
		    <?php if ($totalPages > 1) : ?>
			<nav>
			    <ul class="pagination">
				<li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
				    <a class="page-link" href="?pharmacistId=<?php echo $pharmacistId; ?>&page=1" aria-label="First">
					<span aria-hidden="true">&laquo;&laquo;</span>
				    </a>
				</li>
				<li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
				    <a class="page-link" href="?pharmacistId=<?php echo $pharmacistId; ?>&page=<?php echo ($page > 1) ? $page - 1 : 1; ?>" aria-label="Previous">
					<span aria-hidden="true">&laquo;</span>
				    </a>
				</li>
				<?php for ($i = 1; $i <= $totalPages; $i++) : ?>
				    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>"><a class="page-link" href="?pharmacistId=<?php echo $pharmacistId; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
				<?php endfor; ?>
				<li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
				    <a class="page-link" href="?pharmacistId=<?php echo $pharmacistId; ?>&page=<?php echo ($page < $totalPages) ? $page + 1 : $totalPages; ?>" aria-label="Next">
					<span aria-hidden="true">&raquo;</span>
				    </a>
				</li>
				<li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
				    <a class="page-link" href="?pharmacistId=<?php echo $pharmacistId; ?>&page=<?php echo $totalPages; ?>" aria-label="Last">
					<span aria-hidden="true">&raquo;&raquo;</span>
				    </a>
				</li>
			    </ul>
			</nav>
		    <?php endif; ?>
		<?php else : ?>
		    <p>No prescriptions found for this pharmacist.</p>
		<?php endif; ?>
	    </div>
	</div>
    </div>
    <?php echo $footer; ?>
</body>
</html>
