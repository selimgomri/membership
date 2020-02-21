<?php

global $db;

$getInfo = $db->prepare("SELECT MForename, MSurname, SquadName, DateOfBirth, Website, Social, Noticeboard, FilmTraining, ProPhoto FROM ((members INNER JOIN squads ON squads.SquadID = members.SquadID) LEFT JOIN memberPhotography ON members.MemberID = memberPhotography.MemberID) ORDER BY SquadFee DESC, SquadName ASC, MSurname ASC, MSurname ASC;");
$getInfo->execute([]);

$now = new DateTime('now', new DateTimeZone('Europe/London'));

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=photography-permissions-export.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, [
  'Forename',
  'Surname',
  'Squad',
  'Website',
  'Social',
  'Noticeboard.',
  'Filming for training',
  'Pro Photography',
]);
while ($info = $getInfo->fetch(PDO::FETCH_ASSOC)) {
  $dob = new DateTime($info['DateOfBirth'], new DateTimeZone('Europe/London'));
  $age = (int) $dob->diff($now)->format("%Y");

  $web = $social = $noticeboard = $filmTraining = $proPhoto = "No";
  if ($age >= 18) {
    $web = $social = $noticeboard = $filmTraining = $proPhoto = "N/A";
  }

  if (bool($info['Website'])) {
    $web = 'Yes';
  }

  if (bool($info['Social'])) {
    $social = 'Yes';
  }

  if (bool($info['Noticeboard'])) {
    $noticeboard = 'Yes';
  }

  if (bool($info['FilmTraining'])) {
    $filmTraining = 'Yes';
  }

  if (bool($info['ProPhoto'])) {
    $proPhoto = 'Yes';
  }


  fputcsv($output, [
    $info['MForename'],
    $info['MSurname'],
    $info['SquadName'],
    $web,
    $social,
    $noticeboard,
    $filmTraining,
    $proPhoto,
  ]);
}