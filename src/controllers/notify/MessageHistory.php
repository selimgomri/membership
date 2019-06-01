<?php

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
      Page <?php echo $page; ?> of <?php echo $numPages; ?>
    </p>
    <?php for ($i = 0; $i < mysqli_num_rows($result); $i++) {
      $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
      $info = json_decode($row['JSONData']);
      $sender = null;
      if ($row['Forename'] != "" && $row['Surname'] != "") {
        $sender = "<dt class=\"col-sm-3\">Sent by</dt><dd class=\"col-sm-9\">" . $row['Forename'] . " " .
        $row['Surname'] . "</dd>";
        if ($row['ForceSend']) {
          $sender .= "<dt class=\"col-sm-3\">Force Sent</dt><dd class=\"col-sm-9\">True</dd>";
        }
      } else {
        $sender = "<dt class=\"col-sm-3\">Sent by</dt><dd class=\"col-sm-9\">" . $info->Sender->Name . "</dd>";
        if ($row['ForceSend']) {
          $sender .= "<dt class=\"col-sm-3\">Force Sent</dt><dd class=\"col-sm-9\">True</dd>";
        }
      }
      ?>
      <div class="cell p-0">
        <div class="p-3">
          <p>
            <strong>
              <?php echo $row['Subject']; ?>
            </strong>
          </p>
          <dl class="row mb-0 small">
          <?php echo $sender; ?>
          <?php if ($row['JSONData'] != "") { ?>
          <dt class="col-sm-3">Sent To</dt>
          <dd class="col-sm-9">
            <?php
            $squads = (array) $info->To->Squads;
            $lists = (array) $info->To->Targeted_Lists;
            foreach ($squads as $s) { ?>
              <span class="badge badge-pill rounded badge-dark">
                <?php echo $s; ?>
              </span><?php
            }
            foreach ($lists as $s) { ?>
              <span class="badge badge-pill rounded badge-dark">
                <?php echo $s; ?>
              </span><?php
            } ?>
          </dd>
          <dt class="col-sm-3 mb-0">Date</dt>
          <dd class="col-sm-9 mb-0">
            <?php echo date("d F Y", strtotime($row['Date'])); ?>
          </dd>
        </dl>
        <?php } ?>
        </div>
        <div class="p-3 pt-0 bg-light force-wrap">
          <?php echo $row['Message']; ?>
        </div>
      </div>
    <?php } ?>

    <nav aria-label="Page navigation">
      <ul class="pagination mb-0">
        <?php if ($numMails <= 10) { ?>
        <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page ?>"><?php echo $page ?></a></li>
        <?php } else if ($numMails <= 20) { ?>
          <?php if ($page == 1) { ?>
          <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page ?>"><?php echo $page ?></a></li>
    			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page+1 ?>">Next</a></li>
          <?php } else { ?>
          <li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page-1 ?>">Previous</a></li>
    	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page-1 ?>"><?php echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page ?>"><?php echo $page ?></a></li>
          <?php } ?>
        <?php } else { ?>
    			<?php if ($page == 1) { ?>
    			<li class="page-item active"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page ?>"><?php echo $page ?></a></li>
    	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page+2 ?>"><?php echo $page+2 ?></a></li>
    			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page+1 ?>">Next</a></li>
          <?php } else { ?>
    			<li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page-1 ?>">Previous</a></li>
          <?php if ($page > 2) { ?>
          <li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page-2 ?>"><?php echo $page-2 ?></a></li>
          <?php } ?>
    	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page-1 ?>"><?php echo $page-1 ?></a></li>
    	    <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page ?>"><?php echo $page ?></a></li>
    			<?php if ($numMails > $page*10) { ?>
    	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
          <?php if ($numMails > $page*10+10) { ?>
          <li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page+2 ?>"><?php echo $page+2 ?></a></li>
          <?php } ?>
    	    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("notify/history/page/"); ?><?php echo $page+1 ?>">Next</a></li>
          <?php } ?>
        <?php } ?>
      <?php } ?>
      </ul>
    </nav>
  </div>
</div>

<?php
include BASE_PATH . "views/footer.php";
