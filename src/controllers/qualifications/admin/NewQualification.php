<?php

$pagetitle = "New Qualification";

global $db;
$getUser = $db->prepare("SELECT COUNT(*) FROM users WHERE UserID = ?");
$getUser->execute([$person]);

$qualifications = $db->query("SELECT COUNT(*) FROM qualificationsAvailable");
$qualificationsCount = $qualifications->fetchColumn();
$qualifications = $db->query("SELECT ID, `Name` FROM qualificationsAvailable ORDER BY `Name` ASC");

if ($getUser->fetchColumn() == 0 || $qualificationsCount == 0) {
  halt(404);
}

$getUser = $db->prepare("SELECT Forename, Surname FROM users WHERE UserID = ?");
$getUser->execute([$person]);

$user = $getUser->fetch(PDO::FETCH_ASSOC);

$name = $user['Forename'] . ' ' . $user['Surname'];

$form = $_SESSION['NewQualificationData'];

if ($form['valid-from'] == null) {
  $form['valid-from'] = date("Y-m-d");
}

if ($form['valid-to'] == null) {
  $form['valid-to'] = date("Y-m-d");
}

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>New Qualification</h1>

  <div class="row">
    <div class="col-md-8">
      <p class="lead">
        Add a new qualification for <?=htmlspecialchars($name)?>.
      </p>

      <form method="post" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="name">Select qualification</label>
          <select class="custom-select" id="name" name="name">
            <option selected disabled>Select a qualification</option>
          <?php while ($qualification = $qualifications->fetch(PDO::FETCH_ASSOC)) { ?>
            <option value="<?=$qualification['ID']?>"><?=htmlspecialchars($qualification['Name'])?></option>
          <?php } ?>
          </select>
        </div>

        <div class="form-group">
          <label for="info">Qualification information (Optional)</label>
          <textarea class="form-control" id="info" name="info"><?=htmlspecialchars($form['info'])?></textarea>
        </div>

        <div class="form-group">
          <label for="valid-from">Valid From</label>
          <input type="date" class="form-control" id="valid-from" name="valid-from" value="<?=htmlspecialchars($form['valid-from'])?>" required>
        </div>

        <div class="custom-control custom-checkbox form-group">
          <input type="checkbox" class="custom-control-input" value="1" id="expires" name="expires" onclick="toggleState('valid-box', 'expires')">
          <label class="custom-control-label" for="expires">Does this qualification expire?</label>
        </div>

        <div class="form-group d-none" id="valid-box">
          <label for="valid-to">Valid To</label>
          <input type="date" class="form-control" id="valid-to" name="valid-to" value="<?=htmlspecialchars($form['valid-to'])?>">
        </div>

        <button type="submit" class="btn btn-success">
          Add Qualification
        </button>
      </form>
    </div>
  </div>
</div>

<script>
function toggleState(id, check) {
	var element = document.getElementById(id);
  var check = document.getElementById(check);
  if (check.checked) {
    element.classList.remove("d-none");
  } else {
    element.classList.add("d-none");
  }
}
</script>

<script defer src="<?=autoUrl("public/js/NeedsValidation.js")?>"></script>

<?php

include BASE_PATH . 'views/footer.php';
