<?php

$db = app()->db;

$sql = $db->prepare("SELECT COUNT(*) FROM `targetedLists` WHERE `ID` = ?;");
$sql->execute([$id]);
if ($sql->fetchColumn() == 0) {
  halt(404);
}

$sql = $db->prepare("DELETE FROM `targetedLists` WHERE `ID` = ?;");
$sql->execute([$id]);

header("Location: " . autoUrl("notify/lists"));
