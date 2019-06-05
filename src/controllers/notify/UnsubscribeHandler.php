<?php

global $db;

$user_id = hexdec($userid);
$email = str_replace(' ', '+', urldecode($email));

$list_lc - mb_strtolower($list);

if ($list_lc != "notify" && $list_lc != "security" && $list_lc != "payments") {
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

updateSubscription(false, $list, $user_id);

$pagetitle = "Notify Unsubscribe";
include BASE_PATH . "views/header.php";?>

<div class="container">
	<h1>Successfully Unsubscribed</h1>
	<p>You will no longer receive emails from the <span class="mono"><?=htmlspecialchars($list)?></span> list.</p>
	<p>
		For further help and support with emails from <?=CLUB_NAME?>, visit
		our <a href="<?=autoUrl("notify")?>">Notify Help Centre</a>.
	</p>
	<p>
		Notify by <?=CLUB_NAME?>
	</p>
</div>

<?php include BASE_PATH . "views/footer.php";
