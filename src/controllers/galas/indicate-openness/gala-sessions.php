<?php

global $db;
$galaDetails = $db->prepare("SELECT GalaName `name`, GalaDate `ends` FROM galas WHERE GalaID = ?");
$galaDetails->execute([$id]);
$gala = $galaDetails->fetch(PDO::FETCH_ASSOC);

if ($gala == null) {
  halt(404);
}

$galaDate = new DateTime($gala['ends'], new DateTimeZone('Europe/London'));
$nowDate = new DateTime('now', new DateTimeZone('Europe/London'));

$getSessions = $db->prepare("SELECT `Name`, `ID` FROM galaSessions WHERE Gala = ? ORDER BY `ID` ASC");
$getSessions->execute([$id]);
$session = $getSessions->fetch(PDO::FETCH_ASSOC);

$pagetitle = 'Add sessions to ' . htmlspecialchars($gala['name']);

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas/" . $id)?>"><?=htmlspecialchars($gala['name'])?></a></li>
      <li class="breadcrumb-item active" aria-current="page">Sessions</li>
    </ol>
  </nav>
  <div class="row">
    <div class="col-lg-8">
      <h1>Manage sessions at <?=htmlspecialchars($gala['name'])?></h1>
      <p class="lead">Adding sessions allows parents to indicate if a swimmer will be able to enter any given session.</p>

      <?php if (isset($_SESSION['SuccessStatus']) && $_SESSION['SuccessStatus']) { ?>
      <div class="alert alert-success">Saved</div>
      <?php unset($_SESSION['SuccessStatus']);
      } ?>

      <?php if (isset($_SESSION['ErrorStatus']) && $_SESSION['ErrorStatus']) { ?>
      <div class="alert alert-danger">Changes were not saved</div>
      <?php unset($_SESSION['ErrorStatus']);
      } ?>

      <?php if ($nowDate > $galaDate) { ?>
      <div class="alert alert-warning">
        This gala has finished. Changes you attempt to make will not be saved.
      </div>
      <?php } ?>

      <p>If you don't add sessions for a gala, parents will be unable indicate whether they can attend.</p>

      <form method="post">

        <?php if ($session == null) { ?>
        <div class="form-group">
          <label for="numSessions">Number of sessions</label>
          <input type="number" class="form-control" id="numSessions" name="numSessions" placeholder="Enter number of sessions" aria-describedby="numSessionsHelp">
          <small id="numSessionsHelp" class="form-text text-muted">We'll try to automatically name sessions. You can change these names in a moment.</small>
        </div>
        <?php } else {
          $i = 1;
          do { ?>
        <div class="row align-items-end mb-3">
          <div class="col-9">
            <div class="form-group mb-0">
              <label for="session-<?=$session['ID']?>">Session <?=$i?> name</label>
              <input type="text" class="form-control" id="session-<?=$session['ID']?>" name="session-<?=$session['ID']?>" value="<?=htmlspecialchars($session['Name'])?>" placeholder="Enter name of session">
            </div>
          </div>
          <div class="col">
            <a href="<?=autoUrl("galas/" . $id . "/sessions/" . $session['ID'] . "/delete")?>" class="btn btn-danger">
              Delete
            </a>
          </div>
        </div>
        <?php $i++; } while ($session = $getSessions->fetch(PDO::FETCH_ASSOC)); ?>
        <div class="form-group">
          <label for="newSession">Add a session</label>
          <input type="text" class="form-control" id="newSession" name="newSession" placeholder="Enter name of new session">
        </div>
        <?php } ?>

        <p>
          <button class="btn btn-success" type="submit">
            Go
          </button>
        </p>
      </form>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';