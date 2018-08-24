<?

if ($_POST['agree'] == 1) {
	$done_by_renewal = false;
	if ($renewal == 0) {
		$sql = "SELECT `MemberID` FROM  `members` WHERE UserID = ?";
		try {
			$query = $db->prepare($sql);
			$query->execute([$_SESSION['UserID']]);
		} catch (PDOException $e) {
			halt(500);
		}
		$swimmers = sizeof($query->fetchAll(PDO::FETCH_ASSOC));
		$sql = "SELECT `MemberID` FROM `members` WHERE UserID = ? AND RR = ? ORDER
		BY `MemberID` ASC";
		try {
			$query = $db->prepare($sql);
			$query->execute([$_SESSION['UserID'], 1]);
		} catch (PDOException $e) {
			halt(500);
		}
		$new_sw = $query->fetchAll(PDO::FETCH_ASSOC);
		$new_swimmers = sizeof($new_sw);
		if ($swimmers != $new_swimmers) {
			$sql = "SELECT `MemberID` FROM `members` WHERE MemberID > ? AND UserID = ?
			AND RR = ? ORDER BY `MemberID` ASC";
			try {
				$query = $db->prepare($sql);
				$query->execute([$id, $_SESSION['UserID'], 1]);
			} catch (PDOException $e) {
				halt(500);
			}
			$new_sw = $query->fetchAll(PDO::FETCH_ASSOC);
			$part = $new_sw[0]['ID'];
			$sql = "UPDATE `renewalProgress` SET `Substage` = '1', `Part` = ? WHERE
			`RenewalID` = ? AND `UserID` = ?";
			try {
				$db->prepare($sql)->execute([$part, $renewal, $_SESSION['UserID']]);
			} catch (PDOException $e) {
				halt(500);
			}
			$done_by_renewal = true;
		}
	}

	if (!$done_by_renewal) {
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

header("Location: " . app('request')->curl);
