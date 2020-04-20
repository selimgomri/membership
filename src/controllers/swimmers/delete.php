<?php

global $db;

$db->beginTransaction();

$responseCode = 200;
$responseData = [
  'status' => $responseCode,
  'message' => ''
];

try {

  if (empty($_POST['member']) || empty($_POST['password'])) {
    throw new Exception('Required details were not provided.');
  }

  // Get info for member we're deleting
  $getDeleteMember = $db->prepare("SELECT MForename Forname, MSurname Surname FROM members WHERE MemberID = ?");
  $getDeleteMember->execute([
    $_POST['member']
  ]);
  $deleteMember = $getDeleteMember->fetch(PDO::FETCH_ASSOC);

  if ($deleteMember == null) {
    throw new Exception('No such member exists, so we cannot delete the member.');
  }

  // Get user info to verify password
  $getCurrentUser = $db->prepare("SELECT `Password`, Forename, Surname, EmailAddress FROM users WHERE UserID = ?");
  $getCurrentUser->execute([
    $_SESSION['UserID']
  ]);
  $currentUser = $getCurrentUser->fetch(PDO::FETCH_ASSOC);

  if ($currentUser == null) {
    throw new Exception('Current user does not exist. This means an unknown error occurred.');
  }

  if (!password_verify($_POST['password'], $currentUser['Password'])) {
    throw new Exception('The password provided was incorrect.');
  }

  // Delete things

  // Delete extra subscriptions
  $delete = $db->prepare("DELETE FROM extrasRelations WHERE MemberID = ?");
  $delete->execute([
    $_POST['member']
  ]);

  // Delete meet results?
  $delete = $db->prepare("DELETE FROM meetResults WHERE Member = ?");
  $delete->execute([
    $_POST['member']
  ]);

  // Delete member email addresses
  $delete = $db->prepare("DELETE FROM memberEmailAddresses WHERE Member = ?");
  $delete->execute([
    $_POST['member']
  ]);

  // Delete medical notes
  $delete = $db->prepare("DELETE FROM memberMedical WHERE MemberID = ?");
  $delete->execute([
    $_POST['member']
  ]);

  // Delete photography permissions
  $delete = $db->prepare("DELETE FROM memberPhotography WHERE MemberID = ?");
  $delete->execute([
    $_POST['member']
  ]);

  // Delete moves
  $delete = $db->prepare("DELETE FROM moves WHERE MemberID = ?");
  $delete->execute([
    $_POST['member']
  ]);

  // Delete targeted list memberships
  $delete = $db->prepare("DELETE FROM targetedListMembers WHERE ReferenceType = ? AND ReferenceID = ?");
  $delete->execute([
    'Member',
    $_POST['member']
  ]);

  // Delete times
  $delete = $db->prepare("DELETE FROM `times` WHERE MemberID = ?");
  $delete->execute([
    $_POST['member']
  ]);

  // Delete times individual
  $delete = $db->prepare("DELETE FROM timesIndividual WHERE MemberID = ?");
  $delete->execute([
    $_POST['member']
  ]);

  // Deactivate member
  $delete = $db->prepare("UPDATE members SET Active = 0, SquadID = NULL, UserID = NULL WHERE MemberID = ?");
  $delete->execute([
    $_POST['member']
  ]);

  // Commit
  $db->commit();

  // Send emails
  // TODO

  $responseData['status'] = 200;
  $responseData['message'] = $deleteMember['Forename'] . ' has been deleted successfully.';

} catch (PDOException $e) {
  $responseData['status'] = 500;
  $responseData['message'] = 'A database error occurred. All changes have been rolled back.';
  $db->rollBack();
} catch (Exception $e) {
  $responseData['status'] = 500;
  $responseData['message'] = $e->getMessage();
  $db->rollBack();
} finally {
  http_response_code(200);
  echo json_encode($responseData);
}