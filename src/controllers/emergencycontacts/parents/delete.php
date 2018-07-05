<?

$user = $_SESSION['UserID'];

$contact = new EmergencyContact();
$contact->connect($link);
$contact->getByContactID($id);

if ($contact->getUserID() != $user) {
	halt(404);
}

$contact->delete();

header("Location: " . autoUrl("emergencycontacts"));
