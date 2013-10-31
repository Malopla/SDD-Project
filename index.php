<?php session_start();
  
  // Includes db.php functions and enables connection to the database
  try {	
	include 'resources/database/db.php';
	include 'db-class.php';
	include 'auth-class.php';
	include 'user-class.php';
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
	 
  // Registration Field
  if (isset($_POST['register']) && $_POST['register'] == 'Register') {
		$msg=$auth->validateRegistration();
		if ($msg == "Success, you can now log in.")
			$auth->register($_POST['username'],$_POST['pass'],$_POST['email']);
  }
  
  // If logged in
  if (isset($_SESSION['username'])) {
    $username=$_SESSION['username'];
	
	// if you are the user who posted the item -> enable delete
    $sql = "SELECT del FROM permissions WHERE username= :username";
	$perm_stmt = $dbconn->prepare($sql);
	$perm_stmt->execute(array(':username' => $username));
	$perm_stmt->bindColumn(1, $permission);
	$perm_stmt->fetch(PDO::FETCH_BOUND);
	if ($permission == 'true'){
      $del=true;
	}
	else {
	  $del=false;
	}	
	// If you have twitter permisions -> Enable twitter button
	$sql = "SELECT twitter FROM permissions WHERE username= :username";
	$perm_stmt = $dbconn->prepare($sql);
	$perm_stmt->execute(array(':username' => $username));
	$perm_stmt->bindColumn(1, $twitter);
	$perm_stmt->fetch(PDO::FETCH_BOUND);
	if ($twitter == 'true'){
      $twit=true;
	}
	else {
	  $twit=false;
	}
  }
  
  // Create a lost/found statement from the message area
  if(isset($_POST['create'])){
    if ($_POST['location'] == 'Location') {
	  $msg = 'Needs to be an actual location.';
	}
	else if (empty($_POST['message'])){
	  $msg = 'Fill in the text area.';
	}
	else {
      $username=$_SESSION['username'];
	  $sql = 'INSERT INTO lost_found (username, found, location, message) VALUES (:username, :found, :location, :message)';
	  $stmt = $dbconn->prepare($sql);
	  $stmt->execute( array(':username' => $username,':found' => $_POST['found'], ':location' => $_POST['location'], ':message' => $_POST['message']));
	  header('Location:index.php');
	}
  }  
  
  // Update -> Make sure updates are valid
  if(isset($_POST['update'])) {
    if (!isset($_POST['email']) || empty($_POST['email'])) {
      $msg = "You must have an email.";
	}

	else{
	  if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $sql = 'UPDATE profile SET firstname = :firstname, lastname = :lastname, email = :email, phone = :phone WHERE prid=:prid';
	    $stmt = $dbconn->prepare($sql);
	    $stmt->execute( array(':firstname' => $_POST['firstname'], ':lastname' => $_POST['lastname'], ':email' => $_POST['email'], ':phone' => $_POST['phone'], ':prid' => $_POST['prid']));
      }
      else{
       $msg = "This must be an email.";
      }
    }
  }
  
  // Delete Code
  if (isset($_POST['delete']) && $_POST['delete'] == 'Delete') {
	$stmt = $dbconn->prepare("DELETE FROM lost_found WHERE pid=(:pid)");
	$stmt->execute(array(':pid' => $_POST['pid']));
  }

 ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Lost &amp; Found </title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <style type="text/css" media="all"> @import "style.css"; </style>
    <script type="text/javascript" src="resources/javascript.js"></script>
  </head>

  <body>
    <div id="wrapper">
	
      <div id="logo">
        <a><img src="images/Logo.png" height="110px" width="187px" alt="logo" href = "index.php" /></a>
      </div>
      <div id="menuBar">
        <ul id="menu">
          <li id="menu-1"><a href="index.php" title="Home" class="current"><span>Home</span></a></li>
          <li id="menu-2"><a href="location.php" title="My Profile"><span>My Profile</span></a></li>
          <li id="menu-3"><a href="lost.php" title="Subjects"><span>By Subject</span></a></li>
          <li id="menu-4"><a href="found.php" title="Tutors"><span>By User</span></a></li>
        </ul>
		
		<div class="searchSite">
          <form id="searchForm"  action="index.php" method='get' >
            <input type="text" class="search" name="search" id="search" placeholder="Search users..." />
            <input type="submit" class="searchButton"  value=""  />
          </form>
        </div>          
      </div>
	  


	  <div id="loginBar">       		
		<!-- If Logged in -->
		<?php if (isset($_SESSION['username'])){ ?>
		  <h2 style="display:inline;">Hello <?php echo htmlentities($_SESSION['username']) ?></h2>
		  <?php if (isset($msg)) echo "<p>$msg</p>" ?>
		  
			<!-- Logout Button -->
		  <form method="post" action="index.php">
			<input name="logout" class = "submitButton" style="display: inline;" id = "out" type="submit" value="Logout" />
		  </form>

	    <?php } else { ?>
		  <?php if (isset($err)) echo "<p>$err</p>" ?>
		  <form method="post" action="index.php">
		    <span><label >Username </label><input type="text" name="username" /> </span>
			<span><label >Password </label><input type="password" name="pass" /> </span>
		    <span><input name="login" class = "submitButton" type="submit" value="Login" /></span>
		  </form>
			<?php } ?>       
	  </div>
	  

		

	  <!-- Search Result Area  -->
	  <div id="content">
	    <?php if(isset($_GET['search'])) { ?>
		  SEARCH RESULTS
		  <br/>
		  <!-- 
				Hidden by Default -> Displays only when searched
		  -->
		  <div id="searchresultsIndex" class="visible">
		    <?php 
			$sql_count = "SELECT COUNT(*) AS count FROM lost_found WHERE message LIKE :search";
			$stmt = $dbconn->prepare($sql_count);
			$stmt->execute(array(':search' => '%'.$_GET['search'].'%'));
			$num = $stmt->fetch(PDO::FETCH_ASSOC);
			if (intval($num['count']) == 0) { ?>
			  <p>There is nothing like that on here.</p>
			<?php } else { 
			  $sql = "SELECT * FROM lost_found WHERE message LIKE :search ORDER BY time DESC";
			  $stmt = $dbconn->prepare($sql);
			  $stmt->execute(array(':search' => '%'.$_GET['search'].'%'));
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
		<!-- 
			Feed Area 
		-->
	    <div id="profileDiv">
				<?php if (!isset($_SESSION['username'])) { ?>
			  <h1>Sign Up</h1>
			   <?php if (isset($msg)) echo "<p>$msg</p>" ?>
			  <form method="post" action="index.php">
				 <label>Username: </label><input type="text" name="username" /><br/>
				<label>Email: </label><input type="text" name="email" /><br/>
				<label>Password: </label><input type="password" name="pass" /><br/>
				<label>Confirm Password: </label><input type="password" name="passconfirm" /><br/>
				<p>All fields are required</p>
				<input type="submit" class = "submitButton" name="register" value="Register" />
			  </form>
				<?php } ?>

          
        </div>
      </div>
    </div>
		<!--
			Footer: Nothing Special
		-->
    <div id="footer">
	  Online Office Hours
	</div>
  </body>
</html>
