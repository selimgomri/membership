<?php

$db = app()->db;

$newAccess = $_POST['accountType'];

if ($newAccess != "Admin" && $newAccess != "Coach" && $newAccess != "Galas" && $newAccess != "Parent") {
	$newAccess = "Parent";
}

$query = $db->prepare("UPDATE `users` SET AccessLevel = ? WHERE UserID = ?");
try {
  $query->execute([$newAccess, $id]);
  ?><span class="text-success pt-3">Successfully updated account type to <?=mb_strtolower($newAccess)?></span><?php
} catch (Exception $e) {
  ?><span class="text-danger mt-2">Failed to update account type</span><?php
}
