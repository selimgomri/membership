<?php

global $db;

$getInfo = $db->prepare("SELECT members.UserID `uid`, Forename ufn, Surname usn, MForename mfn, MSurname msn, members.ASANumber asa, ASACategory cat, DateOfBirth dob, SquadName squad, SquadFee fee, ClubPays exempt, Mandate, BankName, AccountHolderName, AccountNumEnd FROM ((((members INNER JOIN squads ON members.SquadID = squads.SquadID) LEFT JOIN users ON members.UserID = users.UserID) LEFT JOIN paymentPreferredMandate ON users.UserID = paymentPreferredMandate.UserID) LEFT JOIN paymentMandates ON paymentPreferredMandate.MandateID = paymentMandates.MandateID) ORDER BY usn ASC, ufn ASC, users.UserID ASC, msn ASC, mfn ASC");
$getInfo->execute([]);

$logins = $db->prepare("SELECT `Time`, `IPAddress`, Browser, `Platform`, `GeoLocation` FROM userLogins WHERE UserID = ? ORDER BY `Time` DESC LIMIT 1");

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=membership-data-export.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, [
  'User forename',
  'User surname',
  'Member forename',
  'Member surname',
  'ASA number',
  'ASA cat.',
  'Date of birth',
  'Squad',
  'Squad fee',
  'Fee exemption',
  'Bank',
  'Bank a/c holder',
  'Bank a/c num end',
  'Last login',
]);
while ($info = $getInfo->fetch(PDO::FETCH_ASSOC)) {
  $exempt = 'Pays';
  if (bool($info['exempt'])) {
    $exempt = 'Fee exemption';
  }
  $logins->execute([$info['uid']]);
  $loginInfo = $logins->fetch(PDO::FETCH_ASSOC);
  $lastLogin = 'Never';
  if ($loginInfo) {
    $time = new DateTime($loginInfo['Time'], new DateTimeZone('UTC'));
    $time->setTimezone(new DateTimeZone('Europe/London'));
    $lastLogin = $time->format('H:i T \o\n d/m/y');
  }
  $dob = new DateTime($info['dob']);
  fputcsv($output, [
    $info['ufn'],
    $info['usn'],
    $info['mfn'],
    $info['msn'],
    $info['asa'],
    $info['cat'],
    $dob->format("d/m/Y"),
    $info['squad'],
    (string) \Brick\Math\BigDecimal::of((string) $info['fee'])->toScale(2),
    $exempt,
    getBankName($info['BankName']),
    $info['AccountHolderName'],
    $info['AccountNumEnd'],
    $lastLogin,
  ]);
}