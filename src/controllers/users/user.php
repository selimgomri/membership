<?php

$use_white_background = true;

global $db;
$sql = $db->prepare("SELECT COUNT(*) FROM users WHERE UserID = ?");
$sql->execute([$id]);

if ($sql->fetchColumn() == 0) {
  halt(404);
}

$pagetitle = getUserNameByID($link, $id) . " - User Information";
$title = null;
$content = getUserInfoByID($link, $id);
include BASE_PATH . "views/header.php";
?>
<div class="container">
	<?php echo $content ?>
	<div class="">
		<h2>Simulate this user</h2>
		<p class="mb-0"><a href="<?=autoUrl("users/simulate/" . $id)?>">Simulate this user for help and support</a></p>
	</div>
</div>
<?php include BASE_PATH . "views/footer.php";
