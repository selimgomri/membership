<?php

$db = app()->db;

setUserOption($_SESSION['UserID'], 'DefaultAccessLevel', $_GET['type']);
// $_SESSION['SelectedAccessLevel'] = $_GET['type'];

$userObject = new \User($_SESSION['UserID'], $db);

header("location: " . autoUrl(""));