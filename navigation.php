<?php
$nav = '
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<div class="container">
	    <a class="navbar-brand" href="../profiles/index.php">Tiba Mara Moja</a>
	    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
		aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	    </button>
	    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
		<ul class="navbar-nav">
		    <li class="nav-item">
			<a class="nav-link active" href="../profiles/index.php">Home</a>
		    </li>
		    <li class="nav-item">
			<a class="nav-link" href="../profiles/about_us.php">What We Do</a>
		    </li>
		    <li class="nav-item">
			<a class="nav-link" href="../profiles/contact_us.php">Contact Us</a>
		    </li>';
$isLoggedIn = isset($_SESSION['USER']);
if ($isLoggedIn) {
	$nav .= '<li class="nav-item">
		<a class="nav-link" href="../authentication/logout.php">Sign Out</a>
		</li>
		<li class="nav-item">
		<a class="nav-link" href="../profiles/' . $_SESSION['USER'] . '.php?'. 
		$_SESSION['USER'].'Id='. $_SESSION['USER_ID'] .'">' . $_SESSION['USER'] . '</a>
		</li>';
} else {
	$nav .= '<li class="nav-item">
		<a class="nav-link" href="../authentication/login.php">Sign In</a>
		</li>
		<li class="nav-item">
		<a class="nav-link" href="../register/add_patient.php">Sign Up</a>
		</li>';
}
$nav .= '</ul>
	</div>
	</div>
	</nav>
';

$footer = ' <style>
h1 {
color: green;
margin-top: 10px;
margin-bottom: 5px;
}

h2 {
color: purple;
margin-top: 10px;
margin-bottom: 5px;
}

h3 {
color: purple;
margin-top: 10px;
margin-bottom: 5px;
}

h4 {
color: #555;
}

section {
padding-top: 30px;
padding-bottom: 20px;
}

.fa-3x {
color: red;
}

       .footer-section {
            background-color: #333;
            color: #fff;
            padding: 40px 0;
margin-top: 20px;
        }

        .footer-section h4 {
            color: #fff;
        }

        .footer-section p {
            margin: 0;
        }

        .footer-icon {
            color: #fff;
            font-size: 24px;
            margin-right: 15px;
        }


        @media (max-width: 767px) {
            .text-md-right {
                text-align: center !important;
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .col-md-6 {
            width: 50%;
            padding: 0 15px;
        }
    </style>
    <footer class="footer-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h4>Contact Us</h4>
                    <p>Email: info@tibamaramoja.com</p>
                    <p>Phone: +254 721 446 942</p>
                    <p>Address: Ole Sangale - Keri Road, Nairobi West, Kenya</p>
                </div>
                <div class="col-md-6 text-md-right">
                    <h4>Follow Us</h4>
                    <a href="#" class="footer-icon"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="footer-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="footer-icon"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>';
 ?>
