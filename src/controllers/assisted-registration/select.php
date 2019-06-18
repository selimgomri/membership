<?php

$user = $_SESSION['AssRegUser'];

global $db;

$swimmers = $db->query("SELECT MForename `first`, MSurname `last`, SquadName `name`, MemberID `id` FROM members INNER JOIN squads ON members.SquadID = squads.SquadID WHERE members.UserID IS NULL ORDER BY MemberID DESC, `first` ASC, `last` ASC");

$user = $db->prepare("SELECT Forename `first` FROM users WHERE UserID = ?");
$user->execute([$_SESSION['AssRegUser']]);
$user = $user->fetch(PDO::FETCH_ASSOC);

if ($user == null) {
  halt(404);
}

$pagetitle = "Select Swimmers - Assisted Registration";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>Select Swimmers</h1>
      <p class="lead">
        Select <?=htmlspecialchars($user['first'])?>'s swimmer(s)
      </p>

      <div class="alert alert-warning">
        <p class="mb-0">
          <strong>
            Only swimmers without a linked parent are shown.
          </strong>
        </p>
      </div>

      <?php if (isset($_SESSION['AssRegFormError']) && $_SESSION['AssRegFormError']) { ?>
      <div class="alert alert-warning">
        <strong>There was a problem with some of the data supplied</strong>
      </div>
      <?php } ?>

      <form method="post">

        <?php while ($swimmer = $swimmers->fetch(PDO::FETCH_ASSOC)) { ?>
        <div class="form-group">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="member-<?=htmlspecialchars($swimmer['id'])?>" name="member-<?=htmlspecialchars($swimmer['id'])?>">
            <label class="custom-control-label" for="member-<?=htmlspecialchars($swimmer['id'])?>">
              <?=htmlspecialchars($swimmer['first'] . ' ' . $swimmer['last'])?> <em><?=htmlspecialchars($swimmer['name'])?> Squad</em>
            </label>
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

<script defer src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

if (isset($_SESSION['AssRegFormError'])) {
  unset($_SESSION['AssRegFormError']);
}

include BASE_PATH . 'views/footer.php';