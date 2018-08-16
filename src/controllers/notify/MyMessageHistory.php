<?

$null = $page;

$user = mysqli_real_escape_string($link, $_SESSION['UserID']);

$start = 0;

if ($page != null) {
  $start = ($page-1)*10;
} else {
  $page = 1;
}

if ($page == 1 && $null != null) {
  header("Location: " . autoUrl("myaccount/notifyhistory"));
  die();
}

$sql = "SELECT DISTINCT `Subject` , `Message` FROM `notify` WHERE `UserID` =
'$user' AND (`EmailType` <> 'Security' OR `EmailType` IS NULL);";
$numMails  = mysqli_num_rows(mysqli_query($link, $sql));
$numPages = ((int)($numMails/10)) + 1;

if ($start > $numMails) {
  halt(404);
}

$sql = "SELECT DISTINCT `notify`.`Subject`, `notify`.`Message`,
`notify`.`ForceSend`, `Forename`, `Surname`, `JSONData`, `Date` FROM ((`notify`
LEFT JOIN `users` ON notify.Sender = users.UserID) LEFT JOIN `notifyHistory` ON
`MessageID` = notifyHistory.ID) WHERE `notify`.`UserID` = '$user' AND
(`EmailType` <> 'Security' OR `EmailType` IS NULL) ORDER BY `EmailID` DESC LIMIT
$start, 10;";
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
  <div class="mb-3 p-3 bg-white rounded box-shadow">
    <h1>My Message History</h1>
    <? if ($numMails == 0) {
      ?>
      <p class="mb-0">There are no messages to view right now.</p>
      <?
    } else { ?>
    <p class="lead pb-3 mb-0 border-bottom border-gray">
      Page <? echo $page; ?> of <? echo $numPages; ?>
    </p>
    <? for ($i = 0; $i < mysqli_num_rows($result); $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $info = json_decode($row['JSONData']);
      $sender = null;
        if ($row['ForceSend']) {
          $sender .= "<p class=\"mb-0\">This message was sent to all users regardless of whether or not they had opted in or out of emails.</p>";
        }
      ?>
      <div class="media pt-3">
        <div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray force-wrap">
          <div class="d-block text-gray-dark mb-3">
            <p class="mb-0">
              <strong>
                <? echo $row['Subject']; ?>
              </strong>
            </p>
            <? echo $sender; ?>
            <? if ($row['JSONData'] != "") { ?>
            <p class="mb-0">
              We sent this email to:
              <?
              $squads = (array) $info->To->Squads;
              $lists = (array) $info->To->Targeted_Lists;
              foreach ($squads as $s) { ?>
                <span class="badge badge-pill rounded badge-dark">
                  <? echo $s; ?>
                </span><?
              }
              foreach ($lists as $s) { ?>
                <span class="badge badge-pill badge-dark">
                  <? echo $s; ?>
                </span><?
              } ?>
            </p>
            <p class="mb-0">
              Date: <? echo date("d F Y", strtotime($row['Date'])); ?>
            </p>
          <? } ?>
          </div>
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
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page-1 ?>"><? echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page ?>"><? echo $page ?></a></li>
    			<? if ($numMails > $page*10) { ?>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page+1 ?>"><? echo $page+1 ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<? echo autoUrl("myaccount/notifyhistory/page/"); ?><? echo $page+1 ?>">Next</a></li>
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
