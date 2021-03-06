<?php

$name = getUserName($_SESSION['TENANT-' . app()->tenant->getId()]['UserID']);

$db = app()->db;

$acc_details = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile, EmailComms, MobileComms FROM users WHERE UserID = ?");
$acc_details->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);

$swimmers = $db->prepare("SELECT MForename, MMiddleNames, MSurname, DateOfBirth, SquadName, Gender, members.ASANumber, members.NGBCategory FROM members INNER JOIN squadMembers ON members.MemberID = squadMembers.Member INNER JOIN squads ON squadMembers.Squad = squads.SquadID WHERE members.UserID = ? ORDER BY MForename ASC, MSurname ASC");
$swimmers->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);

$emergency_contacts = $db->prepare("SELECT Name, ContactNumber FROM emergencyContacts WHERE UserID = ?");
$emergency_contacts->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);

$mandates = $db->prepare("SELECT BankName, AccountHolderName, AccountNumEnd, InUse FROM paymentMandates WHERE UserID = ?");
$mandates->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);

$logins = $db->prepare("SELECT `Time`, IPAddress, GeoLocation, Browser, Platform, Mobile FROM userLogins WHERE UserID = ? AND `Time` >= ?");
$logins->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID'], date("Y-m-d", strtotime('-120 days'))]);

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=GDpe-Data-Download-' . str_replace(' ', '-', $name) . '.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

fputcsv($output, array(app()->tenant->getKey('CLUB_NAME') . ' User Data Download'));
fputcsv($output, array('Data downloaded at ' . date("H:i, d/m/Y")));
fputcsv($output, ['-----']);

fputcsv($output, array('Account Details'));
fputcsv($output, array('Forename', 'Surname', 'Email Address', 'Phone', 'Email Allowed', 'SMS Allowed'));
$row = $acc_details->fetch(PDO::FETCH_NUM);
$row[3] = "+" . $row[3];
fputcsv($output, $row);
fputcsv($output, ['-----']);

fputcsv($output, array('Swimmers'));
fputcsv($output, array('First', 'Middle', 'Last', 'DOB', 'Squad', 'Sex', $tenant->getKey('NGB_NAME') . ' ID', $tenant->getKey('NGB_NAME') . ' Category (Internal ID)'));
$row = $swimmers->fetch(PDO::FETCH_NUM);
do {
  fputcsv($output, $row);
  $row = $swimmers->fetch(PDO::FETCH_NUM);
} while ($row != null);
fputcsv($output, ['Full details in your account']);
fputcsv($output, ['-----']);

fputcsv($output, array('Emergency Contacts'));
fputcsv($output, array('Name', 'Contact Number'));
$row = $emergency_contacts->fetch(PDO::FETCH_NUM);
do {
  fputcsv($output, $row);
  $row = $emergency_contacts->fetch(PDO::FETCH_NUM);
} while ($row != null);
fputcsv($output, ['-----']);

fputcsv($output, array('Direct Debit Mandates'));
fputcsv($output, array('Bank Name', 'Account Holder', 'Account Num Ends', 'In Use?'));
$row = $mandates->fetch(PDO::FETCH_NUM);
do {
  fputcsv($output, $row);
  $row = $mandates->fetch(PDO::FETCH_NUM);
} while ($row != null);
fputcsv($output, ['-----']);

fputcsv($output, array('Account Logins since ' . date("j M Y", strtotime('-120 days'))));
fputcsv($output, array('Time', 'IP', 'IP Location', 'Browser', 'Operating System', 'Mobile'));
$row = $logins->fetch(PDO::FETCH_NUM);
do {
  fputcsv($output, $row);
  $row = $logins->fetch(PDO::FETCH_NUM);
} while ($row != null);
fputcsv($output, ['-----']);

fputcsv($output, ['-- DOCUMENT GENERATED BY --']);
fputcsv($output, array(app()->tenant->getKey('CLUB_NAME') . ' GDPR Compliance Team', 'via Membership Software', 'by SCDS'));
