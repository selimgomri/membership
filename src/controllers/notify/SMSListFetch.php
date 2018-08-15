<?

$sql = null;

if ($_POST['squadID'] == 'allSquads') {
	$sql = "SELECT DISTINCT `Mobile` FROM `users` INNER JOIN `members` ON `members`.`UserID` = `users`.`UserID` WHERE `MobileComms` = 1";
} else {
	$sql = "SELECT DISTINCT `Mobile` FROM `users` INNER JOIN `members` ON `members`.`UserID` = `users`.`UserID` WHERE `SquadID` = ? AND `MobileComms` = 1";
}

try {
	$query = $db->prepare($sql);
	if ($_POST['squadID'] == 'allSquads') {
		$query->execute();
	} else {
		$query->execute([$_POST['squadID']]);
	}
} catch (PDOException $e) {
	halt(500);
}

$row = $query->fetchAll(PDO::FETCH_ASSOC);

if (!$row) {
	echo "There are no phone numbers available for this squad.";
} else {
	for ($i = 0; $i < sizeof($row); $i++) {
		if ($i < sizeof($row)-1) {
			echo $row[$i]['Mobile'] . ", ";
		} else {
			echo $row[$i]['Mobile'];
		}
	}
}
