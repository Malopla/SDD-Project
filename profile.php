<!DOCTYPE html>

<?php session_start();
  	function sendMeetingMail($student, $tutor, $subject) {
		$studentService = new UserService($mydb, $student);
		$tutorService = new UserService($mydb, $tutor);
		$studentProfile = $studentService->getProfile();
		$tutorProfile = $tutorService->getProfile();
		
		$studentMail = <<<SM
			Hi {$student},

			This is a reminder for your upcoming meeting with {$tutor} about {$subject}.  Their contact information is:
			Email: {$studentProfile['email']}
			Phone: {$studentProfile['phone']}

			Thank you for using Online Office Hours!
SM;
		
		$tutorMail = <<<TM
		Hi {$tutor},

		This is a notification that {$student} has scheduled a meeting about {$subject}.  Their contact information is:
		Email: {$tutorProfile['email']}
		Phone: {$tutorProfile['phone']}

		Thank you for using Online Office Hours!
TM;
		
	echo mail($tutorProfile['email'], "Online Office Hours Meeting Scheduled", "test");
	echo mail($studentProfile['email'], "Online Office Hours Meeting Scheduled", "test");
	
	} 
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
        

        // Login
        if (isset($_POST['login'])) {
                $auth->login($_POST['username'], $_POST['pass']);
        }

        // Logout
        if (isset($_POST['logout'])) {
                $auth->logout();
        }
        
        // Registration
        if (isset($_POST['register'])) {
                $msg=$auth->validateRegistration();
                if ($msg == "Success, you can now log in.")
                        $auth->register($_POST['username'],$_POST['pass'],$_POST['email']);
        }

        // Create service object
        if (isset($_GET['user'])) {
                $service = new UserService($mydb, $_GET['user']);
        		$student = new Student($mydb, $_GET['user']);
        		$tutor = new Tutor($mydb, $_GET['user']);
        }
        else {
                $service = new UserService($mydb, $_SESSION['username']);
        		$student = new Student($mydb, $_SESSION['username']);
        		$tutor = new Tutor($mydb, $_SESSION['username']);
        }
        
        // Is the currently logged in user the owner of this profile page
        $isowner = $auth->isOwner();

        //Add availability
        if(isset($_POST['addAvailability'])){
                if ($_POST['day'] != 'Add Availability') {
                        $service->addAvailability($_POST['day']);
                        header('Location:profile.php');
                }
        }
        
        // Delete availability
        if (isset($_POST['delete'])) {
                $service->removeAvailability($_POST['deleteAvailable']);
        }

        // Add a student subject
        if(isset($_POST['studentAdd']) && $_POST['studentSubject'] != 'Add a Subject'){
                $student->addSubject($_POST['studentSubject']);
                header('Location:profile.php');
        }

        //Add a tutor subject
        if(isset($_POST['tutorAdd']) && $_POST['tutorSubject'] != 'Add a Subject'){
                $tutor->addSubject($_POST['tutorSubject']);
                header('Location:profile.php');
        }
  
        // Update -> Make sure updates are valid
        if(isset($_POST['update'])) {
                if (!isset($_POST['email']) || empty($_POST['email'])) {
                        $msg = "You must have an email.";
                }
                else{
                        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                                $service->updateProfile($_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['phone'], $_POST['bio'], $_POST['sprice'], $_POST['tprice']);
                        }
                        else{
                                $msg = "This must be an email.";
                        }
                }
        }
  
        // Delete student subject
        if (isset($_POST['studentDelete']) && $_POST['studentDelete'] == 'Remove Subject') {
                $student->removeSubject($_POST['deleteSubject']);
        }
        // Delete tutor subject
        else if (isset($_POST['tutorDelete']) && $_POST['tutorDelete'] == 'Remove Subject') {
                $tutor->removeSubject($_POST['deleteSubject']);
        }
		
		if(isset($_POST['studentmeet'])) {
			sendMeetingEmail($_SESSION['username'], $_GET['user'], $_POST['meeting']);
		}
?>

