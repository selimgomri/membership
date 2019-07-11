<?php

try {
  global $db;
  $delete = $db->prepare("DELETE FROM galaSessions WHERE Gala = ? AND ID = ?");
  $delete->execute([$id, $session]);
  header("Location: " . autoUrl("galas/" . $id . "/sessions"));
} catch (Exception $e) {
  halt(404);
}