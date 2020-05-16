<?php

$user = $_SESSION['AssRegUser'];
$db = app()->db;

$swimmers = $db->query("SELECT MForename `first`, MSurname `last`, SquadName `name`, MemberID `id` FROM members INNER JOIN squads ON members.SquadID = squads.SquadID WHERE members.UserID IS NULL ORDER BY MemberID DESC, `first` ASC, `last` ASC");

$setParent = $db->prepare("UPDATE members SET UserID = ?, RR = ? WHERE MemberID = ?");

$setUserRequiresRenewal = $db->prepare("UPDATE users SET RR = ? WHERE UserID = ?");
$setUserRequiresRenewal->execute([
  1,
  $_SESSION['AssRegUser']
]);

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
  $message = "<p>Hello " . htmlspecialchars($user['first']) . ", </p>";
  if (isset($_SESSION['AssRegExisting']) && $_SESSION['AssRegExisting']) {
    $message .= "<p>We've added a new swimmer to your " . htmlspecialchars(env('CLUB_NAME')) . " account. We now need you to <a href=\"" . autoUrl("") . "\">sign in and provide some information</a>.</p>";
    unset($_SESSION['AssRegExisting']);
  } else {
    $message .= "<p>We've created an account for you in our membership system. We use the system to keep track of all our members, information, gala entries, payments and more.</p>";
    $message .= "<p>To continue, <a href=\"" . autoUrl("assisted-registration/" . $_SESSION['AssRegUser'] . "/" . $_SESSION['AssRegPass']) . "\">please follow this link</a></p>";
    $message .= "<p>As part of the registration process, we'll ask you to set a password, let us know your communication preferences and fill in important information about you and/or your members. At the end, we'll set up a direct debit so that payments to " . htmlspecialchars(env('CLUB_NAME')) . " are taken automatically.</p>";
    $message .= "<p>You'll also be given the opportunity to set up a direct debit.</p>";
  }
  if (!bool(env('IS_CLS'))) {
    $message .= '<p>Please note that your club may not provide all services included in the membership software.</p>';
  }

  notifySend(null, $subject, $message, $user['first'] . ' ' . $user['last'], $user['email']);

  $_SESSION['AssRegComplete'] = true;
  $_SESSION['AssRegName'] = $user['first'] . ' ' . $user['last'];
  header("Location: " . autoUrl("assisted-registration/complete"));
} else {
  header("Location: " . autoUrl("assisted-registration/select-swimmers"));
}