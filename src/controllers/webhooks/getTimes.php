<?

ignore_user_abort(true);
set_time_limit(0);

$sql = "SELECT DISTINCT `ASANumber`, `members`.`MemberID` FROM `members` LEFT JOIN `times` ON `members`.`MemberID` = `times`.`MemberID` WHERE `LastUpdate` IS NULL OR `LastUpdate` < CURDATE() LIMIT 4;";
$result = mysqli_query($link, $sql);

$count = mysqli_num_rows($result);

$date = date("Y-m-d");
$type = null;

for ($i = 0; $i < $count; $i++) {
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$user = $row['MemberID'];

	if (!strpos($row['ASANumber'], 'CLSX') && $row['ASANumber'] != "") {
		$array = getTimes($row['ASANumber']);
		for ($y = 0; $y < 4; $y++) {
			if ($y == 0) {
				$type = "CY_SC";
			} else if ($y == 1) {
				$type = "SCPB";
			} else if ($y == 2) {
			 	$type = "CY_LC";
		 	} else if ($y == 3) {
				$type = "LCPB";
			}

			$num = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `times` WHERE `MemberID` = '$user' AND `Type` = '$type';"));

			$t1 = $array[$type][1];
			$t2 = $array[$type][2];
			$t3 = $array[$type][3];
			$t4 = $array[$type][4];
			$t5 = $array[$type][5];
			$t6 = $array[$type][6];
			$t7 = $array[$type][7];
			$t8 = $array[$type][8];
			$t9 = $array[$type][9];
			$t10 = $array[$type][10];
			$t11 = $array[$type][11];
			$t12 = $array[$type][12];
			$t13 = $array[$type][13];
			$t14 = $array[$type][14];
			$t15 = $array[$type][15];
			$t16 = $array[$type][16];
			$t17 = $array[$type][17];
			$t18 = $array[$type][18];

			if ($num == 0) {
				$sql = "INSERT INTO `times` (`MemberID`, `LastUpdate`,  `Type`,
				`50Free`, `100Free`, `200Free`, `400Free`, `800Free`, `1500Free`,
				`50Breast`, `100Breast`, `200Breast`, `50Fly`, `100Fly`, `200Fly`,
				`50Back`, `100Back`, `200Back`, `100IM`, `200IM`, `400IM`) VALUES
				('$user', '$date', '$type', '$t1', '$t2', '$t3', '$t4', '$t5', '$t6',
				'$t7', '$t8', '$t9', '$t10', '$t11', '$t12', '$t13', '$t14', '$t15',
				'$t18', '$t16', '$t17');";
				mysqli_query($link, $sql);
			} else {
				$sql = "UPDATE `times` SET `LastUpdate` = '$date', `50Free` = '$t1',
				`100Free` = '$t2', `200Free` = '$t3', `400Free` = '$t4', `800Free` =
				'$t5', `1500Free` = '$t6', `50Breast` = '$t7', `100Breast` = '$t8',
				`200Breast` = '$t9', `50Fly` = '$t10', `100Fly` = '$t11', `200Fly` =
				'$t12', `50Back` = '$t13', `100Back` = '$t14', `200Back` = '$t15',
				`200IM` = '$t16', `400IM` = '$t17', `100IM` = '$t18' WHERE `MemberID` =
				'$user' AND `Type` = '$type';";
				mysqli_query($link, $sql);
			}
		}
	}
}

?>
