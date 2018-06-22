<?php
$pagetitle = "New Emergency Contact";
include BASE_PATH . "views/header.php";
?>

<div class="container">
	<h1>Create a new Emergency Contact</h1>
	<p class="lead">
		Please let this person know that you've added them as an emergency contact.
	</p>

	<form method="post">
		<div class="form-group">
	    <label for="name">Name</label>
	    <input type="text" class="form-control" id="name" name="name"
			placeholder="Enter name">
	  </div>

		<div class="form-group">
	    <label for="number">Contact Number</label>
	    <input type="text" class="form-control" id="number" name="number"
			aria-describedby="numberHelp" placeholder="Enter number">
	    <small id="numberHelp" class="form-text text-muted">
				We'll never share these contact details with anyone else.
			</small>
	  </div>

		<div class="mb-3">
			<a class="btn btn-outline-success" href="">Save</a>
			<a class="btn btn-success" href="">Save and Continue</a>
		</div>
	</form>
</div>

<?php include BASE_PATH . "views/footer.php";
