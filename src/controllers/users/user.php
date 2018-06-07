<?php
$pagetitle = getUserNameByID($link, $id) . " - User Information";
$title = null;
$content = getUserInfoByID($link, $id);
include BASE_PATH . "views/header.php";
?>
<div class="container">
	<?php echo $content ?>
</div>
<?php include BASE_PATH . "views/footer.php";
