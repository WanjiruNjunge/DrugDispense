<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
	header("Location: ../authentication/login.php");
	exit;
}

// Check if the logged-in user has the necessary permission ('administrator')
if ($_SESSION['USER'] !== 'administrator' && $_SESSION['USER'] !== 'patient' && $_SESSION['USER'] !== 'doctor') {
	header("Location: ../errors/permission_denied.php");
	exit;
}

// Database configuration
require_once('../database.php');
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check if patientId is provided as a GET parameter
if (!isset($_GET['patientId'])) {
	header("Location: ../errors/not_found.php");
	exit;
}

$patientId = $_GET['patientId'];

// Fetch patient details
$query = "SELECT * FROM patient WHERE patientId = '$patientId'";
$result = mysqli_query($connection, $query);

if (mysqli_num_rows($result) !== 1) {
	header("Location: ../errors/not_found.php");
	exit;
}

$patient = mysqli_fetch_assoc($result);

if ($_SESSION['USER'] === 'doctor') {
	// Fetch patient doctor details
	$query = "SELECT patientDoctorId FROM patient_doctor WHERE patientId = '$patientId' AND doctorId = '" .
		$_SESSION['USER_ID'] . "'";
	$result = mysqli_query($connection, $query);

	if (mysqli_num_rows($result) < 1) {
		header("Location: ../errors/not_found.php");
		exit;
	}

	$patientDoctorId = mysqli_fetch_assoc($result)['patientDoctorId'];
}

// Check if the user is logged in as an administrator or the patient
// Check if the user is logged in as an administrator or the patient
$isAdmin = isset($_SESSION['USER']) && $_SESSION['USER'] === 'administrator';
$isPatient = isset($_SESSION['USER']) && $_SESSION['USER'] === 'patient' && $_SESSION['USER_ID'] == $patientId;

// Fetch all doctors not already assigned to the patient
$doctorsQuery = "SELECT * FROM doctor WHERE doctorId NOT IN
	(SELECT doctorId FROM patient_doctor WHERE patientId = '$patientId')";
$doctorsResult = mysqli_query($connection, $doctorsQuery);

// Fetch all doctors assigned to the patient
$assignedDoctorsQuery = "SELECT doctor.*, patient_doctor.isPrimary FROM doctor
	INNER JOIN patient_doctor ON doctor.doctorId = patient_doctor.doctorId
	WHERE patient_doctor.patientId = '$patientId'";
$assignedDoctorsResult = mysqli_query($connection, $assignedDoctorsQuery);

// Fetch all drugs for prescription assignment
$drugsQuery = "SELECT * FROM drug";
$drugsResult = mysqli_query($connection, $drugsQuery);

// Handle assigning a doctor to the patient if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignDoctor']) && isset($_POST['doctorId'])) {
	$doctorId = $_POST['doctorId'];
	$isPrimary = isset($_POST['isPrimary']) ? 1 : 0;

	// Prepare and execute the query to insert the doctor assignment into the database
	$query = "INSERT INTO patient_doctor (patientId, doctorId, isPrimary) VALUES ('$patientId', '$doctorId', '$isPrimary')";
	if (mysqli_query($connection, $query)) {
		// Refresh the page to view the changes after adding the assignment
		header("Location: patient.php?patientId=$patientId");
		exit;
	} else {
		echo "Error assigning doctor: " . mysqli_error($connection);
	}
}

// Handle assigning prescriptions to the patient if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignPrescriptions']) && isset($_POST['drugIds'])) {
	$drugId = $_POST['drugIds'];
	$dosage = $_POST['dosage'];
	$frequency = $_POST['frequency'];
	$startDate = $_POST['startDate'];
	$endDate = $_POST['endDate'];
	$price = $_POST['price'];

	// Prepare and execute the query to insert each prescription assignment into the database
	$query = "INSERT INTO prescription (drugId, patientDoctorId, dosage, frequency, startDate, endDate, price)
		VALUES ('$drugId', '$patientDoctorId', '$dosage', '$frequency', '$startDate', '$endDate', '$price')";
	if (!mysqli_query($connection, $query)) {
		echo "Error assigning prescription: " . mysqli_error($connection);
		exit;
	}

	// Refresh the page to view the changes after adding the prescriptions
	header("Location: patient.php?patientId=$patientId");
	exit;
}

