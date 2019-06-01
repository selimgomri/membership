<?php

$id = mysqli_real_escape_string($link, $id);

$sql = "SELECT * FROM galas WHERE galas.GalaID = '$id';";
$info = mysqli_query($link, $sql);

$noTimeSheet = false;

if (mysqli_num_rows($info) == 0) {
  halt(404);
	$noTimeSheet = true;
}

$info = mysqli_fetch_array($info, MYSQLI_ASSOC);

$sql = null;

if ($_SESSION['AccessLevel'] == "Parent") {
  $uid = mysqli_real_escape_string($link, $_SESSION['UserID']);

  $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID =
  members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE
  galas.GalaID = '$id' AND members.UserID = '$uid' ORDER BY members.MForename ASC,
  members.MSurname ASC;";
} else {
  $sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID =
  members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE
  galas.GalaID = '$id' ORDER BY members.MForename ASC, members.MSurname ASC;";
}

$result = mysqli_query($link, $sql);

if (mysqli_num_rows($result) == 0) {
	$noTimeSheet = true;
}

if ($noTimeSheet) {
  $pagetitle = "Galas";
  include BASE_PATH . "views/header.php";
  include "galaMenu.php"; ?>
  <div class="container">
    <h1>There is no Time Sheet available for the gala you requested</h1>
    <?php if ($_SESSION['AccessLevel'] == "Parent") {
      ?><p class="lead">This may be because your swimmers have not entered this gala.</p>
		<?php } else {
      ?><p class="lead">There are no entries yet for this gala.</p>
		<?php } ?>
  </div>
  <?php
  include BASE_PATH . "views/footer.php";
} else {
	// output headers so that the file is downloaded rather than displayed
  header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=' . str_replace(' ', '', $info['GalaName']) . '-TimeSheet.csv');

	// create a file pointer connected to the output stream
  $output = fopen('php://output', 'w');

  fputcsv($output, array(CLUB_NAME . ' Gala Time Sheet'));
  fputcsv($output, array($info['GalaName'] . " - " . $info['GalaVenue'] . " - " . date("d/m/Y", strtotime($info['GalaDate']))));
  fputcsv($output, array('Time Sheet Report Generated on ' . date("d/m/Y, H:i")));
	fputcsv($output, array(''));

  $swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Back','100Back','200Back','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','100IM','200IM','400IM',];
  $swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Back','100 Back','200 Back','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','100 IM','200 IM','400 IM'];

  fputcsv($output, array('Forename', 'Surname', 'Swims'));

	// loop over the rows, outputting them
  while ($row = mysqli_fetch_assoc($result)) {
  	$member = mysqli_real_escape_string($link, $row['MemberID']);
  	$typeA = $typeB = null;
  	if ($info['CourseLength'] == "SHORT") {
  		$typeA = "SCPB";
  		$typeB = "CY_SC";
  	} else {
  		$typeA = "LCPB";
  		$typeB = "CY_LC";
  	}
  	$timesPB = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `times`
  	WHERE `MemberID` = '$member' AND `Type` = '$typeA';"), MYSQLI_ASSOC);
  	$timesCY = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `times`
		WHERE `MemberID` = '$member' AND `Type` = '$typeB';"), MYSQLI_ASSOC);
		
		//pre([$timesPB, $timesCY]);

  	$swims = [
  		$row['MForename'],
  		$row['MSurname'],
  		'Event'
  	];

  	$timesArrayA = $timesArrayB = $heats = $finalsA = $finalsB = [
  		'',
  		''
  	];

  	$timesArrayA[] = 'PB';
  	$timesArrayB[] = date("Y") . ' PB';
  	$heats[] = 'Heats';
  	$finalsA[] = 'Semi-Finals';
  	$finalsB[] = 'Finals';

  	for ($i = 0; $i < sizeof($swimsArray); $i++) {
  		if ($row[$swimsArray[$i]] == 1) {
  			$swims[] = $swimsTextArray[$i];
  			$timesArrayA[] = $timesPB[$swimsArray[$i]];
  			$timesArrayB[] = $timesCY[$swimsArray[$i]];
  		}
  	}

  	fputcsv($output, $swims);
  	fputcsv($output, $timesArrayA);
  	fputcsv($output, $timesArrayB);
  	fputcsv($output, $heats);
  	fputcsv($output, $finalsA);
		fputcsv($output, $finalsB);
  }
}
