<?php

global $db;

$getInfo = $db->prepare("SELECT members.MemberID, MForename fn, MSurname sn, members.UserID, trainingLogs.Title, trainingLogs.Content, trainingLogs.ContentType, trainingLogs.DateTime FROM trainingLogs INNER JOIN members ON trainingLogs.Member = members.MemberID WHERE trainingLogs.ID = ?");
$getInfo->execute([$id]);
$info = $getInfo->fetch(PDO::FETCH_ASSOC);

if ($info == null) {
  halt(404);
}

if ($_SESSION['AccessLevel'] == 'Parent' && $info['UserID'] != $_SESSION['UserID']) {
  halt(404);
}

if (isset($_SESSION['LogBooks-MemberLoggedIn']) && bool($_SESSION['LogBooks-MemberLoggedIn'])) {
  if ($_SESSION['LogBooks-Member'] != $info['MemberID']) {
    halt(404);
  }
}

$pagetitle = htmlspecialchars("Edit log entry - " . $info['fn'] . ' ' . $info['sn']);

$title = $entry = $date = $time = "";
$contentType = "text/plain";

if (isset($info['Title'])) {
  $title = $info['Title'];
}
if (isset($info['Content'])) {
  $entry = $info['Content'];
}
if (isset($info['ContentType'])) {
  $contentType = $info['ContentType'];
}
if (isset($info['DateTime'])) {
  $dateObject = new DateTime($info['DateTime'], new DateTimeZone('UTC'));
  $dateObject->setTimezone(new DateTimeZone('Europe/London'));
  $date = $dateObject->format("Y-m-d");
  $time = $dateObject->format("H:i");
}

include BASE_PATH . 'views/header.php';

?>

<form method="post" class="needs-validation" novalidate>

  <div class="bg-light mt-n3 py-3 mb-3">
    <div class="container">

      <?php if (isset($_SESSION['LogBooks-MemberLoggedIn']) && bool($_SESSION['LogBooks-MemberLoggedIn'])) { ?>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("log-books"))?>">Log book</a></li>
        <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("log-books/logs/" . $id))?>">#<?=htmlspecialchars($id)?></a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
      </nav>
      <?php } else { ?>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("log-books"))?>">Members</a></li>
          <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("log-books/members/" . $info['MemberID']))?>"><?=htmlspecialchars(mb_substr($info['fn'], 0, 1, 'utf-8') . mb_substr($info['sn'], 0, 1, 'utf-8'))?></a></li>
          <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("log-books/logs/" . $id))?>">#<?=htmlspecialchars($id)?></a></li>
          <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
      </nav>
      <?php } ?>

      <div class="row align-items-center">
        <div class="col-lg-8">
          <h1>
            Edit log book entry
          </h1>
          <p class="lead mb-0">
            <?=htmlspecialchars($info['fn'] . ' ' . $info['sn'])?>'s log book
          </p>
          <div class="mb-3 d-lg-none"></div>
        </div>
        <div class="col text-right">
          <p class="mb-0">
            <button type="submit" class="btn btn-success">Save <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
          </p>
        </div>
      </div>

    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-lg-8">

        <?php if (isset($_SESSION['EditLogErrorMessage'])) { ?>
        <div class="alert alert-danger">
        <?=$_SESSION['EditLogErrorMessage']?>
        </div>
        <?php unset($_SESSION['EditLogErrorMessage']); } ?>

        <?php if (isset($_SESSION['EditLogSuccessMessage']) && bool($_SESSION['EditLogSuccessMessage'])) { ?>
        <div class="alert alert-success">
          <p class="mb-0">
            <strong>Changes saved successfully</strong>
          </p>
        </div>
        <?php unset($_SESSION['EditLogSuccessMessage']); } ?>

        <h2>Log entry</h2>
        <div class="form-group">
          <label for="title">Log title</label>
          <input type="text" required class="form-control" id="title" name="title" placeholder="e.g. Swimming training" value="<?=htmlspecialchars($title)?>">
          <div class="invalid-feedback">
            You must give this log entry a title.
          </div>
        </div>

        <div class="form-group">
          <label for="entry">Log entry</label>
          <textarea rows="15" required class="form-control" id="entry" name="entry" placeholder="e.g.&#13;&#10;5 x 200m free&#13;&#10;10 x 50m back"><?=htmlspecialchars($entry)?></textarea>
          <div class="invalid-feedback">
            You must fill out the log entry.
          </div>
        </div>

      </div>
      <div class="col">
        <div class="cell">
          <h2>More options</h2>
          <p>
            You can edit the time and date for this activity.
          </p>
          <div class="form-group">
            <label for="date">Date</label>
            <input class="form-control" id="date" name="date" type="date" value="<?=htmlspecialchars($date)?>">
            <div class="invalid-feedback">
              You must enter a valid date.
            </div>
          </div>

          <div class="form-group">
            <label for="time">Time</label>
            <input class="form-control" id="time" name="time" type="time" value="<?=htmlspecialchars($time)?>">
            <div class="invalid-feedback">
              You must enter a valid time.
            </div>
          </div>

          <div class="form-group mb-0">
            <label for="content-type">Content type</label>
            <select required class="custom-select" id="content-type" name="content-type" aria-describedby="content-type-help">
              <option value="text/plain" <?php if ($contentType == 'text/plain') { ?>selected<?php } ?> >Plain text</option>
              <option value="text/plain-monospace" <?php if ($contentType == 'text/plain-monospace') { ?>selected<?php } ?> >Monospaced plain text</option>
              <option value="text/markdown" <?php if ($contentType == 'text/markdown') { ?>selected<?php } ?> >Markdown</option>
            </select>
            <div class="invalid-feedback">
              You must select a content type.
            </div>
            <small id="content-type-help" class="form-text text-muted">
              The content type you select will affect how this log entry gets displayed when you're not editing.
            </small>
          </div>
        </div>
      </div>
    </div>

    <p>
      <button type="submit" class="btn btn-success">Save <i class="fa fa-floppy-o" aria-hidden="true"></i></button>
    </p>

  </div>

</form>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();