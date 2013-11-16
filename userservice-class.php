<?php

class UserService {
	private $_db;
	private $username;

	public function __construct($db, $username) {
		$this->_db=$db;
		$this->username=$username;
	}

	public function updateProfile($fname, $lname, $email, $phone, $bio, $sprice, $tprice) {
		$this->_db->query('UPDATE profile SET firstname = :firstname, lastname = :lastname, email = :email, phone = :phone, bio = :bio, studentprice = :sprice, tutorprice = :tprice WHERE username = :username', array(':firstname' => $fname, ':lastname' => $lname, ':email' => $email, ':phone' => $phone, ':bio' => $bio, ':sprice' => $sprice, ':tprice' => $tprice , ':username' => $this->username));
	}

	public function getProfile() {
		$stmt = $this->_db->query('SELECT * FROM profile WHERE username=:username', array(':username' => $this->username));
		$profile=['firstname' => $stmt['firstname'], 'lastname' => $stmt['lastname'], 'email' => $stmt['email'], 'phone' => $stmt['phone'], 'bio' => $stmt['bio'], 'studentprice' => $stmt['studentprice'], 'tutorprice' => $stmt['tutorprice']];
		return $profile;
	}

	public function addAvailability($day) {
			  $this->_db->query('INSERT INTO availability (username, day) VALUES (:username, :day)', array(':username' => $this->username,':day' => $day));
	}
	
	public function getAvailability() {
		$stmt=$this->_db->queryNoFetch('SELECT * FROM availability WHERE username=:username', array(':username' => $this->username));
		$availability=array();
		while($row=$stmt->fetch())	{
			array_push($availability, $row['day']);
		}
		return $availability;
	}

	public function removeAvailability($day) {
		$this->_db->query('DELETE FROM availability WHERE username=:username AND day=:day', array(':username' => $this->username, ':day' => $day));
	} 

	
	
}

?>
