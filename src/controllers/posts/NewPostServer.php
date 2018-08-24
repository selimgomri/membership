<?

global $db;

$date = $_POST['date'];
if ($date == "") {
	$date = date("Y-m-d H:s:i");
}

$data = [
	$_SESSION['UserID'],
	$date,
	$_POST['content'],
	$_POST['title'],
	$_POST['excerpt'],
	$_POST['path'],
	$_POST['type'],
	$_POST['mime']
];

$sql = "INSERT INTO `posts` (`Author`, `Date`, `Content`, `Title`, `Excerpt`, `Path`, `Type`, `MIME`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
try {
	$db->prepare($sql)->execute($data);
} catch (PDOException $e) {
	halt(500);

}

$id = $db->lastInsertId();

$_SESSION['PostStatus'] = "Successfully added";

header("Location: " . autoUrl("posts/" . $id));
