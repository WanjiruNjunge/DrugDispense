<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
	header("Location: ../authentication/login.php");
	exit;
}

if (isset($_GET['doctorId'])) {
	$doctorId = $_GET['doctorId'];
}

// Check if the logged-in user has the necessary permission ('administrator')
if ($_SESSION['USER'] !== 'administrator' && $_SESSION['USER'] !== 'doctor') {
	header("Location: ../errors/permission_denied.php");
	exit;
}

// Database configuration
require_once('../database.php');
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Handle assigning a doctor to the patient if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignPatient']) && isset($_POST['patientId'])) {
	$patientId = $_POST['patientId'];
	$isPrimary = isset($_POST['isPrimary']) ? 1 : 0;

	// Prepare and execute the query to insert the doctor assignment into the database
	$query = "INSERT INTO patient_doctor (patientId, doctorId, isPrimary) VALUES ('$patientId', '$doctorId', '$isPrimary')";
	if (mysqli_query($connection, $query)) {
		// Refresh the page to view the changes after adding the assignment
		header("Location: ../profiles/doctor.php?doctorId=$doctorId");
		exit;
	} else {
		echo "Error assigning patient: " . mysqli_error($connection);
	}
}

// Handle assigning a prescription to the patient if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignPrescription']) && isset($_POST['drugId'])) {
	$drugId = $_POST['drugId'];

	// Prepare and execute the query to insert the prescription assignment into the database
	$query = "INSERT INTO prescription (drugId, patientDoctorId, isGiven)
		VALUES ('$drugId', 0, 0)";
	if (mysqli_query($connection, $query)) {
		// Refresh the page to view the changes after adding the prescription
		header("Location: patient_profile.php?patientId=$patientId");
		exit;
	} else {
		echo "Error assigning prescription: " . mysqli_error($connection);
	}
}
?>

	<!DOCTYPE html>
	<html>
	<head>
	<title>Doctor Profile</title>
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
	<div class="col-md-4" style = "margin-bottom: 10px;">
<?php

// Retrieve the doctor's data from the database
if (!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

// Query to get the doctor's details
$query = "SELECT * FROM doctor WHERE doctorId = $doctorId";
$result = mysqli_query($connection, $query);
$doctor = mysqli_fetch_assoc($result);

// Close the database connection
mysqli_close($connection);
?>

		<div class="card">
		    <img src="../static/images/users/doctors/<?php echo $doctor['imageUrl']; ?>" class="card-img-top" alt="Doctor Image">
		    <div class="card-body">
			<h3 class="card-title"><strong style = "color: #ff4500;">Doctor ID</strong> <?php echo $doctor['doctorId']; ?></h3>
			<h3 class="card-title"><?php echo $doctor['name']; ?></h3>
			<p class="lead"><?php echo $doctor['medicalCertificateNumber']; ?></p>
			<p class="lead"><?php echo $doctor['gender']; ?></p>
			<p class="lead"><?php echo $doctor['email']; ?></p>
			<p class="lead"><?php echo $doctor['phoneNumber']; ?></p>
			<p class="lead"><?php echo $doctor['specialization']; ?></p>
			<a href="../edit/edit_doctor.php?doctorId=<?php echo $doctorId; ?>" class="btn btn-primary">Edit Profile</a>
		    </div>
		</div>
		<div class="card mt-4">
		    <div class="card-body">
			<h5 class="card-title">Assign a Patient</h5>
			<form method="post" action="../profiles/doctor.php?doctorId=<?php echo $doctorId; ?>">
<?php
// Retrieve the list of unassigned patients from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

// Query to get all patients not already assigned to the doctor
$query = "SELECT * FROM patient WHERE patientId NOT IN (SELECT patientId FROM patient_doctor WHERE doctorId = $doctorId)";
$unassignedPatientsResult = mysqli_query($connection, $query);

// Close the database connection
mysqli_close($connection);
?>

			    <div class="mb-3">
				<label for="patientId" class="form-label">Select a Patient:</label>
				<select id="patientId" name="patientId" class="form-select" required>
				    <option value="">Select a Patient</option>
				    <?php while ($patient = mysqli_fetch_assoc($unassignedPatientsResult)) : ?>
					<option value="<?php echo $patient['patientId']; ?>"><?php echo $patient['name']; ?></option>
				    <?php endwhile; ?>
				</select>
			    </div>
			    <div class="mb-3 form-check">
				<input type="checkbox" class="form-check-input" id="isPrimary" name="isPrimary">
				<label class="form-check-label" for="isPrimary">Primary Patient</label>
			    </div>
			    <button type="submit" name="assignPatient" class="btn btn-primary">Assign Patient</button>
			</form>
		    </div>
		</div>
	    </div>

	    <!-- Main Column -->
	    <div class="col-md-8">
		<!-- Display Assigned Patients -->
		<h1>Assigned Patients</h1>
<?php
// Get doctorId from the session
$doctorId = $_SESSION['USER_ID'];

// Retrieve the assigned patients from the database
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!$connection) {
	die("Connection failed: " . mysqli_connect_error());
}

// Query to get all patients assigned to the doctor
$query = "SELECT patient.*, patient_doctor.isPrimary FROM patient JOIN patient_doctor ON patient.patientId = patient_doctor.patientId WHERE patient_doctor.doctorId = $doctorId";
$assignedPatientsResult = mysqli_query($connection, $query);

// Close the database connection
mysqli_close($connection);
?>

		<?php if (mysqli_num_rows($assignedPatientsResult) > 0) : ?>
		    <table class="table table-bordered">
			<thead>
			    <tr>
				<th>Id</th>
				<th>Name</th>
				<th>Email</th>
				<th>Phone Number</th>
				<th>Primary</th>
			    </tr>
			</thead>
			<tbody>
			    <?php while ($patient = mysqli_fetch_assoc($assignedPatientsResult)) : ?>
				<tr>
				    <td><?php echo $patient['patientId']; ?></td>
				    <td><a href="../profiles/patient.php?patientId=<?php echo $patient['patientId']; ?>"><?php echo $patient['name']; ?></a></td>
				    <td><?php echo $patient['email']; ?></td>
				    <td><?php echo $patient['phoneNumber']; ?></td>
				    <td><?php echo $patient['isPrimary'] ? 'Yes' : 'No'; ?></td>
				</tr>
			    <?php endwhile; ?>
			</tbody>
		    </table>
		<?php else : ?>
		    <p>No assigned patients found.</p>
		<?php endif; ?>
	    </div>
	</div>
    </div>
    <?php echo $footer; ?>

    <!-- Include Bootstrap and moment.js JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
</body>
</html>
