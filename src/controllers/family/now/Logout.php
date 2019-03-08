<?php

$sql = "SELECT `UID` FROM ((members INNER JOIN familyMembers ON
familyMembers.MemberID = members.MemberID) INNER JOIN familyIdentifiers ON
familyIdentifiers.ID = familyMembers.FamilyID) WHERE FamilyID = ? LIMIT 1";

try {
	$query = $db->prepare($sql);
	$query->execute([$id]);
} catch (PDOException $e) {
	halt(500);
}

$row = $query->fetchAll(PDO::FETCH_ASSOC);

if (!$row) {
	halt(404);
}

$_SESSION = array();
session_destroy();
header("Location: " . autoUrl("register/family/" . $id . "/" . $row[0]['UID']));
?>
