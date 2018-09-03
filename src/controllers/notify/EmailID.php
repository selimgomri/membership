<?php

$id = mysqli_real_escape_string($link, $id);

$user = $_SESSION['UserID'];

$sql = "SELECT * FROM `notify` INNER JOIN `users` ON notify.UserID = users.UserID WHERE `EmailID` = '$id';";
$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	halt(404);
}

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$pagetitle = $row['Subject'] . " - " . $row['Forename'] . " " . $row['Surname'];

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";

 ?>

<div class="container">
	<div class="p-3 my-3 text-white bg-primary rounded shadow">
		<h1 class="h6 mb-0"><strong><? echo $row['Subject']; ?></strong></h1>
		<p class="mb-0">To: <? echo $row['Forename'] . " " . $row['Surname']; ?></p>
	</div>

	<div class="my-3 p-3 bg-white rounded shadow">
		<h2 class="border-bottom border-gray pb-2">Message</h2>
		<? echo $row['Message']; ?>
	</div>
</div>

<?php include BASE_PATH . "views/footer.php";
