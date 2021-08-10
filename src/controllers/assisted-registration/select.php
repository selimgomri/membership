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

<div class="container-xl">
  <div class="row">
    <div class="col-md-8">
      <h1>Select Members</h1>
      <p class="lead">
        Select <?= htmlspecialchars($user['first']) ?>'s member(s)
      </p>

      <div class="alert alert-warning">
        <p class="mb-0">
          <strong>
            Only members without a linked user/parent account are shown.
          </strong>
        </p>
      </div>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['AssRegFormError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['AssRegFormError']) { ?>
        <div class="alert alert-warning">
          <strong>There was a problem with some of the data supplied</strong>
        </div>
      <?php } ?>

      <form method="post" id="select-form">

        <?php while ($swimmer = $swimmers->fetch(PDO::FETCH_ASSOC)) {
          $getSquads->execute([
            $swimmer['id']
          ]);
        ?>
          <div class="card card-body mb-3">
            <div class="form-check mb-0">
              <input class="form-check-input" type="checkbox" id="member-<?= htmlspecialchars($swimmer['id']) ?>" name="member-<?= htmlspecialchars($swimmer['id']) ?>" data-collapse="member-<?= htmlspecialchars($swimmer['id']) ?>-collapse" value="1" autocomplete="off">
              <label class="form-check-label" for="member-<?= htmlspecialchars($swimmer['id']) ?>">
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

            <div class="collapse" id="member-<?= htmlspecialchars($swimmer['id']) ?>-collapse">
              <div class="form-check custom-control-inline mt-2">
                <input type="radio" id="member-rr-yes-<?= htmlspecialchars($swimmer['id']) ?>" name="member-rr-<?= htmlspecialchars($swimmer['id']) ?>" class="form-check-input" checked value="yes">
                <label class="form-check-label" for="member-rr-yes-<?= htmlspecialchars($swimmer['id']) ?>">Require registration</label>
              </div>
              <div class="form-check custom-control-inline mb-0">
                <input type="radio" id="member-rr-no-<?= htmlspecialchars($swimmer['id']) ?>" name="member-rr-<?= htmlspecialchars($swimmer['id']) ?>" class="form-check-input" value="no">
                <label class="form-check-label" for="member-rr-no-<?= htmlspecialchars($swimmer['id']) ?>">Add to account quietly</label>
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
$footer->addJS("js/NeedsValidation.js");
$footer->addJS("js/assisted-registration/select-swimmers.js");
$footer->render();
