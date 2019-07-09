<?php

global $db;
$user = $_SESSION['UserID'];

$query = $db->prepare("SELECT * FROM galas WHERE galas.GalaID = ?");
$query->execute([$id]);
$info = $query->fetch(PDO::FETCH_ASSOC);

$dateOfGala = new DateTime($info['GalaDate'], new DateTimeZone('Europe/London'))
;
$lastDayOfYear = new DateTime('last day of December ' . $dateOfGala->format('Y'), new DateTimeZone('Europe/London'))
;

$noTimeSheet = false;

if ($info == null) {
  halt(404);
	$noTimeSheet = true;
}

$toHash = $info['GalaID'];
if ($_SESSION['AccessLevel'] == 'Parent') {
  $toHash .= $_SESSION['UserID'];
}
$hash = hash('sha256', $toHash);

if ($_SESSION['AccessLevel'] == "Parent") {
  $uid = $_SESSION['UserID'];

  $query = $db->prepare("SELECT * FROM (((galaEntries INNER JOIN members ON galaEntries.MemberID =
  members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) INNER JOIN squads ON squads.SquadID = members.SquadID) WHERE
  galas.GalaID = ? AND members.UserID = ? ORDER BY members.MForename ASC,
  members.MSurname ASC");
  $query->execute([$id, $uid]);
} else {
  $query = $db->prepare("SELECT * FROM (((galaEntries INNER JOIN members ON galaEntries.MemberID =
  members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) INNER JOIN squads ON squads.SquadID = members.SquadID) WHERE
  galas.GalaID = ? ORDER BY members.MForename ASC, members.MSurname ASC");
  $query->execute([$id]);
}

$data = $query->fetch(PDO::FETCH_ASSOC);

if ($data == null) {
  halt(404);
}

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Back','100Back','200Back','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','100IM','200IM','400IM',];
$swimsTextArray = ['50 Fr','100 Fr','200 Fr','400 Fr','800 Fr','1500 Fr','50 Bk','100 Bk','200 Bk','50 Br','100 Br','200 Br','50 Fly','100 Fly','200 Fly','100 IM','200 IM','400 IM'];

$timesQuery = $db->prepare("SELECT * FROM times WHERE MemberID = ? AND Type = ?");

ob_start();?>

