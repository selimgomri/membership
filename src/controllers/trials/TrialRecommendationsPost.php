<?php

global $db;

$query = $db->prepare("SELECT COUNT(*) FROM joinSwimmers WHERE ID = ?");
$query->execute([$request]);

if ($query->fetchColumn() != 1) {
  halt(404);
}

$squad = $_POST['squad'];
if ($squad == "null") {
  $query = $db->prepare("UPDATE joinSwimmers SET Comments = ? WHERE ID = ?");
  $query->execute([htmlspecialchars(trim($_POST['comments'])), $request]);
} else {
  $query = $db->prepare("UPDATE joinSwimmers SET Comments = ?, SquadSuggestion = ? WHERE ID = ?");
  $query->execute([htmlspecialchars(trim($_POST['comments'])), $squad, $request]);
}

$_SESSION['TrialRecommendationsUpdated'] = true;
header("Location: " . currentUrl());
