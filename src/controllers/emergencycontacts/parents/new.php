<?php

$use_white_background = true;
$pagetitle = "New Emergency Contact";

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

        <form method="post">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
          </div>
          <div class="form-group">
            <label for="num">Contact Number</label>
            <input type="tel" class="form-control" id="num" name="num" placeholder="Phone" required>
          </div>
          <button type="submit" class="btn btn-success">Add</button>
        </form>

      </div>
    </div>

  </div>
</div>

<?php

include BASE_PATH . 'views/footer.php';