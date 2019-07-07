<?php

global $db;

$getUser = $db->prepare("SELECT UserID, Forename, Surname, EmailAddress, Mobile, `Password` FROM users WHERE UserID = ? AND RR = ?");
$getUser->execute([$_SESSION['AssRegGuestUser'], true]);
$user = $getUser->fetch(PDO::FETCH_ASSOC);

if ($user == null) {
  halt(404);
}

$getUserSwimmers = $db->prepare("SELECT MForename fn, MSurname sn FROM members WHERE UserID = ?");
$getUserSwimmers->execute([$_SESSION['AssRegGuestUser']]);

$pagetitle = "Welcome to " . htmlspecialchars(env('CLUB_NAME')) . " - Assisted Registration";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Hello <?=htmlspecialchars($user['Forename'])?></h1>
      <p class="lead">
        Welcome to <?=htmlspecialchars(env('CLUB_NAME'))?>. Let's complete your account registration.
      </p>

      <p>
        You will register yourself (for a parent account) and the following swimmers:
      </p>

      <ul>
      <?php while ($s = $getUserSwimmers->fetch(PDO::FETCH_ASSOC)) { ?>
        <li><?=htmlspecialchars($s['fn'] . ' ' . $s['sn'])?></li>
      <?php } ?>
      </ul>

      <p>
        If any of your swimmers are missing from this list, please contact the membership secretary before you continue.
      </p>

      <p>
        <a href="<?=autoUrl("assisted-registration/confirm-details")?>" class="btn btn-success">
          Continue
        </a>
      </p>

    </div>
  </div>
</div>

<script defer src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

include BASE_PATH . 'views/footer.php';