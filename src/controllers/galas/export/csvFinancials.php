<?php
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=accessKeys.csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('Forename', 'Surname', 'Squad' , 'ASA Number' , 'Number of Swims' , 'Fee' , 'PaymentType'));

// fetch the data
$sql = "SELECT members.MForename, members.MSurname, squads.SquadName, members.ASANumber, galaEntries.NumberSwims, galaEntries.Fee, , galaEntries.PaymentTypeID FROM (galaEntries (INNER JOIN members ON galaEntries.MemberID = members.MemberID)) ORDER BY `members`.`MForename` , `members`.`MSurname` ASC;";
$rows = mysqli_query($link, $sql);

// loop over the rows, outputting them
while ($row = mysqli_fetch_assoc($rows)) fputcsv($output, $row);
?>
