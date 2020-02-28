<?php

$use_white_background = true;
$pagetitle = "New Emergency Contact";

include BASE_PATH . 'views/header.php';
if (isset($renewal_trap) && $renewal_trap) {
	include BASE_PATH . 'views/renewalTitleBar.php';
}

$v = null;
if (isset($_SESSION['POST_DATA'])) {
  $v = $_SESSION['POST_DATA'];
  unset($_SESSION['POST_DATA']);
}

?>

<div class="container">

  <?php if (!isset($renewal_trap) || !$renewal_trap) { ?>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("emergency-contacts")?>">Emergency Contacts</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add new</li>
    </ol>
  </nav>
  <?php } ?>

  <div class="">
    <h1>
      Add a new Emergency Contact
    </h1>

    <div class="row">
      <div class="col-lg-8">

      <?php if (isset($_SESSION['AddNewError'])) {
			echo $_SESSION['AddNewError'];
			unset($_SESSION['AddNewError']);
		  } ?>

        <form method="post" class="needs-validation" novalidate>
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<?=htmlspecialchars($v['name'])?>" required>
            <div class="invalid-feedback">
              You must provide the name of the emergency contact
            </div>
          </div>
          

          <div class="form-group">
            <label for="relation">Relation</label>
            <input type="text" class="form-control" id="relation" name="relation" placeholder="Relation" value="<?=htmlspecialchars($v['relation'])?>" required>
            <div class="invalid-feedback">
              You must provide the relation so we can decide who is best to call
            </div>
          </div>

          <div class="form-group">
            <label for="num">Contact Number</label>
            <input type="tel" pattern="\+{0,1}[0-9]*" class="form-control" id="num" name="num" placeholder="Phone" value="<?=htmlspecialchars($v['num'])?>" required>
            <div class="invalid-feedback">
              You must provide a valid UK phone number
            </div>

          </div>
          <button type="submit" class="btn btn-success">Add</button>
        </form>

      </div>
    </div>

  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();