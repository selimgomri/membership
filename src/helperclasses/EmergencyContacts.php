<?

class EmergencyContacts {
	private $contacts;
	private $dbconn;

	public function __construct($dbconn) {
		$this->$contacts = [];
		$this->dbconn = $dbconn;
	}

	public function byParent($id) {
		$id = mysqli_real_escape_string($this->dbconn, $id);
		$sql = "SELECT * FROM `emergencyContacts` WHERE `UserID` = '$id';";
		$result = mysqli_query($this->dbconn, $sql);
		for ($i = 0; $i < mysqli_num_rows($result); $i++) {
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$new = new EmergencyContact();
			$new->existing(
				$row['ID'],
				$row['UserID'],
				$row['Name'],
				$row['ContactNumber']
			);
			$this->contacts[] = $new;
		}
	}

	public function bySwimmer($id) {
		$sql = "SELECT * FROM `members` LEFT JOIN `emergencyContacts` ON
		members.UserID = emergencyContacts.UserID WHERE `MemberID` = '$id';";
		$result = mysqli_query($this->dbconn, $sql);
		for ($i = 0; $i < mysqli_num_rows($result); $i++) {
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$new = new EmergencyContact();
			$new->existing(
				$row['ID'],
				$row['UserID'],
				$row['Name'],
				$row['ContactNumber']
			);
			$this->contacts[] = $new;
		}
	}

	public function getContacts() {
		return $this->contacts;
	}

	public function getContact($i) {
		return $this->contacts[$i];
	}

}
