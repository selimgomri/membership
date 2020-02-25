<?php

if (!SCDS\CSRF::verify()) {
  halt(403);
}

global $db;
use Respect\Validation\Validator as v;

$userID = $_SESSION['UserID'];
$forenameUpdate = false;
$middlenameUpdate = false;
$surnameUpdate = false;
$dateOfBirthUpdate = false;
$sexUpdate = false;
$otherNotesUpdate = false;
$photoUpdate = false;
$update = false;
$successInformation = "";

$getDetails = $db->prepare("SELECT * FROM members WHERE MemberID = ? and UserID = ?");
$getDetails->execute([$id, $_SESSION['UserID']]);
$row = $getDetails->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
  halt(404);
}

try {

  $forename = $row['MForename'];
  $middlename = $row['MMiddleNames'];
  $surname = $row['MSurname'];
  $dateOfBirth = $row['DateOfBirth'];
  $sex = $row['Gender'];
  $otherNotes = $row['OtherNotes'];

  if (!empty($_POST['forename'])) {
    $newForename = trim(mb_ucfirst($_POST['forename']));

    if ($newForename != $forename) {
      $update = $db->prepare("UPDATE `members` SET `MForename` = ? WHERE `MemberID` = ?");
      $update->execute([$newForename, $id]);
      $forenameUpdate = true;
      $update = true;
    }
  }
  if (isset($_POST['middlenames'])) {
    $newMiddlenames = trim(mb_ucfirst($_POST['middlenames']));
    if ($newMiddlenames != $middlename) {
      $update = $db->prepare("UPDATE `members` SET `MMiddleNames` = ? WHERE `MemberID` = ?");
      $update->execute([$newMiddlenames, $id]);
      $middlenameUpdate = true;
      $update = true;
    }
  }
  if (!empty($_POST['surname'])) {
    $newSurname = trim(mb_ucfirst($_POST['surname']));
    if ($newSurname != $surname) {
      $update = $db->prepare("UPDATE `members` SET `MSurname` = ? WHERE `MemberID` = ?");
      $update->execute([$newSurname, $id]);
      $surnameUpdate = true;
      $update = true;
    }
  }
  if (!empty($_POST['datebirth'])) {
    $newDateOfBirth = trim(mb_ucfirst($_POST['datebirth']));
    if ($newDateOfBirth != $dateOfBirth && v::date()->validate($newDateOfBirth)) {
      $update = $db->prepare("UPDATE `members` SET `DateOfBirth` = ? WHERE `MemberID` = ?");
      $update->execute([$newDateOfBirth, $id]);
      $dateOfBirthUpdate = true;
      $update = true;
    }
  }
  if (!empty($_POST['sex'])) {
    $newSex = trim(mb_ucfirst($_POST['sex']));
    if ($newSex != $sex) {
      $update = $db->prepare("UPDATE `members` SET `Gender` = ? WHERE `MemberID` = ?");
      $update->execute([$newSex, $id]);
      $sexUpdate = true;
      $update = true;
    }
  }
  if (isset($_POST['otherNotes'])) {
    $newOtherNotes = trim(ucfirst($_POST['otherNotes']));
    if ($newOtherNotes != $otherNotes) {
      $update = $db->prepare("UPDATE `members` SET `OtherNotes` = ? WHERE `MemberID` = ?");
      $update->execute([$newOtherNotes, $id]);
      $otherNotesUpdate = true;
      $update = true;
    }
  }
  if ((!empty($_POST['disconnect'])) && (!empty($_POST['disconnectKey']))) {
    $disconnect = trim($_POST['disconnect']);
    $disconnectKey = trim($_POST['disconnectKey']);
    if ($disconnect == $disconnectKey) {
      $newKey = generateRandomString(8);
      $update = $db->prepare("UPDATE `members` SET `UserID` = NULL, AccessKey = ? WHERE `MemberID` = ?");
      $update->execute([$newKey, $id]);
      header("Location: " . autoUrl("members"));
    }
  }
  if (!empty($_POST['swimmerDeleteDanger'])) {
    $deleteKey = trim($_POST['swimmerDeleteDanger']);
    if ($deleteKey == $dbAccessKey) {
      $update = $db->prepare("DELETE FROM `members` WHERE members.MemberID = ?");
      $update->execute([$id]);
      header("Location: " . autoUrl("members"));
    }
  }
  if (isset($_POST['webPhoto']) || isset($_POST['socPhoto']) || isset($_POST['noticePhoto']) || isset($_POST['trainFilm']) || isset($_POST['webPhoto'])) {
    setupPhotoPermissions($id);
  }
  // Web Photo Permissions
  $photo[0] = 1;
  if (!isset($_POST['webPhoto']) || $_POST['webPhoto'] != 1) {
    $photo[0] = 0;
  }
  $update = $db->prepare("UPDATE `memberPhotography` SET `Website` = ? WHERE `MemberID` = ?");
  $update->execute([$photo[0], $id]);
  $photoUpdate = true;
  $update = true;

  // Social Media Photo Permissions
  $photo[1] = 1;
  if (!isset($_POST['socPhoto']) || $_POST['socPhoto'] != 1) {
    $photo[1] = 0;
  }
  $update = $db->prepare("UPDATE `memberPhotography` SET `Social` = ? WHERE `MemberID` = ?");
  $update->execute([$photo[1], $id]);
  $photoUpdate = true;
  $update = true;

  // Notice Board Photo Permissions
  $photo[2] = 1;
  if (!isset($_POST['noticePhoto']) || $_POST['noticePhoto'] != 1) {
    $photo[2] = 0;
  }
  $update = $db->prepare("UPDATE `memberPhotography` SET `Noticeboard` = ? WHERE `MemberID` = ?");
  $update->execute([$photo[2], $id]);
  $photoUpdate = true;
  $update = true;

  // Filming in Training Permissions
  $photo[3] = 1;
  if (!isset($_POST['trainFilm']) || $_POST['trainFilm'] != 1) {
    $photo[3] = 0;
  }
  $update = $db->prepare("UPDATE `memberPhotography` SET `FilmTraining` = ? WHERE `MemberID` = ?");
  $update->execute([$photo[3], $id]);
  $photoUpdate = true;
  $update = true;

  // Pro Photographer Photo Permissions
  $photo[4] = 1;
  if (!isset($_POST['proPhoto']) || $_POST['proPhoto'] != 1) {
    $photo[4] = 0;
  }
  $update = $db->prepare("UPDATE `memberPhotography` SET `ProPhoto` = ? WHERE `MemberID` = ?");
  $update->execute([$photo[4], $id]);
  $photoUpdate = true;
  $update = true;

  if (isset($_POST['country'])) {

    if ($row['Country'] != $_POST['country'] && isset($countries[$_POST['country']])) {
      // Update
      $updateCountry = $db->prepare("UPDATE members SET Country = ? WHERE MemberID = ?");
      $updateCountry->execute([
        $_POST['country'],
        $id
      ]);
      $countryUpdate = true;
      $update = true;
    }
  
  }

  $_SESSION['SwimmerSaved'] = true;

} catch (Exception $e) {

  $_SESSION['SwimmerNotSaved'] = true;

}

header("Location: " . currentUrl());
