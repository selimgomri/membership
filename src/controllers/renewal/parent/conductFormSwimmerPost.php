<?

global $db;

if ($_POST['agree'] == 1) {
	$done_by_renewal = false;
if (false/*isPartialRegistration() && !getNextSwimmer($_SESSION['UserID'], $id, true)*/) {
		$full_renewal = false;
		$substage = 1;
		$member = getNextSwimmer($_SESSION['UserID'], 0, true);
		$sql = "UPDATE `renewalProgress` SET `Stage` = `Stage` + 1, `Substage` = 0, `Part` = 0 WHERE `RenewalID` = 0 AND `UserID` = ?";
		try {
			$db->prepare($sql)->execute([$_SESSION['UserID']]);
		} catch (PDOException $e) {
			halt(500);
		}
	} else if (false/*isPartialRegistration()*/) {
		$member = getNextSwimmer($_SESSION['UserID'], $id, true);
		$sql = "UPDATE `renewalProgress` SET `Part` = ? WHERE `RenewalID` = 0 AND
		`UserID` = ?";
		try {
			$db->prepare($sql)->execute([$id, $_SESSION['UserID']]);
		} catch (PDOException $e) {
			halt(500);
		}
	} else {
		$id = mysqli_real_escape_string($link, $id);
		$user = mysqli_real_escape_string($link, $_SESSION['UserID']);
		$sql = "SELECT * FROM `members` WHERE `UserID` = '$user' AND `MemberID` > '$id' ORDER BY `MemberID` ASC
		LIMIT 1;";
		$result = mysqli_query($link, $sql);

		$renewal = mysqli_real_escape_string($link, $renewal);
		if (mysqli_num_rows($result) == 0) {
			$sql = "UPDATE `renewalProgress` SET `Stage` = `Stage` + 1, `Substage` = '0',
			`Part` = '0' WHERE `RenewalID` = '$renewal' AND `UserID` = '$user';";
			mysqli_query($link, $sql);
		} else {
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$member = mysqli_real_escape_string($link, $row['MemberID']);
			$sql = "UPDATE `renewalProgress` SET `Part` = '$member' WHERE `RenewalID` =
			'$renewal' AND `UserID` = '$user';";
			mysqli_query($link, $sql);
		}
	}
} else {
	$_SESSION['RenewalErrorInfo'] = '
	<div class="alert alert-danger">
		<p class="mb-0">
			<strong>
				You must tick to confirm you agree to this code of conduct
			</strong>
		</p>
	</div>';
}

header("Location: " . currentUrl());