<!DOCTYPE html>
<html>
  <head>
  <meta charset='utf-8'>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i" rel="stylesheet" type="text/css">
  <!--<link href="https://fonts.googleapis.com/css?family=Open+Sans:700,700i" rel="stylesheet" type="text/css">-->
  <?php include BASE_PATH . 'helperclasses/PDFStyles/Main.php'; ?>
  <style>
  table {
    font-size: 9pt;
    /* display: table-cell; */
    table-layout: fixed;
    width: 100%;
    white-space: nowrap;
    margin: 0 0 12pt 0;
  }
  td, th {
    width: 14.29mm;
    overflow: hidden;
    text-overflow: ellipsis;
    border: 0.2mm solid #ccc;
    padding: 2pt;
  }
  .avoid-page-break-inside {
    page-break-inside: avoid;
  }
  .d-inline {
    display: inline;
  }
  .d-block {
    display: block;
  }
  .pb-3 {
    padding-bottom: 12pt;
  }
  .text-center {
    text-align: center;
  }
  </style>
  <title><?=htmlspecialchars($info['GalaName'])?> Timesheet</title>
  </head>
  <body>
    <?php include BASE_PATH . 'helperclasses/PDFStyles/Letterhead.php'; ?>

    <p>
      Generated at <?=date("H:i \o\\n d/m/Y")?>
    </p>

    <p>
      Timesheet Reference Number: <span class="mono"><?=mb_substr($hash, 0, 8)?></span>
    </p>

    <!--
    <p>
      <strong><?=$name?></strong><br>
      Parent
    </p>
    -->

    <div class="primary-box mb-3" id="title">
      <h1 class="mb-0">
        <?=htmlspecialchars($info['GalaName'])?>
      </h1>
      <p class="lead mb-0">Gala Timesheet Report<?php if ($_SESSION['AccessLevel'] == "Parent") {?> for <?=htmlspecialchars(getUserName($_SESSION['UserID']))?><?php } ?></p>
      <p class="lead mb-0"><?=htmlspecialchars($info['GalaVenue']) . " - " . date("d/m/Y", strtotime($info['GalaDate']))?></p>
    </div>

    <h2>How to use this document</h2>
    <p class="lead">
      Gala Time Sheets show personal best times from the British Swimming
      rankings and provide you with space to write down times achieved in heats,
      semi-finals and finals.
    </p>
    <p>
      When printing this document, you may need to select
      <strong>Landscape</strong> if your computer does not do so automatically.
    </p>
    <p>
      *<strong>NT</strong> denotes the swimmer does not have a PB in the given
      category or event.
    </p>

    <div class="avoid-page-break-inside">
      <?php if (bool(env('IS_CLS'))) { ?>
      <p>&copy; Chester-le-Street ASC <?=date("Y")?></p>
      <?php } else { ?>
      <p class="mb-0">&copy; Swimming Club Data Systems <?=date("Y")?></p>
      <p>Produced by Swimming Club Data Systems for <?=htmlspecialchars(env('CLUB_NAME'))?></p>
      <?php } ?>
    </div>

    <div class="page-break"></div>

    <?php
    do {
    	$member = $data['MemberID'];
    	$typeA = $typeB = null;
    	if ($info['CourseLength'] == "SHORT") {
    		$typeA = "SCPB";
    		$typeB = "CY_SC";
    	} else {
    		$typeA = "LCPB";
    		$typeB = "CY_LC";
    	}
      $timesQuery->execute([$member, $typeA]);
      $timesPB = $timesQuery->fetch(PDO::FETCH_ASSOC);
      $timesQuery->execute([$member, $typeB]);
      $timesCY = $timesQuery->fetch(PDO::FETCH_ASSOC);

      $swims = $timesArrayA = $timesArrayB = $heats = $finalsA = $finalsB = null;

    	$timesArrayA = $timesArrayB = $heats = $finalsA = $finalsB = [];

      $swims[] = 'Event';
    	$timesArrayA[] = 'PB';
    	$timesArrayB[] = '12mnth PB';
    	$heats[] = 'Heats';
    	$finalsA[] = 'Semi-F';
    	$finalsB[] = 'Finals';

    	for ($i = 0; $i < sizeof($swimsArray); $i++) {
    		if ($data[$swimsArray[$i]] == 1) {
    			$swims[] = $swimsTextArray[$i];
          if ($timesPB[$swimsArray[$i]]) {
            $timesArrayA[] = $timesPB[$swimsArray[$i]];
          } else {
            $timesArrayA[] = 'NT';
          }
          if ($timesCY[$swimsArray[$i]]) {
            $timesArrayB[] = $timesPB[$swimsArray[$i]];
          } else {
            $timesArrayB[] = 'NT';
          }
          $heats[] = '';
          $finalsA[] = '';
          $finalsB[] = '';
    		}
    	}

      $targetSize = sizeof($swimsArray) + 1 - sizeof($swims);

      for ($i = 0; $i < $targetSize; $i++) {
        $swims[] = '';
        $timesArrayA[] = '';
        $timesArrayB[] = '';
        $heats[] = '';
        $finalsA[] = '';
        $finalsB[] = '';
      }

      ?>

      <?php
      $birth = new DateTime($data['DateOfBirth'], new DateTimeZone('Europe/London'));
      $ageOnDay = $birth->diff($dateOfGala);
      $ageEndOfYear = $birth->diff($lastDayOfYear);
      ?>

      <div class="avoid-page-break-inside">
        <div class="mb-3">
          <h2 class="d-inline"><?=htmlspecialchars($data['MForename'] . ' ' . $data['MSurname'])?></h2>
          <p class="d-inline"><?=htmlspecialchars($data['SquadName'])?> Squad, Born <?=$birth->format("j F Y")?>, Age on day: <?=$ageOnDay->format('%y')?>, Age at end of year: <?=$ageEndOfYear->format('%y')?></p>
        </div>

        <table>
          <thead>
            <tr>
              <?php for ($i = 0; $i < sizeof($swims); $i++) { ?>
              <th <?php if ($i > 0) { ?> class="text-center"<?php } ?>>
                <?=htmlspecialchars($swims[$i])?>
              </th>
              <?php } ?>
            </tr>
          </thead>
          <tbody>
            <tr>
              <?php for ($i = 0; $i < sizeof($timesArrayA); $i++) { ?>
              <td <?php if ($i > 0) { ?> class="text-center"<?php } ?>>
                <?=htmlspecialchars($timesArrayA[$i])?>
              </td>
              <?php } ?>
            </tr>

            <tr>
              <?php for ($i = 0; $i < sizeof($timesArrayB); $i++) { ?>
              <td <?php if ($i > 0) { ?> class="text-center"<?php } ?>>
                <?=htmlspecialchars($timesArrayB[$i])?>
              </td>
              <?php } ?>
            </tr>

            <tr>
              <?php for ($i = 0; $i < sizeof($heats); $i++) { ?>
              <td <?php if ($i > 0) { ?> class="text-center"<?php } ?>>
                <?=htmlspecialchars($heats[$i])?>
              </td>
              <?php } ?>
            </tr>

            <tr>
              <?php for ($i = 0; $i < sizeof($finalsA); $i++) { ?>
              <td <?php if ($i > 0) { ?> class="text-center"<?php } ?>>
                <?=htmlspecialchars($finalsA[$i])?>
              </td>
              <?php } ?>
            </tr>

            <tr>
              <?php for ($i = 0; $i < sizeof($finalsB); $i++) { ?>
              <td <?php if ($i > 0) { ?> class="text-center"<?php } ?>>
                <?=htmlspecialchars($finalsB[$i])?>
              </td>
              <?php } ?>
            </tr>
          </tbody>
        </table>
      </div>

      <?php

    } while ($data = $query->fetch(PDO::FETCH_ASSOC)); ?>

    <?php include BASE_PATH . 'helperclasses/PDFStyles/PageNumbers.php'; ?>
  </body>
</html>

<?php

$html = ob_get_clean();

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();

// set font dir here
$dompdf->set_option('font_dir', BASE_PATH . 'fonts/');

$dompdf->set_option('defaultFont', 'Open Sans');
$dompdf->set_option('defaultMediaType', 'all');
$dompdf->set_option("isPhpEnabled", true);
$dompdf->set_option('isFontSubsettingEnabled', false);
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: inline');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$dompdf->stream(str_replace(' ', '', $pagetitle) . ".pdf", ['Attachment' => 0]);
