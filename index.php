<!DOCTYPE html>

<?php /*session_start();
  
  // Includes db.php functions and enables connection to the database
  try {	
	include './resources/database/db.php';
  }
  catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }*/
  $_SESSION['username'] = 'bla';
?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<!--    <meta name="description" content="">
    <meta name="author" content="">-->
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">

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
					//echo file_get_contents('./resources/html/login_form.html');
					if( isset($_SESSION['username']))
					{
						//echo '<ul class="nav navbar-nav navbar-right"><li><a href="./profile.php">' . $_SESSION['username'] . '</a></li></ul>';
						$temp = file_get_contents('./resources/html/profile_dropdown.html');
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
        <div class="page-header">
			<!--<h1>Sticky footer with fixed navbar</h1>-->
		  
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
    <!--<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>-->
	<script src="./resources/jquery/jquery-1.10.2.min.js"></script>
    <script src="./resources/javascript/bootstrap.min.js"></script>
  </body>
</html>