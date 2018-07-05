<?

$user = $_SESSION['UserID'];

$contact = new EmergencyContact();
$contact->connect($link);
$contact->getByContactID($id);

if ($contact->getUserID() != $user) {
	halt(404);
}

$pagetitle = $contact->getName() . " - Emergency Contacts";

include BASE_PATH . 'views/header.php';

?>

<div class="container">
	<div class="mb-3 p-3 bg-white rounded box-shadow">
		<h1>
			<? echo $contact->getName(); ?>
		</h1>

		<form method="post">
		  <div class="form-group">
		    <label for="name">Name</label>
		    <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="<? echo $contact->getName();?>" required>
		  </div>
		  <div class="form-group">
		    <label for="num">Contact Number</label>
		    <input type="tel" class="form-control" id="num" name="num" placeholder="Phone" value="<? echo $contact->getContactNumber();?>" required>
		  </div>
		  <button type="submit" class="btn btn-success">Save</button>
			<a href="<? echo autoUrl("emergencycontacts/" . $id . "/delete"); ?>" class="btn btn-danger">Delete</a>
		</form>

	</div>
</div>

<?

include BASE_PATH . 'views/footer.php';
