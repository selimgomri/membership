<?php

$user = $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegUser'];

$db = app()->db;
$tenant = app()->tenant;

$swimmers = $db->prepare("SELECT MForename `first`, MSurname `last`, MemberID `id`, RRTransfer trans FROM members WHERE Active AND Tenant = ? AND members.UserID IS NULL ORDER BY MemberID DESC, `first` ASC, `last` ASC");
$swimmers->execute([
  $tenant->getId()
]);

$getSquads = $db->prepare("SELECT SquadName FROM squads INNER JOIN squadMembers ON squads.SquadID = squadMembers.Squad WHERE Member = ?");

$user = $db->prepare("SELECT Forename `first` FROM users WHERE UserID = ?");
$user->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['AssRegUser']]);
$user = $user->fetch(PDO::FETCH_ASSOC);

if ($user == null) {
  halt(404);
}

$pagetitle = "Select Members - Assisted Registration";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Select Swimmers</h1>
      <p class="lead">
        Select <?= htmlspecialchars($user['first']) ?>'s member(s)
      </p>

      <div class="alert alert-warning">
        <p class="mb-0">
          <strong>
            Only swimmers without a linked parent are shown.
          </strong>
        </p>
      </div>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegFormError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegFormError']) { ?>
        <div class="alert alert-warning">
          <strong>There was a problem with some of the data supplied</strong>
        </div>
      <?php } ?>

      <form method="post">

        <?php while ($swimmer = $swimmers->fetch(PDO::FETCH_ASSOC)) {
          $getSquads->execute([
            $swimmer['id']
          ]);
        ?>
          <div class="mb-3">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input" id="member-<?= htmlspecialchars($swimmer['id']) ?>" name="member-<?= htmlspecialchars($swimmer['id']) ?>">
              <label class="custom-control-label" for="member-<?= htmlspecialchars($swimmer['id']) ?>">
                <p class="mb-0 font-weight-bold">
                  <?= htmlspecialchars($swimmer['first'] . ' ' . $swimmer['last']) ?>
                  <?php if (isset($swimmer['trans']) && $swimmer['trans']) { ?>
                    - <span class="text-success">Transferring from another club</span>
                  <?php } ?>
                </p>
                <ul class="mb-0 list-unstyled">
                  <?php if ($squad = $getSquads->fetch(PDO::FETCH_ASSOC)) {
                    do { ?>
                      <li><?= htmlspecialchars($squad['SquadName']) ?></li>
                    <?php } while ($squad = $getSquads->fetch(PDO::FETCH_ASSOC));
                  } else { ?>
                    <li>No squads</li>
                  <?php } ?>
                </ul>
              </label>
            </div>

            <div class="">
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="member-rr-yes-<?= htmlspecialchars($swimmer['id']) ?>" name="member-rr-<?= htmlspecialchars($swimmer['id']) ?>" class="custom-control-input" checked value="yes">
                <label class="custom-control-label" for="member-rr-yes-<?= htmlspecialchars($swimmer['id']) ?>">Require registration</label>
              </div>
              <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="member-rr-no-<?= htmlspecialchars($swimmer['id']) ?>" name="member-rr-<?= htmlspecialchars($swimmer['id']) ?>" class="custom-control-input" value="no">
                <label class="custom-control-label" for="member-rr-no-<?= htmlspecialchars($swimmer['id']) ?>">Add to account quietly</label>
              </div>
            </div>
          </div>
        <?php } ?>

        <p>
          <button class="btn btn-success" type="submit">
            Continue
          </button>
        </p>
      </form>
    </div>
  </div>
</div>

<?php

if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegFormError'])) {
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegFormError']);
}

$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();
