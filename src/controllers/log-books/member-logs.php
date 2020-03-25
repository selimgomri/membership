<?php

global $db;

$start = 0;
$page = 0;

if (isset($_GET['page']) && ((int) $_GET['page']) != 0) {
  $page = (int) $_GET['page'];
  $start = ($page-1)*10;
} else {
  $page = 1;
}

$getMember = $db->prepare("SELECT MForename fn, MSurname sn, members.UserID FROM members INNER JOIN squads ON squads.SquadID = members.SquadID WHERE members.MemberID = ?");
$getMember->execute([$member]);
$memberInfo = $getMember->fetch(PDO::FETCH_ASSOC);

if ($memberInfo == null) {
  halt(404);
}

if ($_SESSION['AccessLevel'] == 'Parent' && $memberInfo['UserID'] != $_SESSION['UserID']) {
  halt(404);
}

$getCount = $db->prepare("SELECT COUNT(*) FROM trainingLogs WHERE Member = ?");
$getCount->execute([$member]);
$numLogs  = $getCount->fetchColumn();
$numPages = ((int)($numLogs/10)) + 1;

$getLogs = $db->prepare("SELECT `ID`, `DateTime`, Title, Content, ContentType FROM trainingLogs WHERE Member = :member ORDER BY `DateTime` DESC LIMIT :offset, :num");
$getLogs->bindValue(':member', $member, PDO::PARAM_INT);
$getLogs->bindValue(':offset', $start, PDO::PARAM_INT); 
$getLogs->bindValue(':num', 10, PDO::PARAM_INT); 
$getLogs->execute();
$log = $getLogs->fetch(PDO::FETCH_ASSOC);

$pagetitle = htmlspecialchars($memberInfo['fn'] . ' ' . $memberInfo['sn'] . " log book");

$markdown = new ParsedownExtra();

include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <?php if (isset($_SESSION['LogBooks-MemberLoggedIn']) && bool($_SESSION['LogBooks-MemberLoggedIn'])) { ?>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Log book</li>
      </ol>
    </nav>
    <?php } else { ?>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("log-books"))?>">Members</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars(mb_substr($memberInfo['fn'], 0, 1, 'utf-8') . mb_substr($memberInfo['sn'], 0, 1, 'utf-8'))?></li>
      </ol>
    </nav>
    <?php } ?>

    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1>
          <?=htmlspecialchars($memberInfo['fn'] . ' ' . $memberInfo['sn'])?>'s log book
        </h1>
        <p class="lead mb-0">
          You can log training sessions and other activity.
        </p>
        <div class="mb-3 d-lg-none"></div>
      </div>
      <div class="col text-right">
        <p class="mb-0">
          <a href="<?=htmlspecialchars(autoUrl("log-books/members/" . $member . "/new"))?>" class="btn btn-success">New <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
        </p>
      </div>
    </div>

  </div>
</div>

<div class="container">

  <?php if (isset($_SESSION['AddLogSuccessMessage'])) { ?>
  <div class="alert alert-success">
    <p>
      <strong>Your new entry has been added to your training log.</strong>
    </p>
    <p class="mb-0">
      <a class="btn btn-success" href="<?=htmlspecialchars("#log-" . $_SESSION['AddLogSuccessMessage'])?>">Jump to log</a>
    </p>
  </div>
  <?php unset($_SESSION['AddLogSuccessMessage']); } ?>

  <?php if (isset($_SESSION['SetMemberPassSuccess'])) { ?>
  <div class="alert alert-success">
    <p class="mb-0">
      <strong>Member password updated.</strong>
    </p>
  </div>
  <?php unset($_SESSION['SetMemberPassSuccess']); } ?>

  <div class="row">
    <div class="col-lg-8">
      <?php if ($log) { ?>
      <div class="row mb-3">
        <div class="col">
          <p class="lead mb-0">
            Page <?=htmlspecialchars($page)?> of <?=htmlspecialchars($numPages)?>
          </p>
        </div>
        <div class="col text-right">
          <p class="lead text-muted mb-0">
            <?=htmlspecialchars($numLogs)?> training log<?php if ($numLogs != 1) { ?>s<?php } ?> in total
          </p>
        </div>
      </div>

      <ul class="list-group">
        <?php do {
          $dateObject = new DateTime($log['DateTime'], new DateTimeZone('UTC'));
          $dateObject->setTimezone(new DateTimeZone('Europe/London'));
          ?>
        <li class="list-group-item" id="<?=htmlspecialchars("log-" . $log['ID'])?>">
          <div class="row justify-content-between">
            <div class="col-auto">
              <h2><a href="<?=htmlspecialchars(autoUrl("log-books/logs/" . $log['ID']))?>"><?=htmlspecialchars($log['Title'])?></a></h2>
              <p class="mb-0"><?=htmlspecialchars($dateObject->format("H:i \\o\\n j F Y"))?></p>
            </div>
            <div class="col-auto">
              <p class="mb-0">
                <a href="<?=htmlspecialchars(autoUrl("log-books/logs/" . $log['ID'] . "/edit"))?>" class="btn btn-light">Edit <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
              </p>
            </div>
          </div>
          <hr>
          <?php if (mb_strtolower($log['ContentType']) == 'text/markdown') { ?>
          <div class="blog-main">
            <?= $markdown->text($log['Content']) ?>
          </div>
          <?php } else if (mb_strtolower($log['ContentType']) == 'text/plain-monospace') { ?>
          <div class="mono">
            <?=nl2br(htmlspecialchars($log['Content']))?>
          </div>
          <?php } else { ?>
          <div>
            <?=nl2br(htmlspecialchars($log['Content']))?>
          </div>
          <?php } ?>
        </li>
        <?php } while ($log = $getLogs->fetch(PDO::FETCH_ASSOC)); ?>
      </ul>

      <!-- Pagination -->
      <nav aria-label="Page navigation">
        <ul class="pagination">
          <?php if ($numLogs <= 10) { ?>
          <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
          <?php } else if ($numLogs <= 20) { ?>
            <?php if ($page == 1) { ?>
            <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page+1 ?>">Next</a></li>
            <?php } else { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page-1 ?>">Previous</a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page-1 ?>"><?php echo $page-1 ?></a></li>
            <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
            <?php } ?>
          <?php } else { ?>
            <?php if ($page == 1) { ?>
            <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page+2 ?>"><?php echo $page+2 ?></a></li>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page+1 ?>">Next</a></li>
            <?php } else { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page-1 ?>">Previous</a></li>
            <?php if ($page > 2) { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page-2 ?>"><?php echo $page-2 ?></a></li>
            <?php } ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page-1 ?>"><?php echo $page-1 ?></a></li>
            <li class="page-item active"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page ?>"><?php echo $page ?></a></li>
            <?php if ($numLogs > $page*10) { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page+1 ?>"><?php echo $page+1 ?></a></li>
            <?php if ($numLogs > $page*10+10) { ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page+2 ?>"><?php echo $page+2 ?></a></li>
            <?php } ?>
            <li class="page-item"><a class="page-link" href="<?php echo autoUrl("log-books/members/" . $member . "?page="); ?><?php echo $page+1 ?>">Next</a></li>
            <?php } ?>
          <?php } ?>
        <?php } ?>
        </ul>
      </nav>

      <?php } else { ?>
      <div class="alert alert-warning">
        <p class="mb-0">
          <strong>There are no logs to display</strong>
        </p>
        <p class="mb-0">
          <a href="<?=htmlspecialchars(autoUrl("log-books/members/" . $member . "/new"))?>" class="alert-link">Create a new training log</a> to get started.
        </p>
      </div>
      <?php } ?>
    </div>
    <div class="col">
      <div class="cell">
        <h2>Log books are new!</h2>
        <p class="lead">
          We have added log books to the membership system as a response to the coronavirus (COVID-19) outbreak.
        </p>
        <p>
          This is to allow members to log their home based land training.
        </p>
        <p class="mb-0">
          As always, feedback is very welcome. Send it to <a href="mailto:feedback@myswimmingclub.uk">feedback@myswimmingclub.uk</a>
        </p>
      </div>

      <?php if (isset($_SESSION['AccessLevel']) && $_SESSION['AccessLevel'] == 'Parent') { ?>
      <div class="cell">
        <h2>Member access</h2>
        <p class="lead">
          You can give <?=htmlspecialchars($memberInfo['fn'])?> access to their log book with their own account!
        </p>
        <p>
          You just need to create a password for them to get started. If <?=htmlspecialchars($memberInfo['fn'])?> ever forgets, you can reset their password yourself by coming back to this page.
        </p>
        <p>
          <a href="<?=htmlspecialchars(autoUrl("members/" . $member . "/password?return=" . urlencode(autoUrl("log-books/members/" . $member))))?>" class="btn btn-primary">
            Password settings
          </a>
        </p>

      </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();