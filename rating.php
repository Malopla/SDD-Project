<!DOCTYPE html>

<?php session_start();
  
	// Includes db.php functions and enables connection to the database
	try {	
		include 'resources/database/db.php';
		include 'db-class.php';
		include 'auth-class.php';
		include 'user-class.php';
		include 'userservice-class.php';
	}
	catch (Exception $e) {
		echo "Error: " . $e->getMessage();
	}
	$mydb = new Database();
	$auth = new Auth($mydb);

	// Rating page is being accessed for a specific user, create a service object for them
	if (isset($_GET['user'])) {
		$service = new UserService($mydb, $_GET['user']);
	}
	// Login
	if (isset($_POST['login']) && $_POST['login'] == 'Login') {
		$auth->login($_POST['username'], $_POST['pass']);
	}

	// Logout
	if (isset($_POST['logout']) && $_POST['logout'] == 'Logout') {
		$auth->logout();
	}

	if (isset($_POST['rate'])) {
		$service->addRating($_POST['rating']);
		header('Location:profile.php');
	}
	


	        
?>

<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- <meta name="description" content="">
<meta name="author" content="">-->
<link rel="shortcut icon" href="./images/favicon.png">

<title>Online Office Hours</title>

<!-- Bootstrap core CSS -->
<link href="./resources/css/bootstrap.css" rel="stylesheet">

<!-- Custom styles for this template -->
<link href="./resources/css/sticky-footer-navbar.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
</head>

<body>

<!-- Wrap all page content here -->
<div id="wrap">

<!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                    <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand" href="./">Online Office Hours</a>
                    </div>
                    <div class="navbar-collapse collapse">
                            <ul class="nav navbar-nav">
                                    <li><a href="./profile.php">Profile</a></li>
                                    <li><a href="./by_subject.php">By Subject</a></li>
                                    <li><a href="./by_user.php">By User</a></li>
                            </ul>
                            <!--Login Bar-->
                            <?php
                            if( isset($_SESSION['username']))
                            {
                                    $temp = file_get_contents('./resources/html/logout_form.html');
                                    echo str_replace("username", $_SESSION['username'], $temp );
                            }
                            else
                            {
                                    echo file_get_contents('./resources/html/login_form.html');
                            }
                            ?>
                    </div><!--/.nav-collapse -->
            </div>
    </div>

	<!-- Begin page content -->
	<div class="container">
			<br>
			
			<div class="page-header">
	      		<h1>Rate a User</h1>
				<p>Rate your interaction with a specific tutor you've met with.</p>
			</div>
			<div class="well col-md-6">
				<h2>Leave a rating for user <a href="profile.php?user=<?php echo $_GET['user']; ?>"><?php echo $_GET['user']; ?></a></h2>
				<br>
				<div class="radio-inline">
					<form class="form-vertical" method="post">
						<label class="radio-inline">
  							<input name="rating" type="radio" id="inlineradio0" value="0"> 0
						</label>
						<label class="radio-inline">
  							<input name="rating" type="radio" id="inlineradio1" value="1"> 1
						</label>
						<label class="radio-inline">
  							<input name="rating" type="radio" id="inlineradio2" value="2"> 2
						</label>
						<label class="radio-inline">
  							<input name="rating" type="radio" id="inlineradio3" value="3"> 3
						</label>
						<label class="radio-inline">
  							<input name="rating" type="radio" id="inlineradio4" value="4"> 4
						</label>
						<label class="radio-inline">
  							<input name="rating" type="radio" id="inlineradio5" value="5"> 5
						</label>
						<br>
						<button class="btn btn-md btn-primary btn-block" type="submit" name="rate">Submit</button>
					</form>
				</div>
			</div>
			
		</div>

		<div id="footer">
		  <div class="container">
		  </div>
		</div>



<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="./resources/javascript/bootstrap.js"></script>
</body>
</html>
