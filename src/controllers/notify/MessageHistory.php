<?

$null = $page;

$start = 0;

if ($page != null) {
  $start = ($page-1)*10;
} else {
  $page = 1;
}

if ($page == 1 && $null != null) {
  header("Location: " . autoUrl("notify/history"));
  die();
}

$sql = "SELECT DISTINCT `Subject` , `Message` FROM `notify`;";
$numMails  = mysqli_num_rows(mysqli_query($link, $sql));
$numPages = ((int)($numMails/10)) + 1;

if ($start > $numMails) {
  halt(404);
}

$sql = "SELECT DISTINCT `Subject` , `Message`, `ForceSend`, `Forename`, `Surname` FROM `notify` LEFT JOIN `users` ON notify.Sender = users.UserID ORDER BY `EmailID` DESC LIMIT $start, 10;";
$result = mysqli_query($link, $sql);

$pagetitle = "Message History - Notify";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";?>

<div class="container">
  <div class="my-3 p-3 bg-white rounded box-shadow">
    <h1>Notify Message History</h1>
    <p class="lead pb-3 mb-0 border-bottom border-gray">
      Page <? echo $page; ?> of <? echo $numPages; ?>
    </p>
    <? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $sender = null;
      if ($row['Forename'] != "") {
        $sender = "Sent by " . $row['Forename'] . " " . $row['Surname'];
        if ($row['ForceSend']) {
          $sender .= " - Sending was forced";
        }
      }
      ?>
      <div class="media pt-3">
        <div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
          <div class="d-block text-gray-dark">
            <strong>
              <? echo $row['Subject']; ?><em>
            </strong>
            <? echo $sender; ?></em>
          </div>
          <? echo $row['Message']; ?>
        </div>
      </div>
    <? } ?>

    <nav aria-label="Page navigation">
      <ul class="pagination mb-0">
        <? if ($numMails <= 10) { ?>
        <li class="page-item active"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
        <? } else if ($numMails <= 20) { ?>
          <? if ($page == 1) { ?>
          <li class="page-item active"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } else { ?>
          <li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page-1 ?>">Previous</a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page-1 ?>"><? echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
          <? } ?>
        <? } else { ?>
    			<? if ($numMails == 1) { ?>
    			<li class="page-item active"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+2 ?>"><? echo $page+2 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } else { ?>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page-1 ?>">Previous</a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page-1 ?>"><? echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    			<? if ($numMails > $page*10) { ?>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } ?>
        <? } ?>
      <? } ?>
      </ul>
    </nav>
  </div>
</div>

<?
include BASE_PATH . "views/footer.php";
