<?php

$systemInfo = app()->system;
$leavers = $systemInfo->getSystemOption('LeaversSquad');

$db = app()->db;
$getInfo = $db->prepare("SELECT members.UserID, members.MemberID, members.MForename, members.MMiddleNames,
members.MSurname, users.EmailAddress, members.ASANumber, members.ASACategory,
members.ClubPays, squads.SquadName, squads.SquadFee, squads.SquadCoach,
squads.SquadTimetable, squads.SquadCoC, members.DateOfBirth, members.Gender,
members.OtherNotes, members.AccessKey, memberPhotography.Website,
memberPhotography.Social, memberPhotography.Noticeboard,
memberPhotography.FilmTraining, memberPhotography.ProPhoto,
memberMedical.Conditions, memberMedical.Allergies, memberMedical.Medication, members.Country
FROM ((((members INNER JOIN users ON members.UserID = users.UserID) INNER JOIN
squads ON members.SquadID = squads.SquadID) LEFT JOIN `memberPhotography` ON
members.MemberID = memberPhotography.MemberID) LEFT JOIN `memberMedical` ON
members.MemberID = memberMedical.MemberID) WHERE members.MemberID = ?");
$getInfo->execute([$id]);

$rowSwim = $getInfo->fetch(PDO::FETCH_ASSOC);

if ($rowSwim == null) {
  halt(404);
}

if ($_SESSION['AccessLevel'] == 'Parent' && $_SESSION['UserID'] != $rowSwim['UserID']) {
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

// Get all countries
$countries = getISOAlpha2CountriesWithHomeNations();

$country = '';
if (isset($countries[$rowSwim['Country']])) {
  $country = $countries[$rowSwim['Country']];
}

?>

<?php include BASE_PATH . "views/header.php"; ?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("members"))?>">Members</a></li>
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl("members/" . $id))?>">#<?=htmlspecialchars($id)?></a></li>
      <li class="breadcrumb-item active" aria-current="page">Legacy PBs</li>
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
      <small>Legacy Personal Best Times</small>
    </h1>
  </div>

  <div class="alert alert-info">
    <p class="mb-0">
      <strong>This page is deprecated</strong>
    </p>
    <p class="mb-0">
      We've left it here for clubs still using the legacy PB system. Your club should migrate to the new times system as soon as possible.
    </p>
  </div>

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
  <table class="table table-sm table-borderless table-striped">
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

  <p>
    <a href="<?=htmlspecialchars(autoUrl("members/" . $id . "/edit-times"))?>" class="btn btn-success">
      Edit times
    </a>
  </p>

<?php $footer = new \SCDS\Footer();
$footer->render(); ?>