// Fetch all prescriptions assigned to the patient
$query = "SELECT prescription.*, pharmacy.title AS pharmacy_name, drug.scientificName, doctor.name AS doctor_name, contract.contractId
	FROM prescription
	INNER JOIN drug ON prescription.drugId = drug.drugId
	INNER JOIN contract ON drug.contractId = contract.contractId
	INNER JOIN pharmacy ON contract.pharmacyId = pharmacy.pharmacyId
	INNER JOIN patient_doctor ON prescription.patientDoctorId = patient_doctor.patientDoctorid
	INNER JOIN doctor ON patient_doctor.doctorId = doctor.doctorId
	WHERE patient_doctor.patientId = '$patientId'";
$prescriptionResult = mysqli_query($connection, $query);

// Pagination variables for prescriptions
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
		<title>Patient Profile</title>
		<!-- Include Bootstrap CSS -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Include Moment.js -->
		<script src="https://cdn.jsdelivr.net/momentjs/2.29.1/moment.min.js"></script>
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
						<img src="../static/images/users/patients/<?php echo $patient['imageUrl']; ?>" class="card-img-top" alt="Patient Image">
						<div class="card-body">
							<h5 class="card-title"><?php echo $patient['name']; ?></h5>
							<p class="card-text">Email: <?php echo $patient['email']; ?></p>
							<p class="card-text">Phone Number: <?php echo $patient['phoneNumber']; ?></p>
							<p class="card-text">Date of Birth: <?php echo date("D d F, Y", strtotime($patient['dateOfBirth'])); ?></p>
							<?php if ($isAdmin || $isPatient) : ?>
							<a href="../edit/edit_patient.php?patientId=<?php echo $patientId; ?>" class="btn btn-primary">Update Details</a>
							<?php endif; ?>
						</div>
					</div>
					<div class="card mt-4">
						<div class="card-body">
							<h5 class="card-title">Assign a Doctor</h5>
							<form method="post" action="patient.php?patientId=<?php echo $patientId; ?>">

								<div class="mb-3">
									<label for="doctorId" class="form-label">Select a Doctor:</label>
									<select id="doctorId" name="doctorId" class="form-select" required>
										<option value="">Select a Doctor</option>
										<?php while ($doctor = mysqli_fetch_assoc($doctorsResult)) : ?>
										<option value="<?php echo $doctor['doctorId']; ?>"><?php echo $doctor['name']; ?></option>
										<?php endwhile; ?>
									</select>
								</div>
								<div class="mb-3 form-check">
									<input type="checkbox" class="form-check-input" id="isPrimary" name="isPrimary">
									<label class="form-check-label" for="isPrimary">Primary Doctor</label>
								</div>
								<button type="submit" name="assignDoctor" class="btn btn-primary">Assign Doctor</button>
							</form>
						</div>
					</div>
				</div>

				<!-- Main Column -->
				<div class="col-md-9">
					<h1>Patient Profile</h1>
					<h2>Assigned Doctors</h2>
					<?php if (mysqli_num_rows($assignedDoctorsResult) > 0) : ?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Doctor ID</th>
								<th>Name</th>
								<th>Email</th>
								<th>Gender</th>
								<th>Phone Number</th>
								<th>Primary Doctor</th>
							</tr>
						</thead>
						<tbody>
							<?php while ($assignedDoctor = mysqli_fetch_assoc($assignedDoctorsResult)) : ?>
							<tr>
								<td><?php echo $assignedDoctor['doctorId']; ?></td>
								<td><?php echo $assignedDoctor['name']; ?></td>
								<td><?php echo $assignedDoctor['email']; ?></td>
								<td><?php echo $assignedDoctor['gender']; ?></td>
								<td><?php echo $assignedDoctor['phoneNumber']; ?></td>
								<td><?php echo ($assignedDoctor['isPrimary'] == 1) ? 'Yes' : 'No'; ?></td>
							</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
					<?php else : ?>
					<p>No assigned doctors found.</p>
					<?php endif; ?>
					<?php if ($_SESSION['USER'] === 'doctor') { ?><div class="card">
						<div class="card-body">
							<h5 class="card-title">Assign Prescriptions</h5>
							<form method="post" action="patient.php?patientId=<?php echo $patientId; ?>">
								<div class="mb-3">
									<label for="drugIds" class="form-label">Select Drugs:</label>
									<select id="drugIds" name="drugIds" class="form-select" required>
										<?php mysqli_data_seek($drugsResult, 0); ?>
										<?php while ($drug = mysqli_fetch_assoc($drugsResult)) : ?>
										<option value="<?php echo $drug['drugId']; ?>"><?php echo $drug['scientificName']; ?></option>
										<?php endwhile; ?>
									</select>
								</div>
								<div class="mb-3">
									<label for="dosage" class="form-label">Dosage:</label>
									<input type="text" id="dosage" name="dosage" class="form-control" required>
								</div>
								<div class="mb-3">
									<label for="frequency" class="form-label">Frequency:</label>
									<input type="text" id="frequency" name="frequency" class="form-control" required>
								</div>
								<div class="mb-3">
									<label for="startDate" class="form-label">Start Date:</label>
									<input type="date" id="startDate" name="startDate" class="form-control" required>
								</div>
								<div class="mb-3">
									<label for="endDate" class="form-label">End Date:</label>
									<input type="date" id="endDate" name="endDate" class="form-control" required>
								</div>
								<div class="mb-3">
									<label for="price" class="form-label">Price:</label>
									<input type="text" id="price" name="price" class="form-control" required>
								</div>
								<button type="submit" name="assignPrescriptions" class="btn btn-primary">Assign Prescription</button>
							</form>
						</div>
					</div>
					<?php } ?>
					<h2>All Prescriptions</h2>
					<?php if (mysqli_num_rows($prescriptionResult) > 0) : ?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Prescription ID</th>
								<th>Doctor Name</th>
								<th>Drug Name</th>
								<th>Pharmacy</th>
								<th>Contract</th>
								<th>Dosage</th>
								<th>Frequency</th>
								<th>Start Date</th>
								<th>End Date</th>
								<th>Price</th>
								<th>Is Given</th>
							</tr>
						</thead>
						<tbody>
							<?php while ($prescription = mysqli_fetch_assoc($prescriptionResult)) : ?>
							<tr>
								<td><?php echo $prescription['prescriptionId']; ?></td>
								<td><?php echo $prescription['doctor_name']; ?></td>
								<td><?php echo $prescription['scientificName']; ?></td>
								<td><?php echo $prescription['pharmacy_name']; ?></td>
								<td><?php echo $prescription['contractId']; ?></td>
								<td><?php echo $prescription['dosage']; ?></td>
								<td><?php echo $prescription['frequency']; ?></td>
								<td><?php echo date("D d F, Y h:i A", strtotime($prescription['startDate'])); ?></td>
								<td><?php echo date("D d F, Y h:i A", strtotime($prescription['endDate'])); ?></td>
								<td><?php echo $prescription['price']; ?></td>
								<td><?php echo ($prescription['isGiven'] == 1) ? 'Yes' : 'No'; ?></td>
							</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
					<?php else : ?>
					<p>No prescriptions found.</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
    <?php echo $footer; ?>
	</body>
</html>
