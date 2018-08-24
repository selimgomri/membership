<?

global $db;

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

header("Location: " . app('request')->curl);
