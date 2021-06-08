<?php

$db = app()->db;

$rrRequirement = 1;

$getUser = $db->prepare("SELECT UserID, Forename, Surname, EmailAddress, Mobile, `Password` FROM users WHERE UserID = ? AND RR = ?");
$getUser->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGuestUser'], $rrRequirement]);
$user = $getUser->fetch(PDO::FETCH_ASSOC);

// $um = userMember($user['UserID']);
$um = false;

if ($user == null) {
  halt(404);
}

$getUserSwimmers = $db->prepare("SELECT MForename fn, MSurname sn FROM members WHERE UserID = ?");
$getUserSwimmers->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['AssRegGuestUser']]);

$pagetitle = "Welcome to " . htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) . " - Assisted Registration";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Hello <?= htmlspecialchars($user['Forename']) ?></h1>
      <p class="lead">
        Welcome to <?= htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) ?>. Let's complete your account registration.
      </p>

      <p>
        You will register for a user account for the following members:
      </p>

      <ul>
        <?php while ($s = $getUserSwimmers->fetch(PDO::FETCH_ASSOC)) { ?>
          <li><?= htmlspecialchars($s['fn'] . ' ' . $s['sn']) ?></li>
        <?php } ?>
      </ul>

      <p>
        If any of your members are missing from this list, please contact the membership secretary before you continue.
      </p>

      <p>
        <a href="<?= autoUrl("assisted-registration/confirm-details") ?>" class="btn btn-success">
          Continue
        </a>
      </p>

    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJS("js/NeedsValidation.js");
$footer->render();
