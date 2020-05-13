<?php

$db = app()->db;
$tenant = app()->tenant;

$query = null;
if ($int) {
	$query = $db->prepare("SELECT COUNT(*) FROM `posts` WHERE `ID` = ? AND Tenant = ?");
	$query->execute([
		$id,
		$tenant->getId()
	]);
} else {
	$query = $db->prepare("SELECT COUNT(*) FROM `posts` WHERE `Path` = ? AND Tenant = ?");
	$query->execute([
		$id,
		$tenant->getId()
	]);
}

if ($query->fetchColumn == 0) {
	halt(404);
}

$data = [
	$_POST['content'],
	$_POST['title'],
	$_POST['excerpt'],
	$_POST['path'],
	$_POST['type'],
	$_POST['mime']
];

$sql = null;
if ($int) {
	$sql = "UPDATE `posts` SET `Content` = ?, `Title` = ?, `Excerpt` = ?, `Path` = ?, `Type` = ?, `MIME` = ? WHERE `ID` = ?";
} else {
	$sql = "UPDATE `posts` SET `Content` = ?, `Title` = ?, `Excerpt` = ?, `Path` = ?, `Type` = ?, `MIME` = ? WHERE `Path` = ?";
}

$data[] = $id;

try {
	$db->prepare($sql)->execute($data);
} catch (PDOException $e) {
	halt(500);
}


$_SESSION['PostStatus'] = "Successfully updated";

header("Location: " . autoUrl("posts/" . $id));
