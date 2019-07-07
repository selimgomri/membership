<?php

$user = $_SESSION['AssRegUser'];
global $db;

$swimmers = $db->query("SELECT MForename `first`, MSurname `last`, SquadName `name`, MemberID `id` FROM members INNER JOIN squads ON members.SquadID = squads.SquadID WHERE members.UserID IS NULL ORDER BY MemberID DESC, `first` ASC, `last` ASC");

$setParent = $db->prepare("UPDATE members SET UserID = ?, RR = ? WHERE MemberID = ?");

$user = $db->prepare("SELECT Forename `first`, Surname `last`, EmailAddress `email` FROM users WHERE UserID = ?");
$user->execute([$_SESSION['AssRegUser']]);
$user = $user->fetch(PDO::FETCH_ASSOC);

if ($user == null) {
  halt(404);
}

$selectedSwimmers = [];

$success = false;

while ($swimmer = $swimmers->fetch(PDO::FETCH_ASSOC)) {
  if ($_POST['member-' . $swimmer['id']]) {
    $selectedSwimmers[] = $swimmer['id'];
    $setParent->execute([
      $_SESSION['AssRegUser'],
      1,
      $swimmer['id']
    ]);
    $success = true;
  }
}

if ($success) {
  // Go to next page
  $subject = "Complete your registration at " . env('CLUB_NAME');
  $message = "<p>Hello " . htmlspecialchars("name") . ", </p>";
  $message .= "<p>We've pre-registered you for a " . htmlspecialchars(env('CLUB_NAME')) . " account. To continue, <a href=\"" . autoUrl("assisted-registration/" . $_SESSION['AssRegUser'] . "/" . $_SESSION['AssRegPass']) . "\">please follow this link</a></p>";
  $message .= "As part of the registration process, we'll ask you to set a password and let us know your communication preferences.";

  notifySend(null, $subject, $message, $user['first'] . ' ' . $user['last'], $user['email']);

  $_SESSION['AssRegComplete'] = true;
  $_SESSION['AssRegName'] = $user['first'] . ' ' . $user['last'];
  header("Location: " . autoUrl("assisted-registration/complete"));
} else {
  header("Location: " . currentUrl());
}