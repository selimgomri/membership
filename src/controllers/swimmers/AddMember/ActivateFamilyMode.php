<?

global $db;

if (isset($_SESSION['Swimmers-FamilyMode'])) {
	header("Location: " . autoUrl("swimmers/new"));
	die();
} else {
	$uid = md5(generateRandomString(20) . time());
	$sql = "INSERT INTO familyIdentifiers (UID, ACS) VALUES (?, ?)";
	try {
		$db->prepare($sql)->execute([$uid, generateRandomString(6)]);
	} catch (PDOException $e) {
		halt(500);
	}

	$id = $db->lastInsertId();

	$_SESSION['Swimmers-FamilyMode'] = [
		"FamilyMode" 	=> true,
		"FamilyId"		=> $id
	];
}

header("Location: " . autoUrl("swimmers/new"));
