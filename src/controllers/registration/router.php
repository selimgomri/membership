<?

if ($_SESSION['AccessLevel'] == "Parent") {
	$this->get('/', function() {
		global $link, $db;
		$sql = "SELECT `MemberID` FROM `members` WHERE UserID = ?";
		try {
			$query = $db->prepare($sql);
			$query->execute([$_SESSION['UserID']]);
		} catch (PDOException $e) {
			halt(500);
		}
		$swimmers = sizeof($query->fetchAll(PDO::FETCH_ASSOC));

		$sql = "SELECT `MemberID` FROM `members` WHERE UserID = ? AND RR = ?";
		try {
			$query = $db->prepare($sql);
				$query->execute([$_SESSION['UserID'], 1]);
		} catch (PDOException $e) {
			halt(500);
		}
		$new_swimmers = sizeof($query->fetchAll(PDO::FETCH_ASSOC));

		if ($swimmers != $new_swimmers) {
			include 'newmember/Welcome.php';
		} else {
			include 'newfamily/Welcome.php';
		}
	});
} else {
	$this->get('/', function() {
		global $link;

		$id = 0;
		include BASE_PATH . 'controllers/renewal/admin/list.php';
	});
}

$this->group('/', function() {
  include 'join-from-trial/router.php';
});
