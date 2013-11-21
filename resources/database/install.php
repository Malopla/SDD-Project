<?php 

//The default password for all accounts created here is "admin"
  try {	
  	include 'db.php';
		
	$users_tbl = "CREATE TABLE users
				 (
				  	username VARCHAR(255) NOT NULL, 
				 	password VARCHAR(255) NOT NULL, 
				 	salt VARCHAR(255) NOT NULL,
				 	PRIMARY KEY(username) ) 
				 	ENGINE=INNODB";
			 	
	$stmt = $dbconn->prepare($users_tbl);
	$stmt->execute();
	  
	$profile_tbl = "CREATE TABLE profile
				   (
				      prid int AUTO_INCREMENT PRIMARY KEY, 
				      username VARCHAR(255) NOT NULL, 
				      firstname VARCHAR(255) NOT NULL,
				      lastname VARCHAR(255) NOT NULL, 
				      email VARCHAR(255) NOT NULL, 
				      phone VARCHAR(255) NOT NULL, 
				      FOREIGN KEY (username) REFERENCES users(username) 
				    )
				    	ENGINE=INNODB";
				    	
	$stmt = $dbconn->prepare($profile_tbl);
	$stmt->execute();	  
	
	$lost_found_tbl = "CREATE TABLE lost_found
				 (
				 	pid INT AUTO_INCREMENT PRIMARY KEY, 
				 	username VARCHAR(255) NOT NULL, 
				 	found VARCHAR(255),
				 	location VARCHAR(255),
				 	message VARCHAR(255),
				 	time timestamp NOT NULL DEFAULT NOW(), 
				 	FOREIGN KEY(username) REFERENCES users(username)
				 )
				 	ENGINE=INNODB";
				 	
	$stmt = $dbconn->prepare($lost_found_tbl);
	$stmt->execute();
		
	$locations_tbl = "CREATE TABLE locations
					(
						name VARCHAR(255) PRIMARY KEY
					)
						ENGINE=INNODB";	
	$stmt = $dbconn->prepare($locations_tbl);
	$stmt->execute();	
	
	$permissions_tbl = "CREATE TABLE permissions
					(
						username VARCHAR(255) NOT NULL PRIMARY KEY,
						del enum('true', 'false') NOT NULL,
						twitter enum('true', 'false') NOT NULL,
						FOREIGN KEY(username) REFERENCES users(username)
					)
						ENGINE=INNODB";	
	$stmt = $dbconn->prepare($permissions_tbl);
	$stmt->execute();	
		
		
	$users_val = 'INSERT INTO users 
					  	VALUES   
					("Billy", "46fb40f176cc5d5595dffdc7a877c114c30ad8b5", "rZ1par%J)z@e#6L^$I!@DA4@rsnXN_5t"), 
				 	("Joe", "46fb40f176cc5d5595dffdc7a877c114c30ad8b5", "rZ1par%J)z@e#6L^$I!@DA4@rsnXN_5t"), 
				 	("Bob", "46fb40f176cc5d5595dffdc7a877c114c30ad8b5", "rZ1par%J)z@e#6L^$I!@DA4@rsnXN_5t"),
					("admin", "46fb40f176cc5d5595dffdc7a877c114c30ad8b5", "rZ1par%J)z@e#6L^$I!@DA4@rsnXN_5t")';
	$stmt = $dbconn->prepare($users_val);
	$stmt->execute();
	  
	$profile_val = 'INSERT INTO profile 
					(
						username, firstname, lastname, email, phone) 
							VALUES 
						("Billy", "John", "Root", "rootj2@rpi.edu", "518-482-1946"), 
						("Joe", "Wang", "Beer", "beerwang@rpi.edu", "518-557-3728"), 
						("Bob", "Bang", "Shang", "shangb@rpi.edu", "518-579-3829"),
						("admin", "Stephen", "Tobolowsky", "tobos@rpi.edu", "518-555-2135")';
	$stmt = $dbconn->prepare($profile_val);
	$stmt->execute();
	
	$lost_found_val = 'INSERT INTO lost_found 
					  	(username, found, location, message) 
					  		VALUES 
					  	("Billy", "Found", "Academy Hall", "green scarf of doom"), 
					  	("Joe", "Found", "Mueller Center", "orange peel of banana"), 
					  	("Joe", "Lost", "Nason Hall", "cell phone from 19th century"), 
					  	("Bob", "Found", "Hall Hall", "awesome stuff bro YOLOSWAG"),
						("admin", "Found", "Barton Hall", "A carton of milk")';
	$stmt = $dbconn->prepare($lost_found_val);
	$stmt->execute();
		
	$locations_val = 'INSERT INTO locations
							VALUES
						("87 Gymnasium"), ("Academy Hall"), ("Alumni House"), ("Amos Eaton Hall"), ("Barton Hall"),
						("Biotechnology and Interdisciplinary Studies Building"), ("Bray Hall"), ("Burdett Residence Hall"),
						("Carnegie Building"), ("Cary Hall"), ("Cogswell Laboratory"), ("Commons Dining Hall"), 
						("Crockett Hall"), ("Darrin Communications Center"), ("Davison Hall"), ("E Complex"), 
						("Experimental Media and Performing Arts Center"), ("Folsom Library"), ("Greene Building"), 
						("Hall Hall"), ("Java plus plus"), ("Jonsson Engineering Center"), 
						("Jonsson-rowland Science Center"), ("Lally Hall"), ("Low Center for Industrial Innovation"),
						("Materials Research Center"), ("Mueller Center"), ("Nason Hall"), ("North Hall"), ("Nugent Hall"),
						("Pittsburgh Building"), ("Playhouse"), ("Public Safety and Parking Access Offices"), 
						("Quadrangle Complex"), ("Rensselaer Union"), ("Rice Building"), ("Ricketts Building"), ("Robinson Pool"),
						("Russell Sage Dining Hall"), ("Russell Sage Lab"), ("Sharp Hall"), ("Stacwyck"), ("Troy Building"), 
						("VCC"), ("Walker Laboratory"), ("Warren Hall"), ("West Hall")';
	$stmt = $dbconn->prepare($locations_val);
	$stmt->execute();	
	
	$permissions_val = "INSERT INTO permissions 
						VALUES 
					('Billy', 'false', 'false'),
					('Joe', 'false', 'false'),
					('Bob', 'false', 'false'),
					('admin', 'true', 'true')";
	$stmt = $dbconn->prepare($permissions_val);
	$stmt->execute();
	
		
	echo "Everything should be dandy.";
  }
	
  catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }	
  
?>
