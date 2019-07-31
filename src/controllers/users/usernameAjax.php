<?php

global $db;

if (isset($_POST['userID'])) {

$userID = $_POST['userID'];

	$sql = $db->prepare("SELECT `Forename`, `Surname` FROM `users` WHERE `UserID` = ? AND `AccessLevel` = 'Parent';");
	$sql->execute([$userID]);
	if ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
		echo htmlspecialchars($row['Forename'] . " " . $row['Surname']);
	} else {
		?>Not Found<?php
	}

}

if (isset($_POST['userSur'])) {

	$sur = "%" . $_POST['userSur'] . "%";

	$sql = $db->prepare("SELECT `UserID`, `Forename`, `Surname` FROM `users` WHERE `Surname` LIKE ? AND `AccessLevel` = 'Parent' ORDER BY `Forename` ASC, `Surname` ASC;");
	$sql->execute([
		$sur
	]);
	if ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
		?>
		<option>
			SELECT FROM LIST
		</option>
		<?php
		do { ?>
			<option value="<?=$row['UserID']?>">
				<?=htmlspecialchars($row['Forename'] . " " . $row['Surname'])?>
			</option> <?php
		} while ($row = $sql->fetch(PDO::FETCH_ASSOC));
	} else {
		?>
		<option>
			NO RESULTS FOUND
		</option>
		<?php
	}
}
