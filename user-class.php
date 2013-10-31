<?php

interface User {

	public function addSubject($subject);
	public function removeSubject($subject);
	public function getSubjects();
	public function subjectCount();
}

class Student implements User {
	private $username;
	private $_db;
	
	public function __construct($db, $username) {
		$this->_db=$db;
		$this->username=$username;
	}

	public function subjectCount() {
		$count=$this->_db->query('SELECT subjectCount FROM students WHERE username=:username', array(':username' => $this->username));
		return $count;
	}
	// Add a specified student subject
	public function addSubject($subject) {
	  $this->_db->query('INSERT INTO subjects (username, role, subject) VALUES (:username, :role, :subject)', array(':username' => $this->username,':role' => 'student', ':subject' => $subject));
	}
	// Remove a specified student subject
	public function removeSubject($subject) {
		$this->_db->query('DELETE FROM subjects WHERE username=:username AND role=:role AND subject=:subject', array(':username' => $this->username, ':role' => 'student', ':subject' => $subject)); 
	}
	// Return an array of student subjects
	public function getSubjects() {
		$stmt=$this->_db->queryNoFetch('SELECT * FROM subjects WHERE username=:username AND role=:role',array(':username' => $this->username, ':role' => 'student'));
		$subjects=array();
		while($row=$stmt->fetch())	{
			array_push($subjects, $row['subject']);
		}
		return $subjects;
	}
}

class Tutor implements User {
	private $username;
	private $_db;
	
	public function __construct($db, $username) {
		$this->_db=$db;
		$this->username=$username;
	}
	
	public function subjectCount() {
		$count=$this->_db->query('SELECT subjectCount FROM students WHERE username=:username', array(':username' => $this->username));
		return $count;
	}

	public function addSubject($subject) {
	  $this->_db->query('INSERT INTO subjects (username, role, subject) VALUES (:username, :role, :subject)', array(':username' => $this->username,':role' => 'tutor', ':subject' => $subject));
	}
	public function removeSubject($subject) {
		$this->_db->query('DELETE FROM subjects WHERE username=:username AND role=:role AND subject=:subject', array(':username' => $this->username, ':role' => 'tutor', ':subject' => $subject)); 
	}

	public function getSubjects() {

		$stmt=$this->_db->queryNoFetch('SELECT * FROM subjects WHERE username=:username AND role=:role',array(':username' => $this->username, ':role' => 'tutor'));
		$subjects=array();
		while($row=$stmt->fetch())	{
			array_push($subjects, $row['subject']);
		}
		return $subjects;
	}
}

?>
