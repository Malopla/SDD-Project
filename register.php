<!DOCTYPE html>

<?php session_start();
	include 'resources/database/db.php';
	include 'db-class.php';
	include 'auth-class.php';
	include 'user-class.php';

	$mydb = new Database();
	$auth = new Auth($mydb);
  // Registration Field
  if (isset($_POST['register'])) {
		$msg=$auth->validateRegistration();
		if ($msg == "Success, you can now log in.")
			$auth->register($_POST['username'],$_POST['pass'],$_POST['email']);
			$auth->login($_POST['username'], $_POST['pass']);
			header('Location:profile.php');
  }
?>

<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!--<meta name="description" content="">
<meta name="author" content="">-->
<link rel="shortcut icon" href="./images/favicon.png">

<title>Online Office Hours</title>

<!-- Bootstrap core CSS -->
<link href="./resources/css/bootstrap.css" rel="stylesheet">

<!-- Custom styles for this template -->
<link href="./resources/css/signin.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
</head>

<body>
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
            </div>
    </div>
	<br>
	<br>
	<br>
	<div class="container">
		<div class="well col-sm-6">
			<h2 class="form-signin-heading">Registration</h2>
			<?php if ($msg == "Success, you can now log in.") { ?>
				<div class="alert alert-success"><?php echo $msg; ?></div>
			<?php }
			else if ($msg !='') { ?>
				<div class="alert alert-danger"><?php echo $msg; ?></div>
			<?php } ?>
			<form class="form-horizontal" method="post">
				<div class="form-group">
					<div class="col-md-8">
						<input type="text" class="form-control" placeholder="Username" required autofocus name="username">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-8">
						<input type="text" class="form-control" placeholder="Email" required autofocus name="email">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-8">
						<input type="password" class="form-control" placeholder="Password" name="pass" required>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-8">
		     		<input type="password" class="form-control" placeholder="Confirm Password" required name="passconfirm">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-8">
						<button class="btn btn-lg btn-primary btn-block" type="submit" name="register">Sign-up</button>
					</div>
				</div>
			</form>
		</div>

	</div> <!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="./resources/javascript/bootstrap.js"></script>
</body>
</html>
