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

if ($people) {
  $data = [
    $_SESSION['UserID'],
    $date,
  	$_POST['content'],
  	getUserName($_SESSION['UserID']),
  	$_POST['excerpt'],
  	strtolower(str_replace(' ', '', getUserName($_SESSION['UserID']))),
  	'people_pages',
  	'text/html'
  ];
}

$sql = "INSERT INTO `posts` (`Author`, `Date`, `Content`, `Title`, `Excerpt`, `Path`, `Type`, `MIME`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
try {
	$db->prepare($sql)->execute($data);
} catch (PDOException $e) {
	halt(500);

}

$id = $db->lastInsertId();

$_SESSION['PostStatus'] = "Successfully added";

if ($people) {
  header("Location: " . autoUrl("people/" . strtolower(str_replace(' ', '', getUserName($_SESSION['UserID'])))));
} else {
  header("Location: " . autoUrl("posts/" . $id));
}
