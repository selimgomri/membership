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
		$this->contactNumber = "+44" . ltrim(preg_replace('/\D/', '', str_replace("+44", "", $contactNumber)), '0');
		$this->user = $user;
  }

	public function existing($contactId, $user, $name, $contactNumber) {
		$this->contactId = $contactId;
		$this->user = $user;
		$this->name = $name;
		$this->contactNumber = $contactNumber;
  }

	public function getByContactID($contactId) {
		$this->contactId = $contactId;
		if ($this->dbconn == null) {
			return false;
		}
		$sql = $this->dbconn->prepare("SELECT * FROM `emergencyContacts` WHERE `ID` = ?");
    $sql->execute([$this->contactId]);

    $row = $sql->fetch(PDO::FETCH_ASSOC);
		if ($row == null) {
			return false;
		}
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
		$this->name = ucwords($name);
    try {
  		$sql = $this->dbconn->prepare("UPDATE `emergencyContacts` SET `Name` = ? WHERE `ID` = ?");
      $sql->execute([$this->name, $this->contactId]);
    } catch (Exception $e) {
      return false;
    }
    return true;
	}

	public function setContactNumber($contactNumber) {
		if ($this->dbconn == null) {
			return false;
		}
		if (!v::phone()->validate($contactNumber)) {
			return false;
		}
		$this->contactNumber = "+44" . ltrim(preg_replace('/\D/', '', str_replace("+44", "", $contactNumber)), '0');
    try {
  		$sql = $this->dbconn->prepare("UPDATE `emergencyContacts` SET `ContactNumber` = ? WHERE `ID` = ?");
      $sql->execute([$this->contactNumber, $this->contactId]);
    } catch (Exception $e) {
      return false;
    }
		return true;
	}

	public function delete() {
		if ($this->dbconn == null) {
			return false;
		}
    try {
  		$sql = $this->dbconn->prepare("DELETE FROM `emergencyContacts` WHERE `ID` = ?");
      $sql->execute([$this->contactId]);
    } catch (Exception $e) {
      return false;
    }
    return true;
  }

	public function add() {
		if ($this->dbconn == null) {
			return false;
		}
    try {
  		$sql = $this->dbconn->prepare("INSERT INTO `emergencyContacts` (`UserID`, `Name`, `ContactNumber`) VALUES (?, ?, ?)");
      $sql->execute([
        $this->user,
        $this->name,
        $this->contactNumber
      ]);
    } catch (Exception $e) {
      return false;
    }
    return true;
	}

	public function connect($dbconn) {
		$this->dbconn = $dbconn;
	}
}
