<?php

/*
 * Set up the user's account for them and then automatically transition
 * to a logged in state
 */

// First check if an account for this email already exists

global $db;

try {

  $checkExists = $db->prepare("SELECT COUNT(*) FROM users WHERE EmailAddress = ?");
  $checkExists->execute([$_SESSION['AC-UserDetails']['email-addr']]);

  $userID = null;

  if ($checkExists->fetchColumn() == 0) {
    // Create a new account
    try {
      $addUser = $db->prepare("INSERT INTO users (Password, AccessLevel, EmailAddress, EmailComms, Forename, Surname, Mobile, MobileComms, RR) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $addUser->execute([
        $_SESSION['AC-UserDetails']['password-hash'],
        'Parent',
        $_SESSION['AC-UserDetails']['email-addr'],
        $_SESSION['AC-UserDetails']['allow-email'],
        $_SESSION['AC-UserDetails']['forename'],
        $_SESSION['AC-UserDetails']['surname'],
        $_SESSION['AC-UserDetails']['mobile'],
        $_SESSION['AC-UserDetails']['allow-sms'],
        false
      ]);

      $userID = $db->lastInsertId();
    } catch (Exception $e) {
      halt(500);
    }
  } else {
    $getUser = $db->prepare("SELECT UserID FROM users WHERE EmailAddress = ?");
    $getUser->execute([$_SESSION['AC-UserDetails']['email-addr']]);
    $userID = $getUser->fetchColumn();
  }

  // Get swimmers from trial request
  $getSwimmers = $db->prepare("SELECT First, Last, DoB, SquadRecommendation, Sex FROM joinSwimmers WHERE Parent = ? AND SquadRecommendation IS NOT NULL");
  $getSwimmers->execute([$_SESSION['AC-Registration']['Hash']]);

  $swimmers = $getSwimmers->fetchAll(PDO::FETCH_ASSOC);

  // Add the swimmers in suspended state
  $insertSwimmer = $db->prepare("INSERT INTO members (SquadID, UserID, Status, RR, MForename, MSurname, ASANumber, ASACategory, ClubPays, DateOfBirth, Gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  // For each swimmer, insert to database in suspended state, connected to this user.
  foreach ($swimmers as $swimmer) {
    $asa = null;
    if ($swimmer['ASA'] == "" || $swimmer['ASA'] == null) {
      $asa = $swimmer['ASA'];
    }

    $insertSwimmer->execute([
      $swimmer['SquadRecommendation'],
      $userID,
      false,
      true,
      $swimmer['First'],
      $swimmer['Last'],
      $asa,
      $asa_cat,
      false,
      $swimmer['DoB'],
      $swimmer['Sex']
    ]);
  }
} catch (Exception $e) {
  halt(500);
}

// Go to the medical form for swimmers who are RR
$_SESSION['AC-Registration']['Stage'] = 'MedicalForm';
header("Location: " . autoUrl("register/ac/medical-form"));
