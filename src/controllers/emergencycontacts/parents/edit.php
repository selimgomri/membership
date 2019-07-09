<?php

global $db;

$url_path = "emergency-contacts";
if (isset($renewal_trap) && $renewal_trap) {
	$url_path = "renewal/emergencycontacts";
}

$user = $_SESSION['UserID'];

$contact = new EmergencyContact();
$contact->connect($db);
$contact->getByContactID($id);

if ($contact->getUserID() != $user) {
	halt(404);
}

$pagetitle = htmlspecialchars($contact->getName()) . " - Emergency Contacts";

include BASE_PATH . 'views/header.php';
if (isset($renewal_trap) && $renewal_trap) {
	include BASE_PATH . 'views/renewalTitleBar.php';
}

?>

<div class="container">

  <?php if (!isset($renewal_trap) || !$renewal_trap) { ?>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("emergency-contacts")?>">Emergency Contacts</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit <?=htmlspecialchars($contact->getName())?></li>
    </ol>
  </nav>
  <?php } ?>

  <div class="">
    <h1>
      Edit <?=htmlspecialchars($contact->getName())?>
    </h1>

    <div class="row">
      <div class="col-lg-8">

        <form method="post">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name"
              value="<?=htmlspecialchars($contact->getName())?>" required>
          </div>
          <div class="form-group">
            <label for="num">Contact Number</label>
            <input type="tel" class="form-control" id="num" name="num" placeholder="Phone"
              value="<?=htmlspecialchars($contact->getContactNumber())?>" required>
          </div>
          <p>
            <button type="submit" class="btn btn-success">Save</button>
            <a href="<?=autoUrl($url_path . "/" . $id . "/delete")?>" class="btn btn-danger">Delete</a>
          </p>
        </form>

      </div>
    </div>

  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';