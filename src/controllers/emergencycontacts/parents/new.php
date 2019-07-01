<?php

$use_white_background = true;
$pagetitle = "New Emergency Contact";

include BASE_PATH . 'views/header.php';
if ($renewal_trap) {
	include BASE_PATH . 'views/renewalTitleBar.php';
}

?>

<div class="container">

	<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("emergency-contacts")?>">Galas</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add new</li>
    </ol>
  </nav>

	<div class="">
		<h1>
			Add a new Emergency Contact
		</h1>

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

<?php

include BASE_PATH . 'views/footer.php';
