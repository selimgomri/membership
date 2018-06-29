<?php

/* The EmergencyContact Class
 * An instance of this class should be globally accessible
 */

class EmergencyContact {
	private $name;
	private $contactNumber;
	private $dbconn;
	private $contactId;
	private $user;

	public function __construct() {
		// DO NOTHING
  }

	public function new($name, $contactNumber, $user) {
		$this->name = $name;
		$this->contactNumber = $contactNumber;
		$this->user = $contactNumber;
  }

	public function existing($contactId, $user, $name, $contactNumber) {
		$this->contactId = $contactId;
		$this->user = $user;
		$this->name = $name;
		$this->contactNumber = $contactNumber;
  }

	public function getName() {
		return $this->name;
	}

	public function getContactNumber() {
		return $this->ContactNumber;
	}

	public function setName($name) {
		if ($this->dbconn == null) {
			return false;
		}
		$this->name = mysqli_real_escape_string($this->dbconn, $name);
		$sql = "UPDATE `emergencyContacts` SET `Name` =
		'$this->name' WHERE `ID` = '$this->contactId';";
		if (mysqli_query($this->dbconn, $sql)) {
			return true;
		}
		return false;
	}

	public function setContactNumber($contactNumber) {
		if ($this->dbconn == null) {
			return false;
		}
		$this->contactNumber = mysqli_real_escape_string($this->dbconn,
		$contactNumber);
		$sql = "UPDATE `emergencyContacts` SET `ContactNumber` =
		'$this->contactNumber' WHERE `ID` = '$this->contactId';";
		if (mysqli_query($this->dbconn, $sql)) {
			return true;
		}
		return false;
	}

	public function delete() {
		if ($this->dbconn == null) {
			return false;
		}
		$sql = "DELETE FROM `emergencyContacts` WHERE `ID` = '$this->contactId';";
		if (mysqli_query($this->dbconn, $sql)) {
			return true;
		}
		return false;
	}

	public function add() {
		if ($this->dbconn == null) {
			return false;
		}
		$sql = "INSERT INTO `emergencyContacts` (`UserID`, `Name`, `ContactNumber`)
		VALUES ('$this->user', '$this->name', '$this->contactNumber');";
		if (mysqli_query($this->dbconn, $sql)) {
			return true;
		}
		return false;
	}

	public function connect($dbconn) {
		$this->dbconn = $dbconn;
		$this->contactId = mysqli_real_escape_string($this->dbconn,
		$this->contactId);
	}
}
