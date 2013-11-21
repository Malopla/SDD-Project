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
	$student = new Student($mydb, $_SESSION['username']);
	$tutor = new Student($mydb, $_SESSION['username']);
	// Login
	if (isset($_POST['login']) && $_POST['login'] == 'Login') {
		$auth->login($_POST['username'], $_POST['pass']);
	}

	// Logout
	if (isset($_POST['logout']) && $_POST['logout'] == 'Logout') {
		$auth->logout();
	}
	 
	// Registration
	if (isset($_POST['register']) && $_POST['register'] == 'Register') {
		$msg=$auth->validateRegistration();
		if ($msg == "Success, you can now log in.")
			$auth->register($_POST['username'],$_POST['pass'],$_POST['email']);
	}

	// Create service object
	if (isset($_GET['user'])) {
		$service = new UserService($mydb, $_GET['user']);
	}
	else {
		$service = new UserService($mydb, $_SESSION['username']);
	}
	
	// Is the currently logged in user the owner of this profile page
	$isowner = $auth->isOwner();

	//Add availability
	if(isset($_POST['addAvailability'])){
		if ($_POST['day'] == 'Add Availability') {
			$msg = 'Needs to be an actual day.';
		}
		else {
			$service->addAvailability($_POST['day']);
			header('Location:location.php');
		}
	}
	
	// Delete availability
	if (isset($_POST['delete']) && $_POST['delete'] == 'deleteAvailable') {
		$service->removeAvailability($_POST['deleteAvailable']);
	} 

	// Add a student subject
	if(isset($_POST['studentAdd'])){
		if ($_POST['studentSubject'] == 'Add a Subject') {
			$msg = 'Needs to be an actual subject.';
		}
		else {
			$student->addSubject($_POST['studentSubject']);
			header('Location:location.php');
		}
	}

	//Add a tutor subject
	if(isset($_POST['tutorAdd'])){
		if ($_POST['tutorSubject'] == 'Add a Subject') {
			$msg = 'Needs to be an actual subject.';
		}
		else {
			$tutor->addSubject($_POST['tutorSubject']);
			header('Location:location.php');
		}
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
	if (isset($_POST['delete']) && $_POST['delete'] == 'studentDelete') {
		$student->removeSubject($_POST['deleteSubject']);
	}
	// Delete tutor subject
	else if (isset($_POST['delete']) && $_POST['delete'] == 'tutorDelete') {
		$tutor->removeSubject($_POST['deleteSubject']);
	}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<title>Online Office Hours</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<style type="text/css" media="all"> @import "style.css"; </style>
		<script type="text/javascript" src="resources/javascript.js"></script>
	</head>

	<body>
		<div id="wrapper">
	
      <div id="logo">
        <a><img src="images/Logo.png" height="110px" width="187px" alt="logo" href = "index.php" /></a>
      </div>
	  
      <br/>
	  
      <div id="menuBar">
			<ul id="menu">
				<li id="menu-1"><a href="index.php" title="Home"><span>Home</span></a></li>
				<li id="menu-2"><a href="location.php" title="By Location" class="current"><span>My Profile</span></a></li>
				<li id="menu-3"><a href="lost.php" title="By Lost"><span>By Subject</span></a></li>
				<li id="menu-4"><a href="found.php" title="By Found"><span>By User</span></a></li>
			</ul>
		<div class="searchSite">
			<form id="searchForm"  action="location.php" method='get' >
				<select name="search" id = "locationBar">
					 <?php 
					 $sql = 'SELECT * FROM all_subjects';
					 $stmt = $dbconn->prepare($sql);
					 $stmt->execute(array());
					 foreach ($stmt as $row): ?>
					   <option value="<?php echo $row['subject'] ?>"><?php echo $row['subject'] ?></option>	
					 <?php endforeach; ?>
				</select>	
		      <input type="submit" class="searchButton" id = "locSearch"  value=""  />
          </form>
        </div>          
      </div>
	  
      <br/>
 
		<div id="loginBar">       		
		<!-- If Logged in -->
		<?php if (isset($_SESSION['username'])){ ?>
			<h2 style="display:inline;">Hello <?php echo htmlentities($_SESSION['username']) ?></h2>
			<?php if (isset($msg)) echo "<p>$msg</p>" ?>

			<!-- Logout Button -->
			<form method="post" action="index.php">
				<input name="logout" class = "submitButton" id = "out" type="submit" value="Logout" />
			</form>

			<?php } else { ?>
			<?php if (isset($err)) echo "<p>$err</p>" ?>
			<form method="post" action="location.php">
				<span><label >Username </label><input type="text" name="username" /> </span>
				<span><label >Password </label><input type="password" name="pass" /> </span>
				<span><input name="login" class = "submitButton" type="submit" value="Login" /></span>
			</form>
			<?php } ?>       
		</div>
		
		<div id="content">
			<?php if(isset($_GET['search'])) { ?>
			SEARCH RESULTS
			<br/> 
			<!-- Hidden by Default -> Displays only when searched -->
			<div id="searchresultsIndex" class="visible">
			<?php 
			$sql_count = "SELECT COUNT(*) AS count FROM lost_found WHERE location = :search";
			$stmt = $dbconn->prepare($sql_count);
			$stmt->execute(array(':search' => $_GET['search']));
			$num = $stmt->fetch(PDO::FETCH_ASSOC);
			if (intval($num['count']) == 0) { ?>
				<p>There is nothing from there on here.</p>
			<?php } else { 
				$sql = "SELECT * FROM lost_found WHERE location = :search ORDER BY location ASC";
				$stmt = $dbconn->prepare($sql);
				$stmt->execute(array(':search' => $_GET['search']));
				foreach ($stmt as $row){
					$username = $row['username'];
					$sql = 'SELECT * FROM profile WHERE username=:username';
					$stmt = $dbconn->prepare($sql);
					$stmt->execute(array(':username' => $username));
					$result = $stmt->fetch(PDO::FETCH_ASSOC); ?>
					<div class="name">
						<?php echo $row['username'] ?> : <br/>
						<span class = "data"><?php echo $result['firstname'];?></span>
						<span class = "data"><?php echo $result['lastname'];?></span>
						<span class = "data"><?php echo $result['email'];?></span>
						<span class = "data"><?php echo $result['phone'];?></span>
					</div>
					<div class="message">
					   <?php echo $row['message']; ?>
						<div class="clear"></div>
						<div class="time" style="float:left;font-size:14px;"><?php echo $row['time']." : ".$row['location']; ?></div>
						<div class="lostorfound">
							<?php
							if ($row['found'] == 'Found')
								echo "Found";
							else
								echo "Lost";
							?>
						</div>
						<div class="clear"></div>
						<?php echo "<hr />"; ?>
					</div>
					<div class="clear"></div>
				<?php } ?>
			<?php } ?>
		</div>
		<?php echo "<hr />"; ?>
		<br/>
		<?php } ?>
		
		<div id="profileDiv">
			<?php if (isset($_SESSION['username'])){ 
			$username=$_SESSION['username']; ?>
			<?php if(isset($_POST['editprof'])) {
				$sql = 'SELECT * FROM profile WHERE username=:username';
				$stmt = $dbconn->prepare($sql);
			     $stmt->execute(array(':username' => $username));
				$result = $stmt->fetch(PDO::FETCH_ASSOC); ?>
			
				<h1 id="title">Update Your Profile</h1>
				<?php if (isset($msg)) echo "<p>$msg</p>" ?>
					<form id ="createForm" action="location.php" method="post">
						<label>First Name:</label>
						<input type="text" id="firstname" class = "create" name="firstname" value="<?php echo htmlentities($result['firstname']) ?>"></input><br/>

						<label>Last Name:</label>
						<input type="text" class = "create" id="lastname" name="lastname" value="<?php echo htmlentities($result['lastname']) ?>"></input><br/>

						<label>*Email</label> 
						<input type="text" class = "create"  id="email" name="email" value="<?php echo htmlentities($result['email']) ?>"></input><br/>

						<label>Phone Number</label> 
						<input type="text" class = "create"  id="phone" name="phone" value="<?php echo htmlentities($result['phone']) ?>"></input><br/>

						<label>Bio</label> 
						<input type="text" class = "create"  id="bio" name="bio" value="<?php echo htmlentities($result['bio']) ?>"></input><br/>
						
						<label>Student Price</label> 
						<input type="text" class = "create"  id="sprice" name="sprice" value="<?php echo htmlentities($result['studentprice']) ?>"></input><br/>

						<label>Tutor Price</label> 
						<input type="text" class = "create"  id="tprice" name="tprice" value="<?php echo htmlentities($result['tutorprice']) ?>"></input><br/>
						<p>*Email is required. All other fields are optional.</p>
						<br/>
						<input  type="hidden" name="prid" value="<?php echo htmlentities($result['prid']) ?>">
						<input type="submit" class = "submitButton" id = "create" value="Update" name="update"/>
					</form>	
			<?php } else { 
				$profile=$service->getProfile();?>
				<div id="profileInfoArea">
					First Name: <?php echo htmlentities($profile['firstname']) ?><br/>
					Last Name: <?php echo htmlentities($profile['lastname']) ?><br/>
					Email: <?php echo htmlentities($profile['email']) ?><br/>
					Phone: <?php echo htmlentities($profile['phone']) ?><br/>
					Bio: <?php echo htmlentities($profile['bio']) ?><br/>
					<?php if ($isowner) { ?>
					<form action="location.php" method="post">                   
						<input type="submit" class ="submitButton" value="Edit Profile" name="editprof"/>				  
					</form> <?php } ?>

				<!-- List student subjects -->
				<h3> Student Subjects </h3>
				<?php foreach($student->getSubjects() as $subject) {
				 echo htmlentities($subject).'<br />';  ?>
					<form method="post" action="location.php">
					<input type="hidden" name="deleteSubject" value="<?php echo htmlentities($subject) ?>">
					<?php if($isowner) { ?> 
					<input class = "deleteButton" type="submit" name="delete" value="studentDelete" />
					<?php } ?>
					</form>
				<?php } ?>

				<!-- Add Student Subject -->
				<?php if($isowner) { ?>
				<form name="myForm" method="post" action="location.php">
					<select name="studentSubject">
						<option value = "Add a Subject"> Add a Subject </option>
						<?php 
						$stmt=$mydb->queryNoFetch('SELECT * FROM all_subjects',array());
						foreach ($stmt as $row): ?>
							<option value="<?php echo $row['subject'] ?>"><?php echo $row['subject'] ?></option>	
						<?php endforeach; ?>
					</select>
					<input type="submit" class = "deleteButton" value="Add Subject" name="studentAdd"  />
				</form>
				<?php } ?>

				<!-- List Tutor Subjects -->
				<h3> Tutoring Subjects </h3>
				<?php foreach($tutor->getSubjects() as $subject) {
					echo htmlentities($subject).'<br />';  ?>
					<form method="post" action="location.php">
						<input type="hidden" name="deleteSubject" value="<?php echo htmlentities($subject) ?>">
						<?php if($isowner) { ?>  
						<input class = "deleteButton" type="submit" name="delete" value="tutorDelete" />
						<?php } ?>
					</form>
				<?php } ?>
	
				<!-- Add Tutor Subject -->
				<?php if($isowner) { ?> 
				<form name="myForm" method="post" action="location.php">
					<select name="tutorSubject">
						<option value = "Add a Subject"> Add a Subject </option>
						<?php 
						$stmt=$mydb->queryNoFetch('SELECT * FROM all_subjects',array());
						foreach ($stmt as $row): ?>
							<option value="<?php echo $row['subject'] ?>"><?php echo $row['subject'] ?></option>	
						<?php endforeach; ?>
					</select>
					<input type="submit" class = "deleteButton" value="Add Subject" name="tutorAdd"  />
				</form>
				<?php } ?>

				<!-- List Availability -->
				<h3> Availability </h3>
				<?php foreach($service->getAvailability() as $day) {
					echo htmlentities($day).'<br />';  ?>
					<form method="post" action="location.php">
						<input type="hidden" name="deleteAvailable" value="<?php echo htmlentities($day) ?>">
						<?php if($isowner) { ?>  
						<input class = "deleteButton" type="submit" name="delete" value="deleteAvailable" />
						<?php } ?>
					</form>
				<?php } ?>
	
				<!-- Add Availability -->
				<?php if($isowner) { ?> 
				<form name="myForm" method="post" action="location.php">
					<select name="day">
						<option value = "Add a Subject"> Add Availability </option>
						<option value="Monday">Monday</option>
						<option value="Tuesday">Tuesday</option>	
						<option value="Wednesday">Wednesday</option>	
						<option value="Thursday">Thursday</option>	
						<option value="Friday">Friday</option>	
						<option value="Saturday">Saturday</option>	
						<option value="Sunday">Sunday</option>		
					</select>
					<input type="submit" class = "deleteButton" value="Add Availability" name="addAvailability"  />
				</form>
				<?php } ?>

			</div>
			<?php } ?>
		<?php } ?>
      </div>
		</div>
		<div id="footer">
			Online Office Hours
		</div>
	</body>
</html>
