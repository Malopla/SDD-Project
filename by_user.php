<!DOCTYPE html>

<?php session_start();
  
  // Includes db.php functions and enables connection to the database
  try {	
	require_once('resources/database/db.php');
	require_once('db-class.php');
	require_once('auth-class.php');
	require_once('user-class.php');
	require_once('userservice-class.php');
  }
  catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
   $mydb = new Database();
	$auth = new Auth($mydb);
	// Login
	if (isset($_POST['login']) && $_POST['login'] == 'Login') {
		$auth->login($_POST['username'], $_POST['pass']);
	}

	// Logout
	if (isset($_POST['logout']) && $_POST['logout'] == 'Logout') {
		$auth->logout();
	}

	// Create student and tutor objects
	$student = new Student($mydb, $_SESSION['username']);
	$tutor = new Tutor($mydb, $_SESSION['username']);
	        
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
                                    <li class="active"><a href="./by_user.php">By User</a></li>
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
          		<h1>All Users</h1>
		    </div>
			<table class="table">
			<tr>
				<form id='myform1' method="post" action="by_user.php">
					<input type="hidden" name="sort" value="username" /> 
					<th><a onclick="document.getElementById('myform1').submit(); return false;">Username</a></th>
				</form>
				<form id='myform2' method="post" action="by_user.php">
					<input type="hidden" name="sort" value="firstname" /> 
					<th><a onclick="document.getElementById('myform2').submit(); return false;">First Name</a></th>
				</form>
				<form id='myform3' method="post" action="by_user.php">
					<input type="hidden" name="sort" value="lastname" /> 
					<th><a onclick="document.getElementById('myform3').submit(); return false;">Last Name</a></th>
				</form>
				<form id='myform4' method="post" action="by_user.php">
					<input type="hidden" name="sort" value="email" /> 
					<th><a onclick="document.getElementById('myform4').submit(); return false;">Email</a></th>
				</form>
				<form id='myform5' method="post" action="by_user.php">
					<input type="hidden" name="sort" value="phone" /> 
					<th><a onclick="document.getElementById('myform5').submit(); return false;">Phone Number</a></th>
				</form>
				<form id='myform6' method="post" action="by_user.php">
					<input type="hidden" name="sort" value="tutorprice" /> 
					<th><a onclick="document.getElementById('myform6').submit(); return false;">Tutor Price</a></th>
				</form>
				<form id='myform7' method="post" action="by_user.php">
					<input type="hidden" name="sort" value="studentprice" /> 
					<th><a onclick="document.getElementById('myform7').submit(); return false;">Student Price</a></th>
				</form>
				<form id='myform7' method="post" action="by_user.php">
					<input type="hidden" name="sort" value="rating" /> 
					<th><a onclick="document.getElementById('myform7').submit(); return false;">Rating</a></th>
				</form>
				<th>Profile</th>
			<tr>
			<?php 
						if (isset($_POST['sort'])) {
							$order=$_POST['sort'];
						}
						else {						
							$order="username";
						}
						$users=$mydb->queryNoFetch('SELECT * FROM profile ORDER BY '.$order, array(':username' => $user));
						while($user=$users->fetch()) { ?>

						<tr>
							<td><?php echo $user['username'] ?></td>
							<td ><?php echo $user['firstname'];?></td>
							<td><?php echo $user['lastname'];?></td>
							<td><?php echo $user['email'];?></td>
							<td><?php echo $user['phone'];?></td>
							<td>&#36;<?php echo $user['tutorprice'];?>/hr</td>
							<td>&#36;<?php echo $user['studentprice'];?>/hr</td>
							<td><?php echo round($user['rating'], 2);?></td>
							<td><a href="profile.php?user=<?php echo $user['username']; ?>">profile page</a></td>
						</tr>
					<?php } ?>
			</table>
		                    
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
