<?php
class Database{
	private $_db;
	private $dbname = "test";
	private $db_user = "root";
	private $db_pass = "root1234";

	public function __construct() {
		$this->_db = new PDO('mysql:host=localhost;dbname='.$this->dbname, $this->db_user, $this->db_pass);
	}

	public function query($sqlQuery, $args) {
		$stmt = $this->_db->prepare($sqlQuery);
		$stmt->execute($args);
		return $stmt->fetch();
	}
	public function queryNoFetch($sqlQuery, $args) {
		$stmt = $this->_db->prepare($sqlQuery);
		$stmt->execute($args);
		return $stmt;
	}
}
?>
