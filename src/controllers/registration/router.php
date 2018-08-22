<?

if ($_SESSION['AccessLevel'] == "Parent") {
	$this->get('/', function() {
		global $link;

		include 'newfamily/Welcome.php';
	});
} else {
	$this->get('/', function() {
		global $link;

		$id = 0;
		include BASE_PATH . 'controllers/renewal/admin/list.php';
	});
}
