<?

global $db;
$query = null;

if ($int) {
	$sql = "SELECT * FROM `posts` WHERE `ID` = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([$id]);
	} catch (PDOException $e) {
		halt(500);
	}
} else {
	$sql = "SELECT * FROM `posts` WHERE `Path` = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([$id]);
	} catch (PDOException $e) {
		halt(500);
	}
}
$row = $query->fetch(PDO::FETCH_ASSOC);

if (!$row) {
  halt(404);
}

if ($row['MIME'] != "text/html") {
	header('Content-Type: ' . $row['MIME']);
	echo $row['Content'];
	exit();
} else {

$use_white_background = true;
$pagetitle = htmlentities($row['Title']);

$allow_edit = true;

$container_classes = "";
if ($row['Type'] == "corporate_documentation") {
	$container_classes .= "serif ";
}

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/postsMenu.php";?>

<!--<style>
#post-content p:last-child {
  margin-bottom: 0px;
}
</style>-->

<div class="container <?= $container_classes ?>">
	<div class="row d-none d-print-inline-block">
    <div class="col-6">
      <img class="img-fluid logo" src="<?=autoUrl("/img/chesterLogo.svg")?>"  alt="Chester-le-Street ASC Logo">
    </div>
  </div>
	<a class="logo" alt="Chester-le-Street ASC" href="https://www.chesterlestreetasc.co.uk/"></a>
	<hr>

	<h1><?= htmlentities($row['Title']) ?></h1>

	<div class="row">
		<div class="col-md-8">
			<div id="post-content">
				<?= $row['Content'] ?>
			</div>
		</div>
	</div>
</div>

<?
}
include BASE_PATH . "views/footer.php";
