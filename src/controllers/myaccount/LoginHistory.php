<?

$use_white_background = true;

global $db;

$null = $page;

$start = 0;

if ($page != null) {
  $start = ($page-1)*10;
} else {
  $page = 1;
}

if ($page == 1 && $null != null) {
  header("Location: " . autoUrl("myaccount/loginhistory"));
  die();
}

$sql = "SELECT `ID` FROM `userLogins` WHERE `UserID` = ?";
try {
	$query = $db->prepare($sql);
	$query->execute([$_SESSION['UserID']]);
} catch (PDOException $e) {
	halt(500);
}
$numLogins = sizeof($query->fetchAll(PDO::FETCH_ASSOC));
$numPages = ((int)($numLogins/10)) + 1;

if ($start > $numLogins) {
  //halt(404);
}

$sql = "SELECT `Time`, `IPAddress`, `GeoLocation`, `Browser`, `Platform`, `Mobile` FROM `userLogins` WHERE `UserID` = :user ORDER BY `Time` DESC LIMIT :start, 10";
try {
	$query = $db->prepare($sql);
  $query->bindParam('user', $_SESSION['UserID'], PDO::PARAM_INT);
  $query->bindParam('start', $start, PDO::PARAM_INT);
	$query->execute();
} catch (PDOException $e) {
	 halt(500);
}

$row = $query->fetchAll(PDO::FETCH_ASSOC);

$pagetitle = "Login History";

include BASE_PATH . "views/header.php";
//include BASE_PATH . "views/notifyMenu.php";?>

<div class="container">
  <div class="">
    <h1>Your Login History</h1>
		<div class="alert alert-danger">
			<p class="mb-0"><strong>Spotted anything suspicious?</strong></p>
			<p class="mb-0"><a href="<?=autoUrl("myaccount/password")?>"
			class="alert-link">Change your password</a> straight away.</p>
		</div>
    <? if ($numLogins == 0) { ?>
      <p class="lead pb-3 mb-0 border-bottom border-gray">
        You have never logged in.
      </p>
    <? } else { ?>
    <p class="lead pb-3 mb-0 border-bottom border-gray">
      Page <? echo $page; ?> of <? echo $numPages; ?>
    </p>
    <? for ($i = 0; $i < sizeof($row); $i++) {
    $date = new DateTime($row[$i]['Time'], new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone('Europe/London')); ?>
    <div class="media py-3 my-0 border-bottom border-gray">
      <div class="media-body my-0">
        <div class="d-block text-gray-dark">
          <p class="mb-0">
            <strong>
              Login at <?= $date->format('H:i \o\\n l j F Y') ?>
              using <?= htmlentities($row[$i]['Browser']) ?>
            </strong>
          </p>
          <p class="mb-0">
            <? if ($row[$i]['Mobile']) { ?>
            Login from a mobile device running <?= htmlentities($row[$i]['Platform']) ?>
            <? } else { ?>
            Login from a desktop computer running <?= htmlentities($row[$i]['Platform']) ?><? } ?><? if ($row[$i]['GeoLocation']) { ?> located in <?= htmlentities($row[$i]['GeoLocation']) ?><? } ?>.
            IP Address: <?= htmlentities($row[$i]['IPAddress']) ?>
          </p>
        </div>
      </div>
    </div>
    <? } ?>

    <nav aria-label="Page navigation">
      <ul class="pagination mt-3 mb-0">
        <? if ($numLogins <= 10) { ?>
        <li class="page-item active"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
        <? } else if ($numLogins <= 20) { ?>
          <? if ($page == 1) { ?>
          <li class="page-item active"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } else { ?>
          <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page-1 ?>">Previous</a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page-1 ?>"><? echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
          <? } ?>
        <? } else { ?>
    			<? if ($page == 1) { ?>
    			<li class="page-item active"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page+2 ?>"><? echo $page+2 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } else { ?>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page-1 ?>">Previous</a></li>
          <? if ($page > 2) { ?>
          <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page-2 ?>"><? echo $page-2 ?></a></li>
          <? } ?>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page-1 ?>"><? echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    			<? if ($numLogins > $page*10) { ?>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
          <? if ($numLogins > $page*10+10) { ?>
          <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page+2 ?>"><? echo $page+2 ?></a></li>
          <? } ?>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/loginhistory/page/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } ?>
        <? } ?>
      <? } ?>
      </ul>
    </nav>
  <? } ?>
  </div>
</div>

<?
include BASE_PATH . "views/footer.php";
