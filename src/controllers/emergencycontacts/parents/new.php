<?

$pagetitle = "New Emergency Contact";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
	<div class="mb-3 p-3 bg-white rounded box-shadow">
		<h1>
			Add a new Emergency Contact
		</h1>

		<? if (isset($_SESSION['AddNewError'])) {
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

<?

include BASE_PATH . 'views/footer.php';
