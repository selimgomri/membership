<?php

$db = app()->db;

$getUser = $db->prepare("SELECT UserID, Forename, Surname, EmailAddress, Mobile, `Password` FROM users WHERE UserID = ?");
$getUser->execute([$id]);
$user = $getUser->fetch(PDO::FETCH_ASSOC);

if (!(password_verify($password, $user['Password']))) {
  halt(404);
}

$_SESSION['AssRegGuestUser'] = $id;
$_SESSION['AssRegStage'] = 1;

header("Location: " . autoUrl("assisted-registration/get-started"));