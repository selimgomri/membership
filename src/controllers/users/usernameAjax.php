<?php

if (isset($_POST['userID'])) {

$userID = mysqli_real_escape_string($link, $_POST['userID']);

	$sql = "SELECT `Forename`, `Surname` FROM `users` WHERE `UserID` = '$userID'
	AND `AccessLevel` = 'Parent';";
	$result = mysqli_query($link, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo $row['Forename'] . " " . $row['Surname'];
	} else {
		?>Not Found<?php
	}

}

if (isset($_POST['userSur'])) {

	$sur = "%" . mysqli_real_escape_string($link, $_POST['userSur']) . "%";

	$sql = "SELECT `UserID`, `Forename`, `Surname` FROM `users` WHERE `Surname`
	LIKE '$sur' AND `AccessLevel` = 'Parent' ORDER BY `Forename` ASC, `Surname`
	ASC;";
	$result = mysqli_query($link, $sql);
	$count = mysqli_num_rows($result);
	if ($count == 0) {
		?>
		<option>
			NO RESULTS FOUND
		</option>
		<?php
	} else {
		?>
		<option>
			SELECT FROM LIST
		</option>
		<?php
		for ($i = 0; $i < $count; $i++) {
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);?>
			<option value="<?php echo $row['UserID'];?>">
				<?php echo htmlspecialchars($row['Forename'] . " " . $row['Surname']); ?>
			</option> <?php
		}
	}
}
