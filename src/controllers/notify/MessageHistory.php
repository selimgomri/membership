<?

$null = $page;

$use_white_background = true;

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

$sql = "SELECT `Subject`, `Message` FROM `notifyHistory` ;";
$numMails  = mysqli_num_rows(mysqli_query($link, $sql));
$numPages = ((int)($numMails/10)) + 1;

if ($start > $numMails) {
  halt(404);
}

$sql = "SELECT `notifyHistory`.`Subject`, `notifyHistory`.`Message`,
`notifyHistory`.`ForceSend`, `Forename`, `Surname`, `JSONData`, `Date` FROM
(`notifyHistory` LEFT JOIN `users` ON notifyHistory.Sender = users.UserID) ORDER
BY `Date` DESC LIMIT $start, 10;";
$result = mysqli_query($link, $sql);

$pagetitle = "Message History - Notify";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/notifyMenu.php";?>

<style>
.force-wrap {

  /* These are technically the same, but use both */
  overflow-wrap: break-word;
  word-wrap: break-word;

  -ms-word-break: break-all;
  /* This is the dangerous one in WebKit, as it breaks things wherever */
  word-break: break-all;
  /* Instead use this non-standard one: */
  word-break: break-word;

  /* Adds a hyphen where the word breaks, if supported (No Blink) */
  -ms-hyphens: auto;
  -moz-hyphens: auto;
  -webkit-hyphens: auto;
  hyphens: auto;

}

.force-wrap:last-child, .force-wrap p:last-child {
  margin-bottom: 0px;
}
</style>

<div class="container">
  <div class="">
    <h1>Notify Message History</h1>
    <p class="lead">
      Page <? echo $page; ?> of <? echo $numPages; ?>
    </p>
    <? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $info = json_decode($row['JSONData']);
      $sender = null;
      if ($row['Forename'] != "" && $row['Surname'] != "") {
        $sender = "<p class=\"mb-0\">Sent by: " . $row['Forename'] . " " .
        $row['Surname'] . "</p>";
        if ($row['ForceSend']) {
          $sender .= "<p class=\"mb-0\"><em>Sending was forced</em></p>";
        }
      } else {
        $sender = "<p class=\"mb-0\">Sent by: " . $info->Sender->Name . "</p>";
        if ($row['ForceSend']) {
          $sender .= "<p class=\"mb-0\"><em>Sending was forced</em></p>";
        }
      }
      ?>
      <div class="cell p-0">
        <div class=" p-3">
          <p class="mb-0">
            <strong>
              <? echo $row['Subject']; ?>
            </strong>
          </p>
          <? echo $sender; ?>
          <? if ($row['JSONData'] != "") { ?>
          <p class="mb-0">
            Sent to:
            <?
            $squads = (array) $info->To->Squads;
            $lists = (array) $info->To->Targeted_Lists;
            foreach ($squads as $s) { ?>
              <span class="badge badge-pill rounded badge-dark">
                <? echo $s; ?>
              </span><?
            }
            foreach ($lists as $s) { ?>
              <span class="badge badge-pill rounded badge-dark">
                <? echo $s; ?>
              </span><?
            } ?>
          </p>
          <p class="mb-0">
            Date: <? echo date("d F Y", strtotime($row['Date'])); ?>
          </p>
        <? } ?>
        </div>
        <div class="p-3 pt-0 bg-light force-wrap">
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
    			<? if ($page == 1) { ?>
    			<li class="page-item active"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+2 ?>"><? echo $page+2 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } else { ?>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page-1 ?>">Previous</a></li>
          <? if ($page > 2) { ?>
          <li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page-2 ?>"><? echo $page-2 ?></a></li>
          <? } ?>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page-1 ?>"><? echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    			<? if ($numMails > $page*10) { ?>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
          <? if ($numMails > $page*10+10) { ?>
          <li class="page-item"><a class="page-link" href="<? echo autoUrl("notify/history/page/"); ?><? echo $page+2 ?>"><? echo $page+2 ?></a></li>
          <? } ?>
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
