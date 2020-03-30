<?php

$fluidContainer = true;

global $db;
global $currentUser;

$perms = $currentUser->getPrintPermissions();
$default = $currentUser->getUserOption('DefaultAccessLevel');

foreach ($perms as $key => $value) {
  if ($_POST['selector'] == $key) {
    $currentUser->setUserOption('DefaultAccessLevel', $key);
    $_SESSION['SavedChanges'] = true;
    break;
  }
}

header("location: " . autoUrl("my-account/default-access-level"));