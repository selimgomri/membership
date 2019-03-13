<?

$use_white_background = true;

$null = $page;

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);

$start = 0;

if ($page != null) {
  $start = ($page-1)*10;
} else {
  $page = 1;
}

/*if ($page == 1 && $null != null) {
  header("Location: " . autoUrl("myaccount/notifyhistory"));
  die();
}*/

$sql = "SELECT `notifyHistory`.`Subject` FROM ((`notifyHistory`
LEFT JOIN `users` ON notifyHistory.Sender = users.UserID) INNER JOIN `notify` ON
notify.MessageID = notifyHistory.ID) WHERE notify.UserID = '$user';";
$numMails  = mysqli_num_rows(mysqli_query($link, $sql));
$numPages = ((int)($numMails/10)) + 1;

if ($start > $numMails) {
  halt(404);
}

$sql = "SELECT `notifyHistory`.`Subject`, `notifyHistory`.`Message`,
`notify`.`ForceSend`, `Forename`, `Surname`, `JSONData`, `Date` FROM ((`notifyHistory`
LEFT JOIN `users` ON notifyHistory.Sender = users.UserID) INNER JOIN `notify` ON
notify.MessageID = notifyHistory.ID) WHERE notify.UserID = '$user' ORDER BY `EmailID` DESC LIMIT $start, 10;";
$result = mysqli_query($link, $sql);

$pagetitle = "Message History";

include BASE_PATH . "views/header.php";
//include BASE_PATH . "views/notifyMenu.php";?>

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
    <h1>My Message History</h1>
    <? if ($numMails == 0) {
      ?>
      <p class="mb-0">There are no messages to view right now.</p>
      <?
    } else { ?>
    <p class="lead">
      Page <? echo $page; ?> of <? echo $numPages; ?>
    </p>
    <? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $info = json_decode($row['JSONData']);
      $sender = null;
        if ($row['ForceSend']) {
          $sender .= "<dt class=\"col-sm-3\">Force Send</dt>
          <dd class=\"col-sm-9\">This message was sent to all users regardless of whether or not they had opted in or out of emails.</dd>";
        }
      ?>
      <div class="cell p-0">
        <div class="p-3">
          <p>
            <strong>
              <? echo $row['Subject']; ?>
            </strong>
          </p>
          <dl class="row mb-0 small">
          <? echo $sender; ?>
          <? if ($row['JSONData'] != "") { ?>
          <dt class="col-sm-3">Sent To</dt>
          <dd class="col-sm-9">
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
          </dd>
          <dt class="col-sm-3 mb-0">Date</dt>
          <dd class="col-sm-9 mb-0"><? echo date("d F Y", strtotime($row['Date'])); ?></dd>
          </dl>
      <? } ?>
      </div>
      <div class="bg-light p-3 pt-0 force-wrap">
        <? echo $row['Message']; ?>
      </div>
  </div>
    <? } ?>

    <nav aria-label="Page navigation">
      <ul class="pagination mb-0">
        <? if ($numMails <= 10) { ?>
        <li class="page-item active"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
        <? } else if ($numMails <= 20) { ?>
          <? if ($page == 1) { ?>
          <li class="page-item active"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } else { ?>
          <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page-1 ?>">Previous</a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page-1 ?>"><? echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
          <? } ?>
        <? } else { ?>
    			<? if ($page == 1) { ?>
    			<li class="page-item active"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page+2 ?>"><? echo $page+2 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } else { ?>
    			<li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page-1 ?>">Previous</a></li>
          <? if ($page > 2) { ?>
          <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page-2 ?>"><? echo $page-2 ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page-1 ?>"><? echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    			<? if ($numMails > $page*10) { ?>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
          <? if ($numMails > $page*10+10) { ?>
          <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page+2 ?>"><? echo $page+2 ?></a></li>
          <? } ?>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page+1 ?>">Next</a></li>
          <? } ?>
        <? } ?>
      <? }
      } ?>
      </ul>
    </nav>
  <? } ?>
  </div>
</div>

<?
include BASE_PATH . "views/footer.php";
