<?php 

class Auth{
	private $_db;
	public function __construct(Database $db) {
		$this->_db = $db;
	}

	public function createUser() {

	}
	public function login($username, $pass) {
		$salt_res=$this->_db->query('SELECT salt FROM users WHERE username=:username', array(':username' => $username));
		$salt=($salt_res) ? $salt_res['salt'] : '';
		$salted = sha1($salt . $pass);
		$login_res = $this->_db->query('SELECT username FROM users WHERE username=:username AND password=:pass', 
			array(':username' => $username, ':pass' => $salted));
		if ($user = $login_res) {
			$_SESSION['username'] = $user['username'];
		   header('Location:profile.php');
		}
	}

	public function isOwner() {
		return (!isset($_GET['user']) && isset($_SESSION['username'])) || (isset($_GET['user']) && ($_SESSION['username'] == $_GET['user']));
	}	

	public function logout() {
		unset($_SESSION['username']);
		setcookie(session_name(), '', time()-48000);
		session_destroy();
	}

	public function register($username, $password, $email) {	
		$salt = sha1(uniqid(mt_rand(), true));      
		$salted = sha1($salt . $_POST['pass']);      
		$this->_db->query('INSERT INTO users (username, password, salt) VALUES (:username, :pass, :salt)', array(':username' => $username, ':pass' => $salted, ':salt' => $salt));
		$this->_db->query('INSERT INTO profile (username, email) VALUES (:username, :email)', array(':username' => $username, ':email' => $email));
		$this->_db->query('INSERT INTO students (username, subjectCount) VALUES (:username, :subjectCount)', array(':username' => $username, ':subjectCount' => 0));

	}

	public function validateRegistration() {
		if (!isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['pass']) || !isset($_POST['passconfirm']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['pass'])|| empty($_POST['passconfirm'])) {
			$msg = "Please fill in all of the form fields.";
		}

		// Registration checking for matched passwords
		else if ($_POST['pass'] !== $_POST['passconfirm']) {
			$msg = "Passwords must match.";
		}
		
		else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$row=$this->_db->query('SELECT COUNT(*) AS count FROM users WHERE username = :username', array(':username' => $_POST['username']));
	
			// Account name already exists
			if (intval($row['count']) > 0) {
				$msg = "Account name already exists. Sorry.";
			}

			// Everything has validated -> Create the user account
			else $msg="Success, you can now log in.";
		}
		// Make sure that it's an email address being added
		else {
			$msg = 'Not an email address. Try again.';
		}
		
		return $msg;
	}
}
?>

