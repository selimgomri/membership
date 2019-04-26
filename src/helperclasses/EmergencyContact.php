<?php

/* The EmergencyContact Class
 * An instance of this class should be globally accessible
 */

use Respect\Validation\Validator as v;

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
		$this->name = ucwords($name);
		if (!v::phone()->validate($contactNumber)) {
			return false;
		}
		$this->contactNumber = mysqli_real_escape_string($this->dbconn,
		"+44" . ltrim(preg_replace('/\D/', '', str_replace("+44", "", $contactNumber)), '0'));
		$this->user = $user;
  }

	public function existing($contactId, $user, $name, $contactNumber) {
		$this->contactId = $contactId;
		$this->user = $user;
		$this->name = $name;
		$this->contactNumber = $contactNumber;
  }

	public function getByContactID($contactId) {
		$this->contactId = mysqli_real_escape_string($this->dbconn, $contactId);
		if ($this->dbconn == null) {
			return false;
		}
		$sql = "SELECT * FROM `emergencyContacts` WHERE `ID` = '$this->contactId';";
		$result = mysqli_query($this->dbconn, $sql);
		if (mysqli_num_rows($result) == 0) {
			return false;
		}
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$this->user = $row['UserID'];
		$this->name = $row['Name'];
		$this->contactNumber = $row['ContactNumber'];
  }

	public function getName() {
		return $this->name;
	}

	public function getContactNumber() {
		return $this->contactNumber;
	}

	public function getID() {
		return $this->contactId;
	}

	public function getUserID() {
		return $this->user;
	}

	public function setName($name) {
		if ($this->dbconn == null) {
			return false;
		}
		$this->name = mysqli_real_escape_string($this->dbconn, ucwords($name));
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
		if (!v::phone()->validate($contactNumber)) {
			return false;
		}
		$this->contactNumber = mysqli_real_escape_string($this->dbconn,
		"+44" . ltrim(preg_replace('/\D/', '', str_replace("+44", "", $contactNumber)), '0'));
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
