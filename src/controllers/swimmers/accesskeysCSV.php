<?php

$db = app()->db;
$tenant = app()->tenant;

// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=access-keys.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('Forename', 'Surname', 'Squad' , 'Swim England Number' , 'Access Key'));

// fetch the data
$swimmers = $db->prepare("SELECT members.MForename, members.MSurname, squads.SquadName, members.ASANumber, members.AccessKey FROM (members INNER JOIN squads ON members.SquadID = squads.SquadID) WHERE members.Tenant = ? ORDER BY `members`.`MForename` , `members`.`MSurname` ASC");
$swimmers->execute([
  $tenant->getId()
]);

// loop over the rows, outputting them
while ($row = $swimmers->fetch(PDO::FETCH_ASSOC)) {
  fputcsv($output, $row);
}
?>
