<?php

$pagetitle = "Notify";
include BASE_PATH . "views/header.php";

if ($_POST['List-Unsubscribe'] == "One-Click") {
	$email = mysqli_real_escape_string($link, $email);
	$sql = "UPDATE `users` SET `EmailComms` = '0' WHERE `EmailAddress` = '$email';";
	if (mysqli_query($link, $sql)) { ?>
	<div class="container">
		<h1>Successfully Unsubscribed</h1>
		<p>Notify by Chester-le-Street ASC</p>
	</div>
	<?
	} else { ?>
	<div class="container">
		<h1>Unable to Unsubscribe -Couldn't find email</h1>
		<p>Notify by Chester-le-Street ASC</p>
	</div>
	<?
	}
} else { ?>
<div class="container">
	<h1>Unable to Unsubscribe - Must send a POST Request</h1>
	<p>Notify by Chester-le-Street ASC</p>
</div>
<?
}

include BASE_PATH . "views/footer.php";
