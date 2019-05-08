<?

global $db;

if ($_SESSION['AccessLevel'] == "Parent") {
  $getMed = $db->prepare("SELECT MForename, MSurname, Conditions, Allergies,
  Medication FROM `members` LEFT JOIN `memberMedical` ON members.MemberID =
  memberMedical.MemberID WHERE members.MemberID = ? AND members.UserID = ?");
  $getMed->execute([$id, $_SESSION['UserID']]);
} else {
  $getMed = $db->prepare("SELECT MForename, MSurname, Conditions, Allergies,
  Medication FROM `members` LEFT JOIN `memberMedical` ON members.MemberID =
  memberMedical.MemberID WHERE members.MemberID = ?");
  $getMed->execute([$id]);
}

$row = $getMed->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
  halt(404);
}

setupMedicalInfo($id);

$conditions = $allergies = $medicine = "";

if ($_POST['medConDis'] == 1) {
	$conditions = ucfirst(trim($_POST['medConDisDetails']));
}

if ($_POST['allergies'] == 1) {
	$allergies = ucfirst(trim($_POST['allergiesDetails']));
}

if ($_POST['medicine'] == 1) {
	$medicine = ucfirst(trim($_POST['medicineDetails']));
}

try {
  $update = $db->prepare("UPDATE `memberMedical` SET `Conditions` = ?,
  `Allergies` = ?, `Medication` = ? WHERE `MemberID` = ?");
  $update->execute([
    $conditions,
    $allergies,
    $medicine,
    $id
  ]);
	header("Location: " . app('request')->curl);
} catch (Exception $e) {
	$_SESSION['ErrorState'] = "
	<div class=\"alert alert-danger\">
	<strong>An error occured when we tried to update our records</strong>
	<p class=\"mb-0\">Please try again. Your membership renewal will not be
	affected by this error.</p></div>";
	header("Location: " . app('request')->curl);
}
