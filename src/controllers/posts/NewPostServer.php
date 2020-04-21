<?php

$db = app()->db;

$date = $_POST['date'];
if ($date == "") {
	$date = new DateTime('now', new DateTimeZone('UTC'));
} else {
	$date = new DateTime($_POST['date'], new DateTimeZone('UTC'));
}

$data = [
	$_SESSION['UserID'],
	$date->format("Y-m-d H:i:s"),
	$_POST['content'],
	$_POST['title'],
	$_POST['excerpt'],
	$_POST['path'],
	$_POST['type'],
	$_POST['mime']
];

try {
	$db->prepare("INSERT INTO `posts` (`Author`, `Date`, `Content`, `Title`, `Excerpt`, `Path`, `Type`, `MIME`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")->execute($data);
} catch (PDOException $e) {
	halt(500);
}

$id = $db->lastInsertId();

$_SESSION['PostStatus'] = "Successfully added";

header("Location: " . autoUrl("posts/" . $id));
