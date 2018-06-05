<?php

$userID = $_SESSION['UserID'];
$access = $_SESSION['AccessLevel'];

if ($access == "Committee" || $access == "Admin") {

	$this->any('/', function() {
		global $link;
		require('index.php');
	});

}
