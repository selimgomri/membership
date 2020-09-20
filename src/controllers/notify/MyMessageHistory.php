<?php

$db = app()->db;

$fluidContainer = true;

$page = null;
if (isset($_GET['page'])) {
  $page = $_GET['page'];
}

$null = $page;

$start = 0;

if ($page != null) {
  $start = ((int) $page - 1) * 10;
} else {
  $page = 1;
}

/*if ($page == 1 && $null != null) {
  header("Location: " . autoUrl("my-account/notifyhistory"));
  die();
}*/

$sql = $db->prepare("SELECT COUNT(*) FROM ((`notifyHistory`
LEFT JOIN `users` ON notifyHistory.Sender = users.UserID) INNER JOIN `notify` ON
notify.MessageID = notifyHistory.ID) WHERE notify.UserID = ?;");
$sql->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
$numMails  = $sql->fetchColumn();
$numPages = ((int)($numMails / 10)) + 1;

if ($start > $numMails) {
  halt(404);
}

$sql = $db->prepare("SELECT `notifyHistory`.`Subject`, `notifyHistory`.`Message`, `notify`.`ForceSend`, `Forename`, `Surname`, `JSONData`, `Date` FROM ((`notifyHistory` LEFT JOIN `users` ON notifyHistory.Sender = users.UserID) INNER JOIN `notify` ON notify.MessageID = notifyHistory.ID) WHERE notify.UserID = :user ORDER BY `EmailID` DESC LIMIT :offset, :num;");
$sql->bindValue(':user', $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'], PDO::PARAM_INT);
$sql->bindValue(':offset', $start, PDO::PARAM_INT);
$sql->bindValue(':num', 10, PDO::PARAM_INT);
$sql->execute();
$row = $sql->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Message History";

include BASE_PATH . "views/header.php";
//include BASE_PATH . "views/notifyMenu.php";
?>

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

  .force-wrap:last-child,
  .force-wrap p:last-child {
    margin-bottom: 0px;
  }
</style>

<div class="container-fluid">

  <div class="row justify-content-between">
    <div class="col-md-3 d-none d-md-block">
      <?php
      $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/myaccount/ProfileEditorLinks.json'));
      echo $list->render('notify');
      ?>
    </div>
    <div class="col-md-9">
      <h1>My Message History</h1>

      <?php if ($numMails == 0) { ?>
        <p class="lead">
          All emails sent to you using our <a href="<?= htmlspecialchars(autoUrl('notify')) ?>">Notify</a> system.
        </p>

        <div class="alert alert-warning">
          <p class="mb-0">
            <strong>There are no messages to view right now</strong>
          </p>
          <p class="mb-0">
            Check back later.
          </p>
        </div>
      <?php
      } else { ?>
        <p class="lead">
          Page <?= htmlspecialchars($page) ?> of <?= $numPages ?>
        </p>
        <?php do {
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
                  <?= htmlspecialchars($row['Subject']) ?>
                </strong>
              </p>
              <dl class="row mb-0 small">
                <?= $sender ?>
                <?php if ($row['JSONData'] != "") { ?>
                  <dt class="col-sm-3">Sent To</dt>
                  <dd class="col-sm-9">
                    <?php
                    $squads = [];
                    if (isset($info->To->Squads)) {
                      $squads = (array) $info->To->Squads;
                    }
                    $lists = [];
                    if (isset($info->To->Targeted_Lists)) {
                      $lists = (array) $info->To->Targeted_Lists;
                    }
                    $galas = [];
                    if (isset($info->To->Galas)) {
                      $galas = (array) $info->To->Galas;
                    }
                    $array = array_merge($squads, $lists, $galas);
                    sort($array);
                    foreach ($array as $s) { ?>
                      <span class="badge badge-pill rounded badge-dark">
                        <?= htmlspecialchars(mb_strimwidth($s, 0, 40)) ?>
                      </span><?php
                            } ?>
                  </dd>
                  <dt class="col-sm-3">Date</dt>
                  <dd class="col-sm-9">
                    <?php
                    $date = new DateTime($row['Date'], new DateTimeZone('UTC'));
                    $date->setTimezone(new DateTimeZone('Europe/London')); ?>
                    <?= $date->format('H:i \o\\n l j F Y') ?>
                  </dd>
                  <?php if (isset($info->Attachments)) { ?>
                    <dt class="col-sm-3 mb-0">Attachments</dt>
                    <dd class="col-sm-9 mb-0">
                      <?php $attachments = (array) $info->Attachments;
                      foreach ($attachments as $a) {
                        $faClass = ' fa-file-o ';
                        $download = false;

                        if (isset($a->MIME) && $a->MIME) {

                          if ($a->MIME == 'application/pdf') {
                            $faClass = ' fa-file-pdf-o ';
                          }

                          if (mb_substr($a->MIME, 0, mb_strlen('image')) === 'image') {
                            $faClass = ' fa-file-image-o ';
                          }

                          if (mb_substr($a->MIME, 0, mb_strlen('video')) === 'video') {
                            $faClass = ' fa-file-video-o ';
                          }

                          if (mb_substr($a->MIME, 0, mb_strlen('text')) === 'text') {
                            $faClass = ' fa-file-text-o ';
                            $download = true;
                          }

                          if ($a->MIME == 'application/msword' || $a->MIME == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                            $faClass = ' fa-file-word-o ';
                            $download = true;
                          }

                          if ($a->MIME == 'application/vnd.ms-powerpoint' || $a->MIME == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
                            $faClass = ' fa-file-powerpoint-o ';
                            $download = true;
                          }

                          if ($a->MIME == 'application/vnd.ms-excel' || $a->MIME == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                            $faClass = ' fa-file-powerpoint-o ';
                            $download = true;
                          }
                        } else {
                          $download = true;
                        }
                      ?>
                        <a href="<?= htmlspecialchars(autoUrl("files/" . $a->URI)) ?>" class="d-block mb-1 text-truncate text-decoration-none" <?php if ($download) { ?> download="" <?php } else { ?> target="_blank" <?php } ?>>
                          <span class="fa <?= htmlspecialchars($faClass) ?> fa-fw"></span> <?= htmlspecialchars($a->Filename) ?>
                        </a>
                      <?php
                      } ?>
                    </dd>
                  <?php } ?>
              </dl>
            <?php } ?>
            </div>
            <div class="bg-light p-3 pt-0 force-wrap">
              <?= $row['Message'] ?>
            </div>
          </div>
        <?php } while ($row = $sql->fetch(PDO::FETCH_ASSOC)); ?>

        <nav aria-label="Page navigation">
          <ul class="pagination mb-0">
            <?php if ($numMails <= 10) { ?>
              <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
            <?php } else if ($numMails <= 20) { ?>
              <?php if ($page == 1) { ?>
                <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page + 1 ?>"><?php echo $page + 1 ?></a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page + 1 ?>">Next</a></li>
              <?php } else { ?>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page - 1 ?>">Previous</a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page - 1 ?>"><?php echo $page - 1 ?></a></li>
                <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
              <?php } ?>
            <?php } else { ?>
              <?php if ($page == 1) { ?>
                <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page + 1 ?>"><?php echo $page + 1 ?></a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page + 2 ?>"><?php echo $page + 2 ?></a></li>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page + 1 ?>">Next</a></li>
              <?php } else { ?>
                <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page - 1 ?>">Previous</a></li>
                <?php if ($page > 2) { ?>
                  <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page - 2 ?>"><?php echo $page - 2 ?></a></li>
                  <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page - 1 ?>"><?php echo $page - 1 ?></a></li>
                  <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
                  <?php if ($numMails > $page * 10) { ?>
                    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page + 1 ?>"><?php echo $page + 1 ?></a></li>
                    <?php if ($numMails > $page * 10 + 10) { ?>
                      <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page + 2 ?>"><?php echo $page + 2 ?></a></li>
                    <?php } ?>
                    <li class="page-item"><a class="page-link" href="<?php echo autoUrl("my-account/notifyhistory?page="); ?><?php echo $page + 1 ?>">Next</a></li>
                  <?php } ?>
                <?php } ?>
            <?php }
            } ?>
          </ul>
        </nav>
      <?php } ?>
    </div>
  </div>
</div>

<?php
$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->render();
