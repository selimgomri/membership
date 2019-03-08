<?

global $db;

$use_white_background = true;

$null = $page;

$start = 0;

if ($page != null) {
  $start = ($page-1)*10;
} else {
  $page = 1;
}

if ($page == 1 && $null != null) {
  header("Location: " . autoUrl("people"));
  die();
}

$sql = "SELECT `ID` FROM `posts` WHERE `Type` = 'people_pages'";
try {
	$query = $db->prepare($sql);
	$query->execute();
} catch (PDOException $e) {
	halt(500);
}
$numPosts = sizeof($query->fetchAll(PDO::FETCH_ASSOC));
$numPages = ((int)($numPosts/10)) + 1;

if ($start > $numPosts) {
  halt(404);
}

$sql = "SELECT * FROM `posts` WHERE `Type` = 'people_pages' ORDER BY `Title` ASC, `Date` DESC LIMIT :start, 10;";
try {
	$query = $db->prepare($sql);
  $query->bindParam('start', $start, PDO::PARAM_INT);
	$query->execute();
} catch (PDOException $e) {
	 halt(500);
}
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

$pagetitle = "Club People";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/postsMenu.php";?>

<div class="container">
  <main class="">
    <h1>All People</h1>
    <p class="lead pb-3 mb-0 border-bottom border-gray">
      Page <? echo $page; ?> of <? echo $numPages; ?>
    </p>
    <? for ($i = 0; $i < sizeof($rows); $i++) {
      $row = $rows[$i]; ?>
      <div class="media pt-3">
        <div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray force-wrap">
          <div class="d-block text-gray-dark mb-0">
            <p class="mb-0">
							<a href="<?= autoUrl("people/" . $row['Path']) ?>">
	              <strong>
	                <?= $row['Title']; ?>
	              </strong>
							</a>
            </p>
						<? if ($row['Excerpt'] != "") { ?>
						<p class="mb-0">
							<?= htmlentities($row['Excerpt']) ?>
						</p>
						<? } ?>
          </div>
        </div>
      </div>
    <? } ?>

    <nav aria-label="Page navigation">
      <ul class="pagination">
        <? if ($numPosts <= 10) { ?>
        <li class="page-item active"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page ?>"><? echo $page ?></a></li>
        <? } else if ($numPosts <= 20) { ?>
          <? if ($page == 1) { ?>
          <li class="page-item active"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } else { ?>
          <li class="page-item"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page-1 ?>">Previous</a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page-1 ?>"><? echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page ?>"><? echo $page ?></a></li>
          <? } ?>
        <? } else { ?>
    			<? if ($page == 1) { ?>
    			<li class="page-item active"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page+2 ?>"><? echo $page+2 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } else { ?>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page-1 ?>">Previous</a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page-1 ?>"><? echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    			<? if ($numPosts > $page*10) { ?>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("people/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } ?>
        <? } ?>
      <? } ?>
      </ul>
    </nav>
  </main>
</div>

<?
include BASE_PATH . "views/footer.php";
