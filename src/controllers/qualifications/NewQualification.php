<?php

$pagetitle = "New Qualification";

global $db;
$getUser = $db->prepare("SELECT COUNT(*) FROM users WHERE UserID = ?");
$getUser->execute([$person]);

if ($getUser->fetchColumn() == 0) {
  halt(404);
}

$getUser = $db->prepare("SELECT Forename, Surname FROM users WHERE UserID = ?");
$getUser->execute([$person]);

$user = $getUser->fetch(PDO::FETCH_ASSOC);

$name = $user['Forename'] . ' ' . $user['Surname'];

$form = $_SESSION['NewQualificationData'];

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1>New Qualification</h1>

  <div class="row">
    <div class="col-md-8">
      <p class="lead">
        Add a new qualification for <?=htmlspecialchars($name)?>.
      </p>

      <form method="post">
        <div class="form-group">
          <label for="name">Qualification name</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="DBS Qualification" value="<?=htmlspecialchars($form['name'])?>">
        </div>

        <div class="form-group">
          <label for="info">Qualification information</label>
          <textarea class="form-control" id="info" name="info"><?=htmlspecialchars($form['info'])?></textarea>
        </div>

        <div class="row">
          <div class="col">
            <div class="form-group">
              <label for="valid-from">Valid From</label>
              <input type="date" class="form-control" id="valid-from" name="valid-from" value="<?=htmlspecialchars($form['valid-from'])?>">
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <div class="custom-control custom-checkbox">
                <label class="custom-control-label" for="expires">Qualification Expires</label>
                <input type="checkbox" class="custom-control-input" id="expires" name="expires" value="1">
              </div>
            </div>
          </div>

          <div class="col">
            <div class="form-group">
              <label for="valid-to">Valid To</label>
              <input type="date" class="form-control" id="valid-to" name="valid-to" value="<?=htmlspecialchars($form['valid-to'])?>">
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-success">
          Add Qualification
        </button>
      </form>
    </div>
  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';
