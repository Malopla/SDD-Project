<?php session_start();
  
  // Includes db.php functions and enables connection to the database
  try {	
	include 'resources/database/db.php';
  }
  catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }
  
  // Login Setup
  if (isset($_POST['login']) && $_POST['login'] == 'Login') {
    $salt_stmt = $dbconn->prepare('SELECT salt FROM users WHERE username=:username');
    $salt_stmt->execute(array(':username' => $_POST['username']));
    $res = $salt_stmt->fetch();
    $salt = ($res) ? $res['salt'] : '';
    $salted = sha1($salt . $_POST['pass']);  
    $login_stmt = $dbconn->prepare('SELECT username FROM users WHERE username=:username AND password=:pass');
    $login_stmt->execute(array(':username' => $_POST['username'], ':pass' => $salted));  
    if ($user = $login_stmt->fetch()) {
      $_SESSION['username'] = $user['username'];
	  header('Location:index.php');
    }
    else {
      $err = 'Incorrect username or password.';
    }
  }
    
  // Logout and session destruction
  if (isset($_POST['logout']) && $_POST['logout'] == 'Logout') {
    unset($_SESSION['username']);
    setcookie(session_name(), '', time()-48000);
    session_destroy();
  }
  
  
  // Registration Field
  if (isset($_POST['register']) && $_POST['register'] == 'Register') {
    $email=$_POST['email'];
    if (!isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['pass']) || !isset($_POST['passconfirm']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['pass']) || empty($_POST['passconfirm'])) {
      $msg = "Please fill in all of the form fields.";
	}
	
	// Registration checking for matched passwords
    else if ($_POST['pass'] !== $_POST['passconfirm']) {
      $msg = "Passwords must match.";
    }
	// Email Validation --> Only accepts RPI email addresses
	else if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
	  // Separate the @
	  list($name, $domain) = explode('@', $email);
	  if ($domain == 'rpi.edu'){
	    $sql = "SELECT COUNT(*) AS count FROM users WHERE username = :username";
        $smt = $dbconn->prepare($sql);
        $smt->execute(array(':username' => $_POST['username']));
        $row = $smt->fetch(PDO::FETCH_ASSOC);
        // Account Validation -> Prevent users w/ duplicate names
		if (intval($row['count']) > 0) {
          $msg = "Account already exists. Sorry.";
        }
	  
		// Everything has validated -> Create the generic user account
	    else {	
          $salt = sha1(uniqid(mt_rand(), true));      
          $salted = sha1($salt . $_POST['pass']);      
          $stmt = $dbconn->prepare("INSERT INTO users (username, password, salt) VALUES (:username, :pass, :salt)");
          $stmt->execute(array(':username' => $_POST['username'], ':pass' => $salted, ':salt' => $salt));
          $msg = "Account created.  Go log in.";
	      $sql = 'INSERT INTO profile (username, email) VALUES (:username, :email)';
	      $stmt = $dbconn->prepare($sql);
	      $stmt->execute( array(':username' => $_POST['username'], ':email' => $email));  
        }
	  }
	  // Only RPI emails allowed
	  else {
	    $msg = 'This is not an RPI email. Try again.';
	  }
	}
	// Make sure that it's an email address being added
	else {
	  $msg = 'Not an email address. Try again.';
	}
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

  
  // Twitter Oauth
  require_once 'TwitterOAuth.php';

  if (isset($_POST['tweet']) && $_POST['tweet'] == 'Tweet') {
    $sql = "SELECT message FROM lost_found WHERE pid= :pid";
	$perm_stmt = $dbconn->prepare($sql);
	$perm_stmt->execute(array(':pid' => $_POST['pid']));
	$perm_stmt->bindColumn(1, $twits);
	$perm_stmt->fetch(PDO::FETCH_BOUND);
		
	// Consumer Key/Token and Oauth Key/Token generated by twitter
	define("CONSUMER_KEY", "kEpqpQFs1kKMMFo1AyAg");
	define("CONSUMER_SECRET", "PoiJLJvFRlbCQxhZsLGJq9OtbLISsJv5QQdsDYaB2g");
	define("OAUTH_TOKEN", "991634996-lojnw72IrtOo4xclml9wlZCtByOJTySUdndAuf7J");
	define("OAUTH_SECRET", "GmsCkeSyQHvii0e4fByQanXPSspUTsonElNRtMwJ954");
	
	// Connect to twitter
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, OAUTH_TOKEN, OAUTH_SECRET);
	$content = $connection->get('account/verify_credentials');

	// Status update
	$connection->post('statuses/update', array($twits));
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
        <a><img src="images/logo.png" height="110px" width="187px" alt="logo" href = "index.php" /></a>
      </div>
      <div id="menuBar">
        <ul id="menu">
          <li id="menu-1"><a href="index.php" title="Home" class="current"><span>Home</span></a></li>
          <li id="menu-2"><a href="location.php" title="My Profile"><span>My Profile</span></a></li>
          <li id="menu-3"><a href="lost.php" title="Subjects"><span>Subjects</span></a></li>
          <li id="menu-4"><a href="found.php" title="Tutors"><span>Tutors</span></a></li>
          <li id="menu-5"><a><span>Empty</span></a></li>
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
			<input name="logout" class = "submitButton" id = "out" type="submit" value="Logout" />
		  </form>

	    <?php } else { ?>
		  <?php if (isset($err)) echo "<p>$err</p>" ?>
		  <form method="post" action="index.php">
		    <span><label >Username </label><input type="text" name="username" /> </span>
			<span><label >Password </label><input type="password" name="pass" /> </span>
		    <span><input name="login" class = "submitButton" type="submit" value="Login" /></span>
		  </form>
		<!--
		  <h1>Sign Up</h1>
		  <?php if (isset($msg)) echo "<p>$msg</p>" ?>
		  <form method="post" action="index.php">
		    <label>Username: </label><input type="text" name="username" /><br/>
			<label>*Email: </label><input type="text" name="email" /><br/>
			<label>Password: </label><input type="password" name="pass" /><br/>
			<label>Confirm Password: </label><input type="password" name="passconfirm" /><br/>
			<p>* must be an RPI email. </p>
			<p>
			If you are a guest at RPI or do not have an RPI email, contact
				the office of public safety at (518)-276-6600
			</p>
			<p>All fields are required</p>
			<input type="submit" class = "submitButton" name="register" value="Register" />
		  </form>
		-->
		<?php } ?>       
	  </div>
      <br/>
	  

		
		
		<!-- Login Area -->

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
		
	    <div id="threeDiv">
          <div class="typeButton">
			<!-- 
				Toggle tabs for each section 
			-->
            <a id="alltab" href="#" class="now" onclick="toggle_all()">All</a>
            <a id="losttab" href="#" class="" onclick="toggle_lost()">Lost</a>
            <a id="foundtab" href="#" class="" onclick="toggle_found()">Found</a>
          </div>
          <div class="clear"></div>
          <div id="all" class="visible">
		    <?php  
		    $sql = 'SELECT * FROM lost_found ORDER BY time DESC LIMIT 10';
			$stmt = $dbconn->prepare($sql);
			$stmt->execute(array());
		    foreach ($stmt as $row): 
			  $username = $row['username'];
			  $sql = 'SELECT * FROM profile WHERE username=:username';
			  $stmt = $dbconn->prepare($sql);
			  $stmt->execute(array(':username' => $username));
			  $result = $stmt->fetch(PDO::FETCH_ASSOC);?>
			  
			  <!--
				Print out info from table 
			  -->
			  <div class="name">
                <?php echo $row['username'] ?> : <br/>
                <span class = "data" ><?php echo $result['firstname'];?></span>
			    <span class = "data"><?php echo $result['lastname'];?></span><br/>
			    <span class = "data"><?php echo $result['email'];?></span>
			    <span class = "data"><?php echo $result['phone'];?></span>
              </div>
              <div class="message">
                <div><?php echo $row['message']; ?></div>
                <div class="clear"></div>
                <div class="time" style="float:left;font-size:14px;"><?php echo $row['time']." : ".$row['location']; ?></div>
			    <?php 
			    if (isset($_SESSION['username'])){
				  $thisname = $_SESSION['username'];
				  if ($username == $thisname && $del==false) { ?>
				    <form method="post" action="index.php">
					<input  type="hidden" name="pid" value="<?php echo htmlentities($row['pid']) ?>"> 
					<input class = "deleteButton" type="submit" name="delete" value="Delete" />
				  <?php }?>
				<?php } ?>
				<!--
					Display the info whether it's lost/found entitiy 
				-->
                <div class="lostorfound">
                  <?php
                  if ($row['found']== 'Found')
                    echo "Found";
                  else
                    echo "Lost";
                  ?>
                </div>
					
			    <?php 
				// Let the user delete their entries (or admin delete any)
				if (isset($_SESSION['username'])) {
				  if ($del == true){ ?>
				    <form method="post" action="index.php">
					  <input  type="hidden" name="pid" value="<?php echo htmlentities($row['pid']) ?>"> 
					  <input class = "deleteButton" type="submit" name="delete" value="Delete" />
				    </form>
				  <?php  } ?>
				  <?php 
				  // If Twitter privelages enabled (admin only)
				  if ($twit == true){ ?>
				    <form method="post" action="index.php">
					  <input  type="hidden" name="pid" value="<?php echo htmlentities($row['pid']) ?>"> 
					  <input class = "deleteButton" type="submit" name="tweet" value="Tweet" />
				    </form>
				  <?php } ?>
				<?php  } ?>
                <div class="clear"></div>
			    <?php echo "<hr />"; ?>
              </div>
              <div class="clear"></div>
            <?php endforeach; ?>
          </div>
			<!--
				Follows same logic as All
			-->
          <div id="lost" class="invisible">
            <?php  
		    $sql = 'SELECT * FROM lost_found WHERE found="Lost" ORDER BY time DESC LIMIT 10';
			$stmt = $dbconn->prepare($sql);
			$stmt->execute(array()); 
			foreach ($stmt as $row):  
			  $username = $row['username'];
			  $sql = 'SELECT * FROM profile WHERE username=:username';
			  $stmt = $dbconn->prepare($sql);
			  $stmt->execute(array(':username' => $username));
			  $result = $stmt->fetch(PDO::FETCH_ASSOC);?>
              <div class="name">
                <?php echo $row['username'] ?> : <br/>
                <span class = "data"><?php echo $result['firstname'];?></span>
				<span class = "data"><?php echo $result['lastname'];?></span><br/>
			    <span class = "data"><?php echo $result['email'];?></span>
			    <span class = "data"><?php echo $result['phone'];?></span>  
              </div>
              <div class="message">
                <div><?php echo $row['message']; ?></div>
                <div class="clear"></div>
                <div class="time" style="float:left;font-size:14px;"><?php echo $row['time']." : ".$row['location']; ?></div>
			    <?php 
				if (isset($_SESSION['username'])){
			      $thisname = $_SESSION['username'];
			      if ($username == $thisname && $del==false) { ?>
					<form method="post" action="index.php">
					  <input  type="hidden" name="pid" value="<?php echo htmlentities($row['pid']) ?>"> 
					  <input class = "deleteButton" type="submit" name="delete" value="Delete" />
				    </form>
				  <?php } ?>
				<?php } ?>
                <div class="lostorfound">
                  <?php
                  if ($row['found'] == 'Found')
                    echo "Found";
                  else
                    echo "Lost";
                  ?>
                </div>
				<?php if (isset($_SESSION['username'])) {
			      if ($del == true){ ?>
				    <form method="post" action="index.php">
					  <input  type="hidden" name="pid" value="<?php echo htmlentities($row['pid']) ?>"> 
					  <input class = "deleteButton" type="submit" name="delete" value="Delete" />
					</form>
				  <?php } ?>
				  <?php 
				  // If Twitter privelages enabled (admin only)
				  if ($twit == true){ ?>
				    <form method="post" action="index.php">
					  <input  type="hidden" name="pid" value="<?php echo htmlentities($row['pid']) ?>"> 
					  <input class = "deleteButton" type="submit" name="tweet" value="Tweet" />
				    </form>
				  <?php } ?>
				<?php } ?>
                <div class="clear"></div>
		      <?php echo "<hr />"; ?>
              </div>
              <div class="clear"></div>
            <?php endforeach; ?>
          </div>
			
			<!--
				Follows same logic as lost and all
			-->
          <div id="found" class="invisible">
            <?php  
	        $sql = 'SELECT * FROM lost_found WHERE found="Found" ORDER BY time DESC LIMIT 10';
			$stmt = $dbconn->prepare($sql);
			$stmt->execute(array());
			foreach ($stmt as $row):  
			  $username = $row['username'];
			  $sql = 'SELECT * FROM profile WHERE username=:username';
			  $stmt = $dbconn->prepare($sql);
			  $stmt->execute(array(':username' => $username));
			  $result = $stmt->fetch(PDO::FETCH_ASSOC);?>
              <div class="name">
                <?php echo $row['username'] ?> : <br/>
                <span class = "data"><?php echo $result['firstname'];?></span>
		        <span class = "data"><?php echo $result['lastname'];?></span><br/>
		        <span class = "data"><?php echo $result['email'];?></span>
				<span class = "data"><?php echo $result['phone'];?></span>
              </div>
              <div class="message">
                <div><?php echo $row['message']; ?></div>
                <div class="clear"></div>
                <div class="time" style="float:left;font-size:14px;"><?php echo $row['time']." : ".$row['location']; ?></div>
			    <?php 
			    if (isset($_SESSION['username'])){
			      $thisname = $_SESSION['username'];
				  if ($username == $thisname && $del==false) { ?>
				    <form method="post" action="index.php">
					  <input  type="hidden" name="pid" value="<?php echo htmlentities($row['pid']) ?>"> 
				      <input class = "deleteButton" type="submit" name="delete" value="Delete" />
					</form>
				  <?php } ?>
				<?php } ?>
                <div class="lostorfound">
                  <?php
                  if ($row['found'] == 'Found')
                    echo "Found";
                  else
                    echo "Lost";
                  ?>
                </div>
				<?php if (isset($_SESSION['username'])) {
			      if ($del == true){ ?>
				    <form method="post" action="index.php">
					  <input  type="hidden" name="pid" value="<?php echo htmlentities($row['pid']) ?>"> 
					  <input class = "deleteButton" type="submit" name="delete" value="Delete" />
				    </form>
				  <?php } ?>
				  <?php 
				  // If Twitter privelages enabled (admin only)
				  if ($twit == true){ ?>
				    <form method="post" action="index.php">
					  <input  type="hidden" name="pid" value="<?php echo htmlentities($row['pid']) ?>"> 
					  <input class = "deleteButton" type="submit" name="tweet" value="Tweet" />
				    </form>
				  <?php } ?>				  
				<?php } ?>
                <div class="clear"></div>
				<?php echo "<hr />"; ?>
              </div>
			  
              <div class="clear"></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
		<!--
			Footer: Nothing Special
		-->
    <div id="footer">
	  RPI Lost &amp; Found Directory
	</div>
  </body>
</html>
