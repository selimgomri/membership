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

// Arrays of swims used to check whever to print the name of the swim entered
// BEWARE This is in an order to ease inputting data into SportSystems, contrary to these arrays in other files
$swimsArray = [
  '50Free' => '50&nbsp;Free',
  '100Free' => '100&nbsp;Free',
  '200Free' => '200&nbsp;Free',
  '400Free' => '400&nbsp;Free',
  '800Free' => '800&nbsp;Free',
  '1500Free' => '1500&nbsp;Free',
  '50Back' => '50&nbsp;Back',
  '100Back' => '100&nbsp;Back',
  '200Back' => '200&nbsp;Back',
  '50Breast' => '50&nbsp;Breast',
  '100Breast' => '100&nbsp;Breast',
  '200Breast' => '200&nbsp;Breast',
  '50Fly' => '50&nbsp;Fly',
  '100Fly' => '100&nbsp;Fly',
  '200Fly' => '200&nbsp;Fly',
  '100IM' => '100&nbsp;IM',
  '150IM' => '150&nbsp;IM',
  '200IM' => '200&nbsp;IM',
  '400IM' => '400&nbsp;IM'
];

$strokeCounts = [
  'Free' => 0,
  'Back' => 0,
  'Breast' => 0,
  'Fly' => 0,
  'IM' => 0
];
$distanceCounts = [
  '50' => 0,
  '100' => 0,
  '150' => 0,
  '200' => 0,
  '400' => 0,
  '800' => 0,
  '1500' => 0
];
$chartColours = chartColours(5);
$countEntries = [];
$countEntriesEvents = [];
$countEntriesCount = [];
$countEntriesColours = [];
foreach ($swimsArray as $col => $name) {
  $getCount = $db->prepare("SELECT COUNT(*) FROM galaEntries WHERE MemberID = ? AND `" . $col . "` = 1");
  $getCount->execute([$id]);
  $count = $getCount->fetchColumn();
  if ($count > 0) {
    $countEntries[$col]['Name'] = $name;
    $countEntriesEvents[] = html_entity_decode($name);
    $countEntries[$col]['Event'] = $col;
    $countEntries[$col]['Stroke'] = preg_replace("/[^a-zA-Z]+/", "", $col);
    $countEntries[$col]['Distance'] = preg_replace("/[^0-9]/", '', $col);
    $countEntries[$col]['Count'] = $count;
    $countEntriesCount[] = $count;
    $strokeCounts[$countEntries[$col]['Stroke']] += $countEntries[$col]['Count'];
    $distanceCounts[$countEntries[$col]['Distance']] += $countEntries[$col]['Count'];
    if ($countEntries[$col]['Stroke'] == 'Free') {
      $countEntriesColours[] = $chartColours[0];
    } else if ($countEntries[$col]['Stroke'] == 'Back') {
      $countEntriesColours[] = $chartColours[1];
    } else if ($countEntries[$col]['Stroke'] == 'Breast') {
      $countEntriesColours[] = $chartColours[2];
    } else if ($countEntries[$col]['Stroke'] == 'Fly') {
      $countEntriesColours[] = $chartColours[3];
    } else if ($countEntries[$col]['Stroke'] == 'IM') {
      $countEntriesColours[] = $chartColours[4];
    }
  }
}

