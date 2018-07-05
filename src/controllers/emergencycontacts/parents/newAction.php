<?

$user = $_SESSION['UserID'];

$contact = new EmergencyContact();
$contact->connect($link);

if ($_POST['name'] != null && $_POST['name'] != "" && $_POST['num'] != null && $_POST['num'] != "") {
	$contact->new($_POST['name'], $_POST['num'], $user);
	$contact->add();

	$_SESSION['AddNewSuccess'] = '
	<div class="alert alert-success">
		<p class="mb-0">
			<strong>
				Emergency Contact added successfully
			</strong>
		</p>
	</div>
	';

	header("Location: " . autoUrl("emergencycontacts"));
} else {
	$_SESSION['AddNewError'] = '
	<div class="alert alert-warning">
		<p class="mb-0">
			<strong>
				We were unable to add the contact
			</strong>
		</p>
	</div>
	';

	header("Location: " . app('request')->curl);
}
