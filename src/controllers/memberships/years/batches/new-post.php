<?php


$db = app()->db;
$tenant = app()->tenant;

$getYear = $db->prepare("SELECT `Name`, `StartDate`, `EndDate` FROM `membershipYear` WHERE `ID` = ? AND `Tenant` = ?");
$getYear->execute([
  $id,
  $tenant->getId(),
]);
$year = $getYear->fetch(PDO::FETCH_ASSOC);

if (!$year) halt(404);

if (!isset($_GET['user'])) halt(404);

// Check user exists
$userInfo = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile, RR FROM users WHERE Tenant = ? AND UserID = ? AND Active");
$userInfo->execute([
  $tenant->getId(),
  $_GET['user']
]);

$info = $userInfo->fetch(PDO::FETCH_ASSOC);

if ($info == null) {
  halt(404);
}

$getMembers = $db->prepare("SELECT MemberID id, MForename fn, MSurname sn, NGBCategory ngb, ngbMembership.Name ngbName, ngbMembership.Fees ngbFees, ClubCategory club, clubMembership.Name clubName, clubMembership.Fees clubFees FROM members INNER JOIN clubMembershipClasses AS ngbMembership ON ngbMembership.ID = members.NGBCategory INNER JOIN clubMembershipClasses AS clubMembership ON clubMembership.ID = members.ClubCategory WHERE Active AND UserID = ? ORDER BY fn ASC, sn ASC");
$getMembers->execute([
  $_GET['user']
]);

$getCurrentMemberships = $db->prepare("SELECT `Name` `name`, `Description` `description`, `Type` `type`, `memberships`.`Amount` `paid`, `clubMembershipClasses`.`Fees` `expectPaid` FROM `memberships` INNER JOIN clubMembershipClasses ON memberships.Membership = clubMembershipClasses.ID WHERE `Member` = ? AND `Year` = ?");
$hasMembership = $db->prepare("SELECT COUNT(*) FROM memberships WHERE `Member` = ? AND `Year` = ? AND `Membership` = ?");

while ($member = $getMembers->fetch(PDO::FETCH_OBJ)) {
  $hasMembership->execute([
    $member->id,
    $id,
    $member->ngb,
  ]);
  $has = $hasMembership->fetchColumn() > 0;

  if (!$has && isset($_POST[$member->id . '-' . $member->ngb . '-yes'])) {
    
  }

  $hasMembership->execute([
    $member->id,
    $id,
    $member->club,
  ]);
  $has = $hasMembership->fetchColumn() > 0;

  if (!$has && isset($_POST[$member->id . '-' . $member->club . '-yes'])) {

  }

}