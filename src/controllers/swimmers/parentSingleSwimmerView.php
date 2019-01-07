<?php

$userID = $_SESSION['UserID'];
$id = mysqli_real_escape_string($link, $id);

$use_white_background = true;

$query = "SELECT * FROM members WHERE MemberID = '$id' ";
$result = mysqli_query($link, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$forename = $row['MForename'];
$middlename = $row['MMiddleNames'];
$surname = $row['MSurname'];
$dateOfBirth = $row['DateOfBirth'];
$sex = $row['Gender'];
$otherNotes = $row['OtherNotes'];

// Get the swimmer name
$sqlSecurityCheck = "SELECT `MForename`, `MSurname`, `UserID` FROM `members` WHERE MemberID = '$id';";
$resultSecurityCheck = mysqli_query($link, $sqlSecurityCheck);
$swimmersSecurityCheck = mysqli_fetch_array($resultSecurityCheck, MYSQLI_ASSOC);

$pagetitle;
if ($swimmersSecurityCheck['UserID'] != $userID) {
  halt(404);}
else {
  $pagetitle = $swimmersSecurityCheck['MForename'] . " " .
  $swimmersSecurityCheck['MSurname'];
  $sqlSwim = "SELECT members.MForename, members.MForename, members.MMiddleNames,
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
  members.MemberID = memberMedical.MemberID) WHERE members.MemberID = '$id';";
  $resultSwim = mysqli_query($link, $sqlSwim);
  $rowSwim = mysqli_fetch_array($resultSwim, MYSQLI_ASSOC);
  $age = date_diff(date_create($rowSwim['DateOfBirth']),
  date_create('today'))->y;
  $title = null;

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
    $sql = "SELECT `$col` FROM `galaEntries` WHERE `MemberID` = '$id' AND `$col` = '1'";
    $result = mysqli_query($link, $sql);
    $count = mysqli_num_rows($result);
    $swimsCountArray[$i] = $count;
    $strokesCountArray[$strokesArray[$i]] += $count;
    $counter += $count;
  }
  ?>

  <?php include BASE_PATH . "views/header.php"; ?>

  <div class="container">

    <? if (isset($_SESSION['AddSwimmerSuccessState'])) {
      echo $_SESSION['AddSwimmerSuccessState'];
      unset($_SESSION['AddSwimmerSuccessState']);
    } ?>

    <div class="d-flex align-items-center p-3 my-3 text-white bg-primary rounded" id="dash">
      <img class="mr-3" src="https://www.chesterlestreetasc.co.uk/apple-touch-icon-ipad-retina.png" alt="" width="48" height="48">
      <div class="lh-100">
        <h1 class="h6 mb-0 text-white lh-100"><?php echo $rowSwim["MForename"] . " ";
        if ($rowSwim["MMiddleNames"] != "") {
           echo $rowSwim["MMiddleNames"] . " ";
        }
        echo $rowSwim["MSurname"]?></h1>
        <small>Swimmer, <?php echo $rowSwim["SquadName"]; ?> Squad</small>
      </div>
    </div>
    <p>
      If <?=$rowSwim["MForename"]?> won't be returning to the club from 1
      <?=date("F Y", strtotime('+1 month'))?>, <a href="<?=autoUrl("swimmers/" .
      $id . "/leaveclub/")?>">please click here</a>. This will update our
      systems and automatically remove <?=$rowSwim["MForename"]?> from our
      registers and billing systems on this date.
    </p>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="about-tab" data-toggle="tab" href="#about" role="tab" aria-controls="about" aria-selected="true">About</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="times-tab" data-toggle="tab" href="#times" role="tab" aria-controls="times" aria-selected="false">Times</a>
      </li>
      <? if ($counter>0) { ?>
      <li class="nav-item">
        <a class="nav-link" id="stats-tab" data-toggle="tab" href="#stats" role="tab" aria-controls="stats" aria-selected="false">Stats</a>
      </li>
      <? } ?>
      <li class="nav-item">
        <a class="nav-link" id="squad-tab" data-toggle="tab" href="#squad" role="tab" aria-controls="squad" aria-selected="false">Squad</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="additional-info-tab" data-toggle="tab" href="#additional-details" role="tab" aria-controls="additional-details" aria-selected="false">More</a>
      </li>
    </ul>
    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade mt-3 show active" id="about" role="tabpanel" aria-labelledby="about-tab">
        <div class="">
          <h2 class="border-bottom border-gray pb-2 mb-0">About <?php echo $rowSwim["MForename"]; ?></h2>
          <div class="media pt-3">
            <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
              <strong class="d-block text-gray-dark">Date of Birth</strong>
              <?php echo date('j F Y', strtotime($rowSwim['DateOfBirth'])); ?>
            </p>
          </div>
          <div class="media pt-3">
            <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
              <strong class="d-block text-gray-dark">ASA Number</strong>
              <a href="https://www.swimmingresults.org/biogs/biogs_details.php?tiref=<?php echo $rowSwim["ASANumber"]; ?>" target="_blank" title="ASA Biographical Data"><?php echo $rowSwim["ASANumber"]; ?> <i class="fa fa-external-link" aria-hidden="true"></i></a>
            </p>
          </div>
          <div class="media pt-3">
            <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
              <strong class="d-block text-gray-dark">ASA Membership Category</strong>
              <?php echo $rowSwim["ASACategory"]; ?>
            </p>
          </div>
          <div class="media pt-3">
            <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
              <strong class="d-block text-gray-dark">Attendance</strong>
              <?php echo getAttendanceByID($link, $id, 4); ?>% over the last 4 weeks, <?php echo getAttendanceByID($link, $id); ?>% over all time
            </p>
          </div>
          <div class="media pt-3">
            <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
              <strong class="d-block text-gray-dark">Sex</strong>
              <?php echo $rowSwim["Gender"]; ?>
            </p>
          </div>
          <div class="media pt-3">
            <div class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
              <p class="mb-0 text-gray-dark">
                <strong>
                  Medical Notes
                </strong>
              </p>

              <p class="mb-0 mt-2">
                <em>
                  Medical Conditions or Disabilities
                </em>
              </p>
              <? if ($rowSwim["Conditions"] != "") { ?>
                <p class="mb-0"><?php echo $rowSwim["Conditions"]; ?></p>
              <? } else { ?>
                <p class="mb-0">None</p>
              <? } ?>

              <p class="mb-0 mt-2">
                <em>
                  Allergies
                </em>
              </p>
              <? if ($rowSwim["Allergies"] != "") { ?>
                <p class="mb-0"><?php echo $rowSwim["Allergies"]; ?></p>
              <? } else { ?>
                <p class="mb-0">None</p>
              <? } ?>

              <p class="mb-0 mt-2">
                <em>
                  Medication
                </em>
              </p>
              <? if ($rowSwim["Medication"] != "") { ?>
                <p class="mb-0"><?php echo $rowSwim["Medication"]; ?></p>
              <? } else { ?>
                <p class="mb-0">None</p>
              <? } ?>

            </div>
          </div>
          <? if ($rowSwim["OtherNotes"] != "") { ?>
            <div class="media pt-3">
              <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
                <strong class="d-block text-gray-dark">Other Notes</strong>
                <?php echo $rowSwim["OtherNotes"]; ?>
              </p>
            </div>
          <?php } ?>
          <div class="media pt-3">
            <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
              <strong class="d-block text-gray-dark">
                Exempt from Squad and Membership Fees?
              </strong>
              <?php if ($rowSwim["ClubPays"] == 1){ ?>
                Yes
              <? } else { ?>
                No <em>(Only swimmers at University are usually exempt from most
                fees)</em>
              <? } ?>
            </p>
          </div>
          <div class="media pt-3 border-bottom border-gray">
          <? if (($rowSwim['Website'] != 1 || $rowSwim['Social'] != 1 ||
          $rowSwim['Noticeboard'] != 1 || $rowSwim['FilmTraining'] != 1 ||
          $rowSwim['ProPhoto'] != 1) && ($age < 18)) { ?>
            <p>There are limited photography permissions for this swimmer</p>
            <ul> <?
            if ($row['Website'] != 1) { ?>
              <li>Photos <strong>must not</strong> be taken of this swimmer for our
              website</li><?
            }
            if ($row['Social'] != 1) { ?>
              <li>Photos <strong>must not</strong> be taken of this swimmer for our
              social media</li><?
            }
            if ($row['Noticeboard'] != 1) { ?>
              <li>Photos <strong>must not</strong> be taken of this swimmer for our
              noticeboard</li><?
            }
            if ($row['FilmTraining'] != 1) { ?>
              <li>This swimmer <strong>must not</strong> be filmed for the purposes
              of training</li><?
            }
            if ($row['ProPhoto'] != 1) { ?>
              <li>Photos <strong>must not</strong> be taken of this swimmer by
              photographers</li><?
            }
             ?></ul><?
          } else {
             ?><p class="media-body pb-3 mb-0 lh-125">
               There are no photography limitiations for this swimmer.
             </p><?
          } ?>
          </div>
          <span class="d-block text-right mt-3">
            <a href="<? echo autoUrl("swimmers/edit/" . $id);?>">Edit Details</a> or <a href="<? echo autoUrl("swimmers/" . $id . "/medical");?>">Edit Medical Notes</a>
          </span>
        </div>
      </div>
      <div class="tab-pane fade mt-3" id="times" role="tabpanel" aria-labelledby="times-tab">
        <div class="">
          <h2 class="border-bottom border-gray pb-2 mb-0">Best Times</h2>
          <?
          $sc = "SELECT * FROM `times` WHERE `MemberID` = '$id' AND `Type` = 'SCPB';";
          $lc = "SELECT * FROM `times` WHERE `MemberID` = '$id' AND `Type` = 'LCPB';";
          $scy = "SELECT * FROM `times` WHERE `MemberID` = '$id' AND `Type` = 'CY_SC';";
          $lcy = "SELECT * FROM `times` WHERE `MemberID` = '$id' AND `Type` = 'CY_LC';";
          $sc = mysqli_fetch_array(mysqli_query($link, $sc), MYSQLI_ASSOC);
          $lc = mysqli_fetch_array(mysqli_query($link, $lc), MYSQLI_ASSOC);
          $scy = mysqli_fetch_array(mysqli_query($link, $scy), MYSQLI_ASSOC);
          $lcy = mysqli_fetch_array(mysqli_query($link, $lcy), MYSQLI_ASSOC);
          $ev = ['50Free', '100Free', '200Free', '400Free', '800Free', '1500Free',
          '50Breast', '100Breast', '200Breast', '50Fly', '100Fly', '200Fly',
          '50Back', '100Back', '200Back', '100IM', '200IM', '400IM'];
          $evs = ['50m Free', '100m Free', '200m Free', '400m Free', '800m Free', '1500m Free',
          '50m Breast', '100m Breast', '200m Breast', '50m Fly', '100m Fly', '200m Fly',
          '50m Back', '100m Back', '200m Back', '100m IM', '200m IM', '400m IM'];
          $openedTable = false; ?>
          <? for ($i = 0; $i < sizeof($ev); $i++) {
          if ($sc[$ev[$i]] != "" || $lc[$ev[$i]] != "") {
          if (!$openedTable) { ?>
          <table class="table table-sm table-borderless table-striped mb-0">
            <thead class="thead-light">
              <tr class="">
                <th class="">Swim</th>
                <th>Short Course</th>
                <?php if (!$mob) { ?>
                <th>SC: Last 12 Months</th>
                <?php } ?>
                <th>Long Course</th>
                <?php if (!$mob) { ?>
                <th>LC: Last 12 Months</th>
                <?php } ?>
              </thead>
              <tbody>
              <?
              $openedTable = true;
              }
              echo '<tr class=""><th class="">' . $evs[$i] . '</th><td>';
              if ($sc[$ev[$i]] != "") {
                echo $sc[$ev[$i]];
              }
              echo '</td><td>';
              if (!$mob) {
                if ($scy[$ev[$i]] != "") {
                  echo $scy[$ev[$i]];
                }
                echo '</td><td>';
              }
              if ($lc[$ev[$i]] != "") {
                echo $lc[$ev[$i]];
              }
              if (!$mob) {
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
          <? } else { ?>
          <p class="lead mt-2 mb-0">There are no times available for this swimmer.</p>
          <? } ?>
        </div>
      </div>
      <div class="tab-pane fade mt-3" id="stats" role="tabpanel" aria-labelledby="stats-tab">
        <?	if ($counter>0) { ?>
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
        <? } ?>
      </div>
      <div class="tab-pane fade mt-3" id="squad" role="tabpanel" aria-labelledby="squad-tab">
        <div class="">
          <h2 class="border-bottom border-gray pb-2 mb-0">Squad Information</h2>
          <div class="media pt-3">
            <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
              <strong class="d-block text-gray-dark">Squad</strong>
              <?php echo $rowSwim["SquadName"]; ?> Squad
            </p>
          </div>
          <div class="media pt-3">
            <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
              <strong class="d-block text-gray-dark">Squad Fee</strong>
              <? if ($rowSwim["ClubPays"] == 1) {
                echo $rowSwim['MForename']; ?> is Exempt from Squad Fees
              <? } else { ?>
                &pound;<? echo $rowSwim['SquadFee']; ?>
              <? } ?>
            </p>
          </div>
          <?php if ($rowSwim['SquadTimetable'] != "") { ?>
          <div class="media pt-3">
            <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
              <strong class="d-block text-gray-dark">Squad Timetable</strong>
              <a href="<?php echo $rowSwim["SquadTimetable"]; ?>">Squad Timetable</a>
            </p>
          </div>
          <?php }
          if ($rowSwim['SquadCoC'] != "") { ?>
          <div class="media pt-3">
            <p class="media-body pb-3 mb-0 lh-125 border-bottom border-gray">
              <strong class="d-block text-gray-dark">Squad Code of Conduct</strong>
              <a href="<?php echo autoUrl("pages/codeofconduct/" . $rowSwim["SquadCoC"]); ?>">Squad Code of Conduct</a>
            </p>
          </div>
          <?php } ?>

        <div class="media pt-3 mb-0">
          <p class="media-body pb-3 mb-0 lh-125">
            <strong class="d-block text-gray-dark">Squad Coach</strong>
            <?php echo $rowSwim["SquadCoach"]; ?>
          </p>
        </div>
      </div>
      </div>
      <div class="tab-pane fade mt-3" id="additional-details" role="tabpanel" aria-labelledby="additional-info-tab">
        <div class="">
          <?
          $col = "col-sm-6";
          if ($row['ThriveNumber'] != "") {
            $col = "col-sm-4";
          }
          ?>
          <h2 class="">Membership Card</h2>
          <p>
            Your swimmer's club membership card can be used by our coaches in an
            emergency to access the medical forms and contact details for your
            swimmer.
          </p>
          <p>
            <a href="<?=app('request')->curl?>membershipcard" class="btn btn-primary" target="_blank">
              Print Membership Card
            </a>
          </p>
          <h2 class="">Membership Barcodes</h2>
          <p>We'll let you know in advance if you'll ever need to print these out.</p>
          <div class="row">
            <div class="<? echo $col; ?>">
              <div class="text-center border p-2 bg-white">
                <span class="lead mb-2">ASA Number</span>
                <img class="img-fluid mx-auto d-block"
                src="<? echo autoUrl("services/barcode-generator?codetype=Code128a&size=60&text=" . $row['ASANumber'] . "&print=false"); ?>"
                srcset="<? echo autoUrl("services/barcode-generator?codetype=Code128a&size=120&text=" . $row['ASANumber'] . "&print=false"); ?> 2x, <? echo autoUrl("services/barcode-generator?codetype=Code128a&size=180&text=" . $row['ASANumber'] . "&print=false"); ?> 3x"
                alt="<? echo $row['ASANumber']; ?>"></img>
                <span class="mono"><? echo $row['ASANumber']; ?></span>
              </div>
              <span class="d-block d-sm-none mb-3"></span>
            </div>
            <div class="<? echo $col; ?>">
              <div class="text-center border p-2 bg-white">
                <span class="lead mb-2">CLSASC Number</span>
                <img class="img-fluid mx-auto d-block"
                src="<? echo autoUrl("services/barcode-generator?codetype=Code128&size=60&text=CLSX" . $row['MemberID'] . "&print=false"); ?>"
                srcset="<? echo autoUrl("services/barcode-generator?codetype=Code128&size=120&text=CLSX" . $row['MemberID'] . "&print=false"); ?> 2x, <? echo autoUrl("services/barcode-generator?codetype=Code128&size=180&text=CLSX" . $row['MemberID'] . "&print=false"); ?> 3x"
                alt="CLSX<? echo $row['MemberID']; ?>"></img>
                <span class="mono">CLSX<? echo $row['MemberID']; ?></span>
              </div>
              <? if ($row['ThriveNumber'] != "") { ?><span class="d-block d-sm-none mb-3"></span><? } ?>
            </div>
            <? if ($row['ThriveNumber'] != "") { ?>
            <div class="<? echo $col; ?>">
              <div class="text-center border p-2 bg-white">
                <span class="lead mb-2">Thrive Card</span>
                <img class="img-fluid mx-auto d-block"
                src="<? echo autoUrl("services/barcode-generator?codetype=Code128&size=60&text=" . $row['ThriveNumber'] . "&print=false"); ?>"
                srcset="<? echo autoUrl("services/barcode-generator?codetype=Code128&size=120&text=" . $row['ThriveNumber'] . "&print=false"); ?> 2x, <? echo autoUrl("services/barcode-generator?codetype=Code128&size=180&text=" . $row['ThriveNumber'] . "&print=false"); ?> 3x"
                alt="<? echo $row['ThriveNumber']; ?>"></img>
                <span class="mono"><? echo $row['ThriveNumber']; ?></span>
              </div>
            </div>
            <? } ?>
          </div>
        </div>
      </div>
    </div>

<?php } ?>
  </div>

<?php include BASE_PATH . "views/footer.php"; ?>
