<?php

global $db;
try {
  $delete = $db->prepare("DELETE FROM extras WHERE ExtraID = ?");
  $delete->execute([$id]);
  header("Location: " . autoUrl("payments/extrafees"));
} catch (Exception $e) {
  halt(500);
}
