<?php

global $systemInfo;
$leavers = $systemInfo->getSystemOption('LeaversSquad');

global $db;
$getInfo = $db->prepare("SELECT members.MemberID, members.MForename, members.MMiddleNames,
members.MSurname, users.EmailAddress, members.ASANumber, members.ASACategory,
members.ClubPays, squads.SquadName, squads.SquadFee, squads.SquadCoach,
squads.SquadTimetable, squads.SquadCoC, members.DateOfBirth, members.Gender,
members.OtherNotes, members.AccessKey, memberPhotography.Website,
memberPhotography.Social, memberPhotography.Noticeboard,
memberPhotography.FilmTraining, memberPhotography.ProPhoto,
memberMedical.Conditions, memberMedical.Allergies, memberMedical.Medication
FROM ((((members INNER JOIN users ON members.UserID = users.UserID) INNER JOIN
squads ON members.SquadID = squads.SquadID) LEFT JOIN `memberPhotography` ON
members.MemberID = memberPhotography.MemberID) LEFT JOIN `memberMedical` ON
members.MemberID = memberMedical.MemberID) WHERE members.MemberID = ? AND members.UserID = ?");
$getInfo->execute([$id, $_SESSION['UserID']]);

$rowSwim = $getInfo->fetch(PDO::FETCH_ASSOC);

if ($rowSwim == null) {
  halt(404);
}

$markdown = new ParsedownExtra();
$markdown->setSafeMode(true);

$forename = $rowSwim['MForename'];
$middlename = $rowSwim['MMiddleNames'];
$surname = $rowSwim['MSurname'];
$dateOfBirth = $rowSwim['DateOfBirth'];
$sex = $rowSwim['Gender'];
$otherNotes = $rowSwim['OtherNotes'];
$age = date_diff(date_create($rowSwim['DateOfBirth']),
date_create('today'))->y;

$pagetitle = htmlspecialchars($rowSwim['MForename'] . " " . $rowSwim['MSurname']);

/* Stats Section */
$swimsCountArray = [];
$strokesCountArray = [0, 0, 0, 0, 0];
$strokesCountTextArray = ["Freestyle", "Breaststroke", "Butterfly", "Backstroke", "Individual Medley"];
$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
$strokesArray = ['0','0','0','0','0','0','1','1','1','2','2','2','3','3','3','4','4','4','4',];
$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
$counter = 0;
for ($i=0; $i<sizeof($swimsArray); $i++) {
  $col = $swimsArray[$i];
  $sql = $db->prepare("SELECT COUNT(*) FROM `galaEntries` WHERE `MemberID` = ? AND `$col` = '1'");
  $sql->execute([$id]);
  $count = $sql->fetchColumn();
  $swimsCountArray[$i] = $count;
  $strokesCountArray[$strokesArray[$i]] += $count;
  $counter += $count;
}
?>

<?php include BASE_PATH . "views/header.php"; ?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("swimmers")?>">Swimmers</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars($rowSwim["MForename"])?> <?=htmlspecialchars($rowSwim["MSurname"][0])?></li>
    </ol>
  </nav>

  <?php if (isset($_SESSION['AddSwimmerSuccessState'])) {
    echo $_SESSION['AddSwimmerSuccessState'];
    unset($_SESSION['AddSwimmerSuccessState']);
  } ?>

  <div id="dash">
    <h1>
      <?=htmlspecialchars($rowSwim["MForename"]) . " "?>
      <?php if ($rowSwim["MMiddleNames"] != "") {
        echo htmlspecialchars($rowSwim["MMiddleNames"]) . " ";
      } ?>
      <?=htmlspecialchars($rowSwim["MSurname"])?>
      <br>
      <small>Swimmer, <?=htmlspecialchars($rowSwim["SquadName"])?> Squad</small>
    </h1>
  </div>

  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" id="about-tab" data-toggle="tab" href="#about" role="tab" aria-controls="about" aria-selected="true">About</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" id="times-tab" data-toggle="tab" href="#times" role="tab" aria-controls="times" aria-selected="false">Times</a>
    </li>
    <?php if ($counter>0) { ?>
    <li class="nav-item">
      <a class="nav-link" id="stats-tab" data-toggle="tab" href="#stats" role="tab" aria-controls="stats" aria-selected="false">Stats</a>
    </li>
    <?php } ?>
    <li class="nav-item">
      <a class="nav-link" id="squad-tab" data-toggle="tab" href="#squad" role="tab" aria-controls="squad" aria-selected="false">Squad</a>
    </li>
    <?php if ($leavers != null) { ?>
    <li class="nav-item">
      <a class="nav-link" id="leave-club-tab" data-toggle="tab" href="#leave-club" role="tab" aria-controls="leave-club" aria-selected="false">Leave</a>
    </li>
    <?php } ?>
    <li class="nav-item">
      <a class="nav-link" id="additional-info-tab" data-toggle="tab" href="#additional-details" role="tab" aria-controls="additional-details" aria-selected="false">More</a>
    </li>
  </ul>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade mt-3 show active" id="about" role="tabpanel" aria-labelledby="about-tab">
      <h2>About <?php echo $rowSwim["MForename"]; ?></h2>
      <div class="row">
        <div class="col-sm-6 col-md-4">
          <h3 class="h6">Date of Birth</h3>
          <p><?=date('j F Y', strtotime($rowSwim['DateOfBirth']))?></p>
        </div>
        <div class="col-sm-6 col-md-4">
          <h3 class="h6">Swim England Number</h3>
          <p>
            <a
            href="https://www.swimmingresults.org/biogs/biogs_details.php?tiref=<?=htmlspecialchars($rowSwim["ASANumber"])?>"
            target="_blank" title="ASA Biographical Data">
              <?=htmlspecialchars($rowSwim['ASANumber'])?>
            </a>
          </p>
        </div>
        <div class="col-sm-6 col-md-4">
          <h3 class="h6">Swim England Category</h3>
          <p><?=htmlspecialchars($rowSwim["ASACategory"])?></p>
        </div>
        <div class="col-sm-6 col-md-4">
          <h3 class="h6">Attendance</h3>
          <p>
            <?=getAttendanceByID(null, $id, 4)?>% over the last 4 weeks,
            <?=getAttendanceByID(null, $id)?>% over all time
          </p>
        </div>
        <div class="col-sm-6 col-md-4">
          <h3 class="h6">Sex</h3>
          <p><?=htmlspecialchars($rowSwim["Gender"])?></p>
        </div>
      </div>

      <h2>Medical Notes</h2>
      <div class="row">
        <div class="col-sm-6 col-md-4">
          <h3 class="h6">
            Medical Conditions or Disabilities
          </h3>
          <?php if ($rowSwim["Conditions"] != "") { ?>
            <?=$markdown->text($rowSwim["Conditions"])?>
          <?php } else { ?>
            <p>None</p>
          <?php } ?>
        </div>

        <div class="col-sm-6 col-md-4">
          <h3 class="h6">
            Allergies
          </h3>
          <?php if ($rowSwim["Allergies"] != "") { ?>
            <?=$markdown->text($rowSwim["Allergies"])?>
          <?php } else { ?>
            <p>None</p>
          <?php } ?>
        </div>

        <div class="col-sm-6 col-md-4">
          <h3 class="h6">
            Medication
          </h3>
          <?php if ($rowSwim["Medication"] != "") { ?>
            <?=$markdown->text($rowSwim["Medication"])?>
          <?php } else { ?>
            <p>None</p>
          <?php } ?>
        </div>
      </div>

      <h2>Other Notes</h2>
      <div class="row">
        <?php if ($rowSwim["OtherNotes"] != "") { ?>
        <div class="col-sm-6 col-md-4">
          <h3 class="h6">Parent's Notes</h3>
          <?=$markdown->text($rowSwim["OtherNotes"])?>
        </div>
        <?php } ?>

        <div class="col-sm-6 col-md-4">
          <h3 class="h6">Exempt from Squad Fees?</h3>
          <?php if ($rowSwim["ClubPays"] == 1){ ?>
            <p>Yes</p>
          <?php } else { ?>
            <p>No <em>(Only swimmers at University are usually exempt from most
            fees)</em></p>
          <?php } ?>
        </div>
      </div>

      <h2>Photography Permissions</h2>

      <div class="">
      <?php if (($rowSwim['Website'] != 1 || $rowSwim['Social'] != 1 ||
      $rowSwim['Noticeboard'] != 1 || $rowSwim['FilmTraining'] != 1 ||
      $rowSwim['ProPhoto'] != 1) && ($age < 18)) { ?>
        <p>There are limited photography permissions for this swimmer</p>
        <ul> <?php
        if ($row['Website'] != 1) { ?>
          <li>Photos <strong>must not</strong> be taken of this swimmer for our
          website</li><?php
        }
        if ($row['Social'] != 1) { ?>
          <li>Photos <strong>must not</strong> be taken of this swimmer for our
          social media</li><?php
        }
        if ($row['Noticeboard'] != 1) { ?>
          <li>Photos <strong>must not</strong> be taken of this swimmer for our
          noticeboard</li><?php
        }
        if ($row['FilmTraining'] != 1) { ?>
          <li>This swimmer <strong>must not</strong> be filmed for the purposes
          of training</li><?php
        }
        if ($row['ProPhoto'] != 1) { ?>
          <li>Photos <strong>must not</strong> be taken of this swimmer by
          photographers</li><?php
        }
         ?></ul><?php
      } else {
         ?><p class="media-body pb-3 mb-0 lh-125">
           There are no photography limitiations for this swimmer.
         </p><?php
      } ?>
      </div>

      <div class="mt-3">
        <a class="btn btn-success" href="<?=autoUrl("swimmers/" .
        $id . "/edit")?>">Edit Details</a>
        <a class="btn btn-success" href="<?=autoUrl("swimmers/" . $id .
        "/medical");?>">Edit Medical Notes</a>
      </div>

    </div>
    <div class="tab-pane fade mt-3" id="times" role="tabpanel" aria-labelledby="times-tab">
      <div class="">
        <h2 class="border-bottom border-gray pb-2 mb-0">Best Times</h2>
        <?php
        $timeGet = $db->prepare("SELECT * FROM `times` WHERE `MemberID` = ? AND `Type` = ?");
        $timeGet->execute([$id, 'SCPB']);
        $sc = $timeGet->fetch(PDO::FETCH_ASSOC);
        $timeGet->execute([$id, 'LCPB']);
        $lc = $timeGet->fetch(PDO::FETCH_ASSOC);
        $timeGet->execute([$id, 'CY_SC']);
        $scy = $timeGet->fetch(PDO::FETCH_ASSOC);
        $timeGet->execute([$id, 'CY_LC']);
        $lcy = $timeGet->fetch(PDO::FETCH_ASSOC);
        $ev = ['50Free', '100Free', '200Free', '400Free', '800Free', '1500Free',
        '50Breast', '100Breast', '200Breast', '50Fly', '100Fly', '200Fly',
        '50Back', '100Back', '200Back', '100IM', '200IM', '400IM'];
        $evs = ['50m Free', '100m Free', '200m Free', '400m Free', '800m Free', '1500m Free',
        '50m Breast', '100m Breast', '200m Breast', '50m Fly', '100m Fly', '200m Fly',
        '50m Back', '100m Back', '200m Back', '100m IM', '200m IM', '400m IM'];
        $openedTable = false; ?>
        <?php for ($i = 0; $i < sizeof($ev); $i++) {
        if ($sc[$ev[$i]] != "" || $lc[$ev[$i]] != "") {
        if (!$openedTable) { ?>
        <table class="table table-sm table-borderless table-striped mb-0">
          <thead class="thead-light">
            <tr class="">
              <th class="">Swim</th>
              <th>Short Course</th>
              <?php if (!isset($mob) || !$mob) { ?>
              <th><?=date("Y")?> Short Course</th>
              <?php } ?>
              <th>Long Course</th>
              <?php if (!isset($mob) || !$mob) { ?>
              <th><?=date("Y")?> Long Course</th>
              <?php } ?>
            </thead>
            <tbody>
            <?php
            $openedTable = true;
            }
            echo '<tr class=""><th class="">' . $evs[$i] . '</th><td>';
            if ($sc[$ev[$i]] != "") {
              echo $sc[$ev[$i]];
            }
            echo '</td><td>';
            if (!isset($mob) || !$mob) {
              if ($scy[$ev[$i]] != "") {
                echo $scy[$ev[$i]];
              }
              echo '</td><td>';
            }
            if ($lc[$ev[$i]] != "") {
              echo $lc[$ev[$i]];
            }
            if (!isset($mob) || !$mob) {
              echo '</td><td>';
              if ($lcy[$ev[$i]] != "") {
                echo $lcy[$ev[$i]];
              }
            }
            echo '</td></tr>';
            }
        }
        if ($openedTable) { ?>
          </tbody>
        </table>
        <?php } else { ?>
        <p class="lead mt-2 mb-0">There are no times available for this swimmer.</p>
        <?php } ?>
      </div>
    </div>
    <div class="tab-pane fade mt-3" id="stats" role="tabpanel" aria-labelledby="stats-tab">
      <?php	if ($counter>0) { ?>
      	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
          google.charts.load('current', {'packages':['corechart']});

          google.charts.setOnLoadCallback(drawPieChart);
    			google.charts.setOnLoadCallback(drawBarChart);

          function drawPieChart() {

            var data = google.visualization.arrayToDataTable([
              ['Stroke', 'Total Number of Entries'],
    					<?php for ($i=0; $i<sizeof($strokesCountArray); $i++) {
              	echo "['" . $strokesCountTextArray[$i] . "', " . $strokesCountArray[$i] . "],";
    					} ?>
            ]);

            var options = {
              title: 'Gala Entries by Stroke',
    					fontName: 'Open Sans',
    					backgroundColor: {
    						fill:'transparent'
    					},
    					chartArea: {
    						left: '0',
    						right: '0',
    					}
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            chart.draw(data, options);
          }
    			function drawBarChart() {

            var data = google.visualization.arrayToDataTable([
              ['Stroke', 'Total Number of Entries'],
              <?php for ($i=0; $i<sizeof($swimsArray); $i++) {
    						if ($swimsCountArray[$i] > 0) {
              		echo "['" . $swimsTextArray[$i] . "', " . $swimsCountArray[$i] . "],";
    						}
    					} ?>
            ]);

            var options = {
              title: 'Gala Entries by Event',
    					fontName: 'Open Sans',
    					backgroundColor: {
    						fill:'transparent'
    					},
    					chartArea: {
    						left: '0',
    						right: '0',
    					},
    					backgroundColor: {
    						fill:'transparent'
    					},
    					legend: {
    						position: 'none',
    					}
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('barchart'));

            chart.draw(data, options);
          }
        </script>
        <div class="">
          <h2 class="border-bottom border-gray pb-2 mb-0">Gala Statistics</h2>
    	    <div class="chart" id="piechart"></div>
    			<div class="chart" id="barchart"></div>
        </div>
      <?php } ?>
    </div>
    <div class="tab-pane fade mt-3" id="squad" role="tabpanel" aria-labelledby="squad-tab">
      <div class="">
        <h2 class="border-bottom border-gray pb-2 mb-0">Squad Information</h2>
        <div class="media pt-3">
          <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
            <strong class="d-block text-gray-dark">Squad</strong>
            <?=htmlspecialchars($rowSwim["SquadName"])?> Squad
          </p>
        </div>
        <div class="media pt-3">
          <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
            <strong class="d-block text-gray-dark">Squad Fee</strong>
            <?php if ($rowSwim["ClubPays"] == 1) {
              echo htmlspecialchars($rowSwim['MForename']); ?> is Exempt from Squad Fees
            <?php } else { ?>
              &pound;<?=htmlspecialchars(number_format($rowSwim['SquadFee'], 2))?>
            <?php } ?>
          </p>
        </div>
        <?php if ($rowSwim['SquadTimetable'] != "") { ?>
        <div class="media pt-3">
          <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
            <strong class="d-block text-gray-dark">Squad Timetable</strong>
            <a href="<?=htmlspecialchars($rowSwim["SquadTimetable"])?>">Squad Timetable</a>
          </p>
        </div>
        <?php }
        if ($rowSwim['SquadCoC'] != "") { ?>
        <div class="media pt-3">
          <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
            <strong class="d-block text-gray-dark">Squad Code of Conduct</strong>
            <a href="<?=autoUrl("pages/codeofconduct/" . $rowSwim["SquadCoC"])?>">Squad Code of Conduct</a>
          </p>
        </div>
        <?php } ?>

      <div class="media pt-3 mb-0">
        <p class="media-body pb-3 mb-0 lh-125">
          <strong class="d-block text-gray-dark">Squad Coach</strong>
          <?=htmlspecialchars($rowSwim["SquadCoach"])?>
        </p>
      </div>
    </div>
    </div>
    <?php if ($leavers != null) { ?>
    <div class="tab-pane fade mt-3" id="leave-club" role="tabpanel" aria-labelledby="leave-club-tab">
      <h2>Leave the Club</h2>
      <p class="lead">
        You can leave the club at any time.
      </p>
      <p>
        As we charge you on the first day of each month for that upcoming
        month, it makes sense for our systems to remove your swimmer on the
        first. It doesn't matter if you stop attending before this date.
      </p>

      <p>
        If <?=htmlspecialchars($rowSwim["MForename"])?> won't be returning to
        the club from 1 <?=date("F Y", strtotime('+1 month'))?>, <a
        href="<?=autoUrl("swimmers/" . $id . "/leaveclub/")?>">please click
        here</a>.
      </p>
      <p>
        This will update our systems and automatically remove
        <?=htmlspecialchars($rowSwim["MForename"])?> from our registers and
        billing systems on this date.
      </p>
    </div>
    <?php } ?>
    <div class="tab-pane fade mt-3" id="additional-details" role="tabpanel" aria-labelledby="additional-info-tab">
      <div class="">
        <?php
        $col = "col-sm-6";
        if (isset($rowSwim['ThriveNumber']) != "") {
          $col = "col-sm-4";
        }
        ?>
        <h2 class="">Personal Data</h2>
        <p>
          Under the General Data Protection Regulation, you can request for
          free to download all personal data held about
          <?=$rowSwim["MForename"]?> by <?=htmlspecialchars(env('CLUB_NAME'))?>.
        </p>
        <p>
          <a href="<?=autoUrl("my-account/general/download-member-data/" . $id)?>"
          class="btn btn-primary">
            Download data
          </a>
        </p>
        <p>
          You can download your own personal data from the general account
          options menu.
        </p>

        <?php if (defined("IS_CLS") && IS_CLS) { ?>

        <h2 class="">Membership Card</h2>
        <p>
          Your swimmer's club membership card can be used by our coaches in an
          emergency to access the medical forms and contact details for your
          swimmer.
        </p>
        <p>
          <a href="<?=currentUrl()?>membershipcard" class="btn btn-primary" target="_blank">
            Print Membership Card
          </a>
        </p>

        <?php } ?>
        
        <h2 class="">Membership Barcodes</h2>
        <p>We'll let you know in advance if you'll ever need to print these out.</p>
        <div class="row">
          <div class="<?php echo $col; ?>">
            <div class="text-center border p-2 bg-white">
              <span class="lead mb-2">Swim England Number</span>
              <img class="img-fluid mx-auto d-block"
              src="<?php echo autoUrl("services/barcode-generator?codetype=Code128a&size=60&text=" . $rowSwim['ASANumber'] . "&print=false"); ?>"
              srcset="<?php echo autoUrl("services/barcode-generator?codetype=Code128a&size=120&text=" . $rowSwim['ASANumber'] . "&print=false"); ?> 2x, <?php echo autoUrl("services/barcode-generator?codetype=Code128a&size=180&text=" . $rowSwim['ASANumber'] . "&print=false"); ?> 3x"
              alt="<?php echo $rowSwim['ASANumber']; ?>"></img>
              <span class="mono"><?php echo $rowSwim['ASANumber']; ?></span>
            </div>
            <span class="d-block d-sm-none mb-3"></span>
          </div>
          <div class="<?php echo $col; ?>">
            <div class="text-center border p-2 bg-white">
              <span class="lead mb-2">CLSASC Number</span>
              <img class="img-fluid mx-auto d-block"
              src="<?php echo autoUrl("services/barcode-generator?codetype=Code128&size=60&text=" . urlencode(env('ASA_CLUB_CODE')) . $rowSwim['MemberID'] . "&print=false"); ?>"
              srcset="<?php echo autoUrl("services/barcode-generator?codetype=Code128&size=120&text=" . urlencode(env('ASA_CLUB_CODE')) . $rowSwim['MemberID'] . "&print=false"); ?> 2x, <?php echo autoUrl("services/barcode-generator?codetype=Code128&size=180&text=" . urlencode(env('ASA_CLUB_CODE')) . $rowSwim['MemberID'] . "&print=false"); ?> 3x"
              alt="CLSX<?php echo $rowSwim['MemberID']; ?>"></img>
              <span class="mono"><?=htmlspecialchars(env('ASA_CLUB_CODE'))?><?php echo $rowSwim['MemberID']; ?></span>
            </div>
            <?php if (isset($rowSwim['ThriveNumber']) && $rowSwim['ThriveNumber'] != "") { ?><span class="d-block d-sm-none mb-3"></span><?php } ?>
          </div>
          <?php if (isset($rowSwim['ThriveNumber']) && $rowSwim['ThriveNumber'] != "") { ?>
          <div class="<?php echo $col; ?>">
            <div class="text-center border p-2 bg-white">
              <span class="lead mb-2">Thrive Card</span>
              <img class="img-fluid mx-auto d-block"
              src="<?php echo autoUrl("services/barcode-generator?codetype=Code128&size=60&text=" . $rowSwim['ThriveNumber'] . "&print=false"); ?>"
              srcset="<?php echo autoUrl("services/barcode-generator?codetype=Code128&size=120&text=" . $rowSwim['ThriveNumber'] . "&print=false"); ?> 2x, <?php echo autoUrl("services/barcode-generator?codetype=Code128&size=180&text=" . $rowSwim['ThriveNumber'] . "&print=false"); ?> 3x"
              alt="<?php echo $rowSwim['ThriveNumber']; ?>"></img>
              <span class="mono"><?php echo $rowSwim['ThriveNumber']; ?></span>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include BASE_PATH . "views/footer.php"; ?>
