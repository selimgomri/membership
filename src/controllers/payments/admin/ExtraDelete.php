<?php

$id = mysqli_real_escape_string($link, $id);

$sql = "DELETE FROM `extras` WHERE `ExtraID` = '$id';";

if (!mysqli_query($link, $sql)) {
  halt(500);
}

header("Location: " . autoUrl("payments/extrafees"));