$strokeCountsData = array_values($strokeCounts);

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
    <?php if (sizeof($countEntries) > 0) { ?>
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
      <?php if ((!bool($rowSwim['Website']) || !bool($rowSwim['Social']) ||
      !bool($rowSwim['Noticeboard']) || !bool($rowSwim['FilmTraining']) ||
      !bool($rowSwim['ProPhoto'])) && ($age < 18)) { ?>
        <p>There are limited photography permissions for this swimmer</p>
        <ul> <?php
        if (!bool($rowSwim['Website'])) { ?>
          <li>Photos <strong>must not</strong> be taken of this swimmer for our
          website</li><?php
        }
        if (!bool($rowSwim['Social'])) { ?>
          <li>Photos <strong>must not</strong> be taken of this swimmer for our
          social media</li><?php
        }
        if (!bool($rowSwim['Noticeboard'])) { ?>
          <li>Photos <strong>must not</strong> be taken of this swimmer for our
          noticeboard</li><?php
        }
        if (!bool($rowSwim['FilmTraining'])) { ?>
          <li>This swimmer <strong>must not</strong> be filmed for the purposes
          of training</li><?php
        }
        if (!bool($rowSwim['ProPhoto'])) { ?>
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
        <h2 class="">Best Times</h2>
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

      <!-- STATS SECTION -->

      <h2>Gala entry statistics</h2>

      <div class="row">
        <div class="col-lg-8">
          <canvas id="eventEntries" class="mb-3"></canvas>
        </div>
        <div class="col-lg-4">
          <canvas id="strokeEntries" class="mb-3"></canvas>
        </div>
      </div>

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
      
      <div class="row">
        <div class="col-lg-8">
          <h2>Leave the Club</h2>
          <p class="lead">
            You can leave the club at any time.
          </p>
          <p>
            As we charge you on the first day of each month for that upcoming
            month, it makes sense for our systems to remove your swimmer on the
            first. It doesn't matter if you stop attending before this date.
          </p>

          <div class="alert alert-danger">
            <p>
              If <?=htmlspecialchars($rowSwim["MForename"])?> won't be returning to
              the club on or after 1 <?=date("F Y", strtotime('+1 month'))?>, press the button below to confirm.
            </p>
            <p>
              This will update our systems and automatically remove
              <?=htmlspecialchars($rowSwim["MForename"])?> from our registers and
              billing systems on this date.
            </p>
            <p>
              If you later decide you want to stay at <?=htmlspecialchars(env('CLUB_NAME'))?> then you will need to contact club staff to have the move cancelled.
            </p>
            <p class="mb-0">
              <a class="btn btn-danger" href="<?=autoUrl("swimmers/" . $id . "/leaveclub")?>">Leave the club</a>
            </p>
          </div>
        </div>
      </div>
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
        
        <h2 class="">Membership information</h2>
        <p class="lead">All members are issued with a Swim England (ASA) number and a club registration number.</p>
        <p>You will probably never need your club membership number but your Swim England number is very important.</p>
        <div class="row">
          <div class="<?php echo $col; ?>">
            <div class="text-center border p-2 bg-white">
              <span class="lead mb-2">Swim England Number</span>
              <img class="img-fluid mx-auto d-block"
              src="<?=htmlspecialchars(autoUrl("services/barcode-generator?codetype=Code128a&size=60&text=" . $rowSwim['ASANumber'] . "&print=false"))?>"
              srcset="<?=htmlspecialchars(autoUrl("services/barcode-generator?codetype=Code128a&size=120&text=" . $rowSwim['ASANumber'] . "&print=false"))?> 2x, <?=htmlspecialchars(autoUrl("services/barcode-generator?codetype=Code128a&size=180&text=" . $rowSwim['ASANumber'] . "&print=false"))?> 3x"
              alt="<?=htmlspecialchars($rowSwim['ASANumber'])?>"></img>
              <span class="mono"><?=htmlspecialchars($rowSwim['ASANumber'])?></span>
            </div>
            <span class="d-block d-sm-none mb-3"></span>
          </div>
          <div class="<?php echo $col; ?>">
            <div class="text-center border p-2 bg-white">
              <span class="lead mb-2"><?=htmlspecialchars(env('CLUB_SHORT_NAME'))?> Number</span>
              <img class="img-fluid mx-auto d-block"
              src="<?=htmlspecialchars(autoUrl("services/barcode-generator?codetype=Code128&size=60&text=" . urlencode(env('ASA_CLUB_CODE')) . $rowSwim['MemberID'] . "&print=false"))?>"
              srcset="<?=htmlspecialchars(autoUrl("services/barcode-generator?codetype=Code128&size=120&text=" . urlencode(env('ASA_CLUB_CODE')) . $rowSwim['MemberID'] . "&print=false"))?> 2x, <?=htmlspecialchars(autoUrl("services/barcode-generator?codetype=Code128&size=180&text=" . urlencode(env('ASA_CLUB_CODE')) . $rowSwim['MemberID'] . "&print=false"))?> 3x"
              alt="<?=htmlspecialchars(env('ASA_CLUB_CODE') . $rowSwim['MemberID'])?>"></img>
              <span class="mono"><?=htmlspecialchars(env('ASA_CLUB_CODE') . $rowSwim['MemberID'])?></span>
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

<?php if (sizeof($countEntries) > 0) { ?>
<script src="<?=autoUrl("public/js/Chart.min.js")?>"></script>
<script>
var ctx = document.getElementById('eventEntries').getContext('2d');
var chart = new Chart(ctx, {
  // The type of chart we want to create
  type: 'bar',

  // The data for our dataset
  data: {
    labels: <?=json_encode($countEntriesEvents)?>,
    datasets: [{
      label: <?=json_encode($rowSwim['MForename'] . " " . $rowSwim['MSurname'])?>,
      data: <?=json_encode($countEntriesCount)?>,
      backgroundColor: <?=json_encode($countEntriesColours)?>,
    }],
  },

  // Configuration options go here
  options: {
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true,
          precision: 0,
        }
      }]
    }
  }
});
</script>

<script>
var ctx = document.getElementById('strokeEntries').getContext('2d');
var chart = new Chart(ctx, {
  // The type of chart we want to create
  type: 'pie',

  // The data for our dataset
  data: {
    labels: <?=json_encode(['Free', 'Back', 'Breast', 'Fly', 'IM'])?>,
    datasets: [{
      label: <?=json_encode(html_entity_decode($gala['GalaName']))?>,
      data: <?=json_encode($strokeCountsData)?>,
      backgroundColor: <?=json_encode($chartColours)?>,
    }],
  },

  // Configuration options go here
  // options: {}
});
</script>
<?php } ?>

<?php include BASE_PATH . "views/footer.php"; ?>