<html lang="en">
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- <meta name="description" content="">
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
                                                <li class="active"><a href="./profile.php">Profile</a></li>
                                                <li><a href="./by_subject.php">By Subject</a></li>
                                                <li><a href="./by_user.php">By User</a></li>
                                        </ul>
                                        <?php
                                        if( isset($_SESSION['username']))
                                        {
                                                $temp = file_get_contents('./resources/html/logout_form.html');
                                                echo str_replace("username", $_SESSION['username'], $temp );

                                        }
                                        else
                                        {
                                                echo file_get_contents('./resources/html/login_form.html');
                                        }?>
                                </div><!--/.nav-collapse -->
                        </div>
                </div>

                <!-- Begin page content -->
                <div class="container">
            			<br>
                        <div class="page-header">
                                <h1>Profile</h1>
                        </div>
                        <div class="row">
							<div class="col-md-6">
                                <?php if (isset($_SESSION['username'])){
                                
                                	 if(isset($_POST['editprof'])) {
                                        $profile=$service->getProfile(); ?>
                                
                                        <h1 id="title">Update Your Profile</h1>
                                        <?php if (isset($msg)) echo "<p>$msg</p>" ?>
                                                <form class="form-horizontal" action="profile.php" method="post">
                                                        <label>First Name:</label>
                                                        <input type="text" id="firstname" class = "form-control" name="firstname" value="<?php echo htmlentities($profile['firstname']) ?>"></input><br/>

                                                        <label>Last Name:</label>
                                                        <input type="text" class = "form-control" id="lastname" name="lastname" value="<?php echo htmlentities($profile['lastname']) ?>"></input><br/>

                                                        <label>*Email</label>
                                                        <input type="text" class = "form-control" id="email" name="email" value="<?php echo htmlentities($profile['email']) ?>"></input><br/>

                                                        <label>Phone Number</label>
                                                        <input type="text" class = "form-control" id="phone" name="phone" value="<?php echo htmlentities($profile['phone']) ?>"></input><br/>

                                                        <label>Bio</label>
                                                        <textarea type="text" class = "form-control" rows="5" id="bio" name="bio" value="<?php echo htmlentities($profile['bio']) ?>"></textarea><br/>
                                                        
                                                        <label>Student Price</label>
                                                        <div class="input-group">
                                                                <span class="input-group-addon">$</span>
                                                                <input type="text" class = "form-control" id="sprice" name="sprice" value="<?php echo htmlentities($profile['studentprice']) ?>">
                                                                </input>
                                                        </div>

                                                        <label>Tutor Price</label>
                                                        <div class="input-group">
                                                                <span class="input-group-addon">$</span>
                                                                <input type="text" class = "form-control" id="tprice" name="tprice" value="<?php echo htmlentities($profile['tutorprice']) ?>">
                                                                </input>
                                                        </div>

                                                        <p>*Email is required. All other fields are optional.</p>
                                                        <input type="hidden" name="prid" value="<?php echo htmlentities($profile['prid']); ?>">
                                                        <input type="submit" class = "btn btn-primary" value="Update" name="update"/>
                                                </form>        
                                <?php } else {
                                        $profile=$service->getProfile();?>
                                        First Name: <?php echo htmlentities($profile['firstname']) ?><br/>
                                        Last Name: <?php echo htmlentities($profile['lastname']) ?><br/>
                                        Email: <?php echo htmlentities($profile['email']) ?><br/>
                                        Phone: <?php echo htmlentities($profile['phone']) ?><br/>
										Student Price: &#36;<?php echo htmlentities($profile['studentprice']) ?>/hr<br/>
										Tutor Price: &#36;<?php echo htmlentities($profile['tutorprice']) ?>/hr<br/>
                                        Bio: <?php echo htmlentities($profile['bio']) ?><br/>
                                        <?php if ($isowner) { ?>
                                        <form action="profile.php" method="post">
                                                <input type="submit" class ="btn btn-primary" value="Edit Profile" name="editprof"/>                                
                                        </form> <?php } ?>
									</div>
									<div class="col-md-4">

                                        <!-- List student subjects -->
                                        <h3> Student Subjects </h3>
                                        <?php foreach($student->getSubjects() as $subject) {
												if($isowner) { ?>
													<a href="matches.php?subject=<?php echo htmlentities($subject); ?>&role=tutor"><?php echo htmlentities($subject); ?></a>
													<br>

		                                            <form method="post" action="profile.php">
	                                           	 		<input type="hidden" name="deleteSubject" value="<?php echo htmlentities($subject) ?>">
		                                            	<input class = "btn btn-danger btn-xs" type="submit" name="studentDelete" value="Remove Subject" />
		                                            </form>
												<?php } 
												else {
													echo htmlentities($subject); ?>
		                                            <form method="post" action="profile.php" 
														onsubmit="return confirm('Do you wish to confirm a meeting with <?php echo $_GET['user'];?> about <?php echo $subject ?>?');">
	                                           	 		<input type="hidden" name="meeting" value="<?php $subject ?>">
		                                            	<input class = "btn btn-primary btn-xs" type="submit" name="meet" value="Meet" />
		                                            </form>
													<?php } 
												
                                        } ?>

                                        <!-- Add Student Subject -->
                                        <?php if($isowner) { ?>
                                        <form name="myForm" method="post" action="profile.php">
                                                <select name="studentSubject">
                                                        <option value = "Add a Subject"> Add a Subject </option>
                                                        <?php
                                                        $stmt=$mydb->queryNoFetch('SELECT * FROM all_subjects',array());
                                                        foreach ($stmt as $row): ?>
                                                                <option value="<?php echo $row['subject'] ?>"><?php echo $row['subject'] ?></option>        
                                                        <?php endforeach; ?>
                                                </select>
                                                <input type="submit" class = "btn btn-primary" value="Add Subject" name="studentAdd" />
                                        </form>
                                        <?php } ?>

                                        <!-- List Tutor Subjects -->
                                        <h3> Tutoring Subjects </h3>
                                        <?php foreach($tutor->getSubjects() as $subject) {
											if($isowner) { ?>
												<a href="matches.php?subject=<?php echo htmlentities($subject); ?>&role=student"><?php echo htmlentities($subject); ?></a>
												<br>

	                                            <form method="post" action="profile.php">
                                           	 		<input type="hidden" name="deleteSubject" value="<?php echo htmlentities($subject) ?>">
	                                            	<input class = "btn btn-danger btn-xs" type="submit" name="tutorDelete" value="Remove Subject" />
	                                            </form>
											<?php } 
											else {
												echo htmlentities($subject); ?>
	                                            <form method="post" action="profile.php" 
													onsubmit="return confirm('Do you wish to confirm a meeting with <?php echo $_GET['user'];?> about <?php echo $subject ?>?');">
                                           	 		<input type="hidden" name="meeting" value="<?php $subject ?>">
	                                            	<input class = "btn btn-primary btn-xs" type="submit" name="meet" value="Meet" />
	                                            </form>
											<?php } 
												
                                        } ?>
                
                                        <!-- Add Tutor Subject -->
                                        <?php if($isowner) { ?>
                                        <form name="myForm" method="post" action="profile.php">
                                                <select name="tutorSubject">
                                                        <option value = "Add a Subject"> Add a Subject </option>
                                                        <?php
                                                        $stmt=$mydb->queryNoFetch('SELECT * FROM all_subjects',array());
                                                        foreach ($stmt as $row): ?>
                                                                <option value="<?php echo $row['subject'] ?>"><?php echo $row['subject'] ?></option>        
                                                        <?php endforeach; ?>
                                                </select>
                                                <input type="submit" class = "btn btn-primary" value="Add Subject" name="tutorAdd" />
                                        </form>
                                        <?php } ?>

                                        <!-- List Availability -->
                                        <h3> Availability </h3>
                                        <?php foreach($service->getAvailability() as $day) {
                                                echo htmlentities($day).'<br />'; ?>
                                                <form method="post" action="profile.php">
                                                        <input type="hidden" name="deleteAvailable" value="<?php echo htmlentities($day) ?>">
                                                        <?php if($isowner) { ?>
                                                        <input class = "btn btn-danger btn-xs" type="submit" name="delete" value="Remove Day" />
                                                        <?php } ?>
                                                </form>
                                        <?php } ?>
                
                                        <!-- Add Availability -->
                                        <?php if($isowner) { ?>
                                        <form name="myForm" method="post" action="profile.php">
                                                <select name="day">
                                                        <option value = "Add Availability"> Add Availability </option>
                                                        <option value="Monday">Monday</option>
                                                        <option value="Tuesday">Tuesday</option>        
                                                        <option value="Wednesday">Wednesday</option>        
                                                        <option value="Thursday">Thursday</option>        
                                                        <option value="Friday">Friday</option>        
                                                        <option value="Saturday">Saturday</option>        
                                                        <option value="Sunday">Sunday</option>                
                                                </select>
                                                <input type="submit" class = "btn btn-primary" value="Add Availability" name="addAvailability" />
                                        </form>
                                        <?php } ?>

                            
                                <?php } ?>	
								</div>
                        <?php } ?>
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
