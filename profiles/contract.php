<?php
// Start session
session_start();

// Check if the user is authenticated and allowed to access the page
$allowedRoles = array('administrator', 'supervisor', 'pharmacist');
if ($_SESSION['USER'] !== 'administrator' && $_SESSION['USER'] !== 'pharmacist' && $_SESSION['USER'] !== 'supervisor') {
	header("Location: ../errors/403.php");
	exit();
}

// Database credentials
require_once('../database.php');

// Check if the contractId is provided in the URL
if (!isset($_GET['contractId']) || empty($_GET['contractId']) || !is_numeric($_GET['contractId'])) {
	header("Location: ../errors/404.php");
	exit();
}

// Fetch contract details from the database
$contractId = $_GET['contractId'];
$contract = array();
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT c.*, p.title AS pharmacyName, ph.title AS pharmaceuticalName
		FROM contract c
		INNER JOIN pharmacy p ON c.pharmacyId = p.pharmacyId
		INNER JOIN pharmaceutical ph ON c.pharmaceuticalId = ph.pharmaceuticalId
		WHERE c.contractId = $contractId";

	$result = $conn->query($sql);
	if ($result->num_rows === 1) {
		$contract = $result->fetch_assoc();
	} else {
		header("Location: ../errors/404.php");
		exit();
	}
	$conn->close();
}

if ($_SESSION['USER'] === 'administrator' || $_SESSION['USER'] === 'supervisor') {
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addDrug'])) {
		$scientificName = $_POST['scientificName'];
		$tradeName = $_POST['tradeName'];
		$form = $_POST['form'];

		$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} else {
			$sql = "INSERT INTO drug (scientificName, tradeName, contractId, form)
				VALUES ('$scientificName', '$tradeName', $contractId, '$form')";

			if ($conn->query($sql) === TRUE) {
				header("Location: ../profiles/contract_profile.php?contractId=$contractId");
				exit();
			} else {
				echo "Error inserting record: " . $conn->error;
			}

			$conn->close();
		}
	}
}

function displayDrugTable($contractId, $currentPage, $resultsPerPage)
{
	$offset = ($currentPage - 1) * $resultsPerPage;

	$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} else {
		$sql = "SELECT * FROM drug WHERE contractId = $contractId LIMIT $offset, $resultsPerPage";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			echo '<table class="table table-bordered mt-4">';
			echo '<thead><tr><th>Drug ID</th><th>Scientific Name</th><th>Trade Name</th><th>Form</th><th>Quantity</th><th>Formula</th><th>Expiry Date</th><th>Date Manufactured</th><th>Action</th></tr></thead>';
			echo '<tbody>';
			while ($row = $result->fetch_assoc()) {
				$drugId = $row['drugId'];
				echo <<<HTML
		    <tr>
		    <td>{$row['drugId']}</td>
		    <td>{$row['scientificName']}</td>
		    <td>{$row['commonName']}</td>
		    <td>{$row['form']}</td>
		    <td>{$row['amount']}</td>
		    <td>{$row['formula']}</td>
		    <td>{$row['expiryDate']}</td>
		    <td>{$row['manufacturingDate']}</td>
		    <td><a href = '../edit/edit_drug.php?drugId={$drugId}'>Edit</a></td>
		    </tr>
HTML;
			}
			echo '</tbody>';
			echo '</table>';
		} else {
			echo '<p>No drugs associated with this contract.</p>';
		}
		$conn->close();
	}
}

$resultsPerPage = 10;
$totalResults = 0;
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT COUNT(*) AS total FROM drug WHERE contractId = $contractId";
	$result = $conn->query($sql);
	if ($result->num_rows === 1) {
		$row = $result->fetch_assoc();
		$totalResults = $row['total'];
	}
	$conn->close();
}

$totalPages = ceil($totalResults / $resultsPerPage);

$currentPage = 1;
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
	$currentPage = $_GET['page'];
	if ($currentPage < 1) {
		$currentPage = 1;
	} elseif ($currentPage > $totalPages) {
		$currentPage = $totalPages;
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php
require_once('../navigation.php');
echo $nav;
?>
    <div class="container mt-5">
	<div class="jumbotron">
	    <h1 class="text-primary">Contract Details</h1>
	    <p class="lead">Contract ID: <?= $contract['contractId'] ?></p>
	    <hr class="my-4">
	    <p>Pharmacy: <a href="../profiles/pharmacy.php?pharmacyId=<?= $contract['pharmacyId'] ?>"><?= $contract['pharmacyName'] ?></a></p>
	    <p>Pharmaceutical: <a href="../profiles/pharmaceutical.php?pharmaceuticalId=<?= $contract['pharmaceuticalId'] ?>"><?= $contract['pharmaceuticalName'] ?></a></p>
	    <p>Start Date: <?= $contract['startDate'] ?></p>
	    <p>End Date: <?= $contract['endDate'] ?></p>
	</div>

<?php
if ($_SESSION['USER'] === 'administrator' || $_SESSION['USER'] === 'supervisor') {
	echo '<a href="../register/add_drug.php?contractId=' . $contractId . '" class="btn btn-primary btn-lg btn-block mb-4">Add Drug</a>';
}

displayDrugTable($contractId, $currentPage, $resultsPerPage);
?>

	<nav aria-label="Drug Pagination">
	    <ul class="pagination justify-content-center mt-4">
<?php
for ($page = 1; $page <= $totalPages; $page++) {
	$activeClass = $page === $currentPage ? 'active' : '';
	echo "<li class='page-item $activeClass'><a class='page-link' href='contract_profile.php?contractId=$contractId&page=$page'>$page</a></li>";
}
?>
	    </ul>
	</nav>
    </div>
    <?php echo $footer; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
