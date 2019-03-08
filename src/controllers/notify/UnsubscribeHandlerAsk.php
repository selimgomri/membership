<?php

global $db;

$user_id = hexdec($userid);
$email = str_replace(' ', '+', urldecode($email));

if ($list != "Notify" && $list != "Security" && $list != "Payments") {
	halt(404);
}

try {
	$query = $db->prepare("SELECT COUNT(*) FROM `users` WHERE `UserID` = ? AND `EmailAddress` = ?");
	$query->execute([$user_id, $email]);
} catch (Exception $e) {
	halt(500);
}

if ($query->fetchColumn() != 1) {
	//Do something
	halt(404);
}

$pagetitle = "Notify Unsubscribe";
include BASE_PATH . "views/header.php";?>

<div class="container">
	<h1>Confirm Unsubscribe</h1>
	<p>
		We just need you to press the button below to confirm your unsubscription.
		We do this to prevent accidental unsubscription because some email providers
		"sniff" links sent to users.
	</p>
	<p><a href="<?=app('request')->curl . "do"?>" class="btn
	btn-primary">Unsubscribe from <span
	class="mono"><?=htmlspecialchars($list)?></span></a></p>
	<p>
		For further help and support with emails from <?=CLUB_NAME?>, visit
		our <a href="<?=autoUrl("notify")?>">Notify Help Centre</a>.
	</p>
	<p>
		Notify by <?=CLUB_NAME?>
	</p>
</div>

<? include BASE_PATH . "views/footer.php";
