<?php

global $db;

$_SESSION['SelectedAccessLevel'] = $_GET['type'];

$userObject = new \User($_SESSION['UserID'], $db);

header("location: " . autoUrl(""));