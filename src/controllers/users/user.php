<?php

$use_white_background = true;

global $db;
$sql = $db->prepare("SELECT COUNT(*) FROM users WHERE UserID = ?");
$sql->execute([$id]);

if ($sql->fetchColumn() == 0) {
  halt(404);
}

$name = htmlspecialchars(getUserNameByID($link, $id));

$pagetitle = $name . " - User Information";
$title = null;
$content = getUserInfoByID($link, $id);
include BASE_PATH . "views/header.php";
?>
<div class="container">
  <div class="mb-3">
	   <?=$content?>
   </div>
  <h2>
    <?=$name?>'s Qualifications
  </h2>
  <p>
    <a href="<?=app('request')->curl?>qualifications" class="btn btn-success">
      View Qualifications <span class="fa fa-chevron-right"></span>
    </a>
  </p>

	<div class="">
		<h2>Simulate this user</h2>
		<p class="mb-0"><a href="<?=autoUrl("users/simulate/" . $id)?>" class="btn btn-success">Simulate this user <span class="fa fa-chevron-right"></span> </a></p>
	</div>
</div>
<?php include BASE_PATH . "views/footer.php";
