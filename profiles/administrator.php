<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['USER'])) {
	header("Location: login.php");
	exit();
}

// Check if the user is an administrator
if ($_SESSION['USER'] !== 'administrator') {
	header("Location: ../errors/permission_denied.php");
	exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
.btn {
width : 100%;
margin: 10px;
border: 1px grey solid;
}
</style>
</head>

<body>
<?php
require_once('../navigation.php');
echo $nav;

// Include the database configuration file
require_once('../database.php');

// Function to query the database and retrieve the administrator's details
function getAdministratorDetails($administratorId, $conn)
{
	$query = "SELECT * FROM administrator WHERE administratorId = :id";
	$stmt = $conn->prepare($query);
	$stmt->bindParam(':id', $administratorId, PDO::PARAM_INT);
	$stmt->execute();
	return $stmt->fetch(PDO::FETCH_ASSOC);
}

try {
	// Create a new PDO instance
	$conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Check if the administratorId is set in the URL
	if (isset($_GET['administratorId'])) {
		$administratorId = $_GET['administratorId'];

		// Get the administrator details from the database
		$administrator = getAdministratorDetails($administratorId, $conn);
	}
} catch (PDOException $e) {
	echo "Error: " . $e->getMessage();
}
?>

    <div class="container mt-5">
	<div class="row">
	    <div class="col-md-3">
		<!-- Administrator Profile Section -->
		<div class="card">
		    <img src="../static/images/users/administrators/<?php echo $administrator['imageUrl']; ?>"
			class="card-img-top" alt="Administrator Image">
		    <div class="card-body">
			<h5 class="card-title"><?php echo $administrator['name']; ?></h5>
			<p class="card-text">Email: <?php echo $administrator['email']; ?></p>
			<p class="card-text">Phone: <?php echo $administrator['phoneNumber']; ?></p>
		    </div>
		</div>
	    </div>
	    <div class="col-md-9">
		<!-- Registration and Records Section -->
		<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
		    <li class="nav-item" role="presentation">
			<button class="nav-link active" id="pills-registrations-tab" data-bs-toggle="pill"
			    data-bs-target="#pills-registrations" type="button" role="tab"
			    aria-controls="pills-registrations" aria-selected="true">Registrations</button>
		    </li>
		    <li class="nav-item" role="presentation">
			<button class="nav-link" id="pills-records-tab" data-bs-toggle="pill"
			    data-bs-target="#pills-records" type="button" role="tab" aria-controls="pills-records"
			    aria-selected="false">Records</button>
		    </li>
		</ul>
		<div class="tab-content" id="pills-tabContent">
		    <!-- Registrations Section -->
		    <div class="tab-pane fade show active" id="pills-registrations" role="tabpanel"
			aria-labelledby="pills-registrations-tab">
			<a class = 'btn' href = '../register/add_administrator.php'>Register Administrator</a>
			<a class = 'btn' href = '../register/add_doctor.php'>Register Doctor</a>
			<a class = 'btn' href = '../register/add_patient.php'>Register Patient</a>
			<a class = 'btn' href = '../register/add_pharmaceutical.php'>Register Pharmaceutical</a>
			<a class = 'btn' href = '../register/add_pharmacist.php'>Register Pharmacist</a>
			<a class = 'btn' href = '../register/add_pharmacy.php'>Register Pharmacy</a>
			<a class = 'btn' href = '../register/add_supervisor.php'>Register Supervisor</a>
		    </div>

		    <!-- Records Section -->
		    <div class="tab-pane fade" id="pills-records" role="tabpanel" aria-labelledby="pills-records-tab">
			<a class = 'btn' href = '../lists/registered_administrators.php'>Registered Administrators</a>
			<a class = 'btn' href = '../lists/registered_contracts.php'>Registered Contracts</a>
			<a class = 'btn' href = '../lists/registered_doctors.php'>Registered Doctors</a>
			<a class = 'btn' href = '../lists/registered_drugs.php'>Registered Drugs</a>
			<a class = 'btn' href = '../lists/registered_patients.php'>Registered Patients</a>
			<a class = 'btn' href = '../lists/registered_pharmaceuticals.php'>Registered Pharmaceuticals</a>
			<a class = 'btn' href = '../lists/registered_pharmacies.php'>Registered Pharnacies</a>
			<a class = 'btn' href = '../lists/registered_pharmacists.php'>Registered Pharmacists</a>
			<a class = 'btn' href = '../lists/registered_supervisors.php'>Registered Supervisors</a>
		    </div>
		</div>
	    </div>
	</div>
    </div>
    <?php echo $footer; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
