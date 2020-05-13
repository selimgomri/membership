<?php

$tenant = app()->tenant;

// If select sessions, include that code else continue

if (isset($_POST['is-select-sessions']) && bool($_POST['is-select-sessions'])) {
  $id = $_POST['gala'];
  include 'indicate-openness/session-select-post.php';
} else {

  $db = app()->db;

  // Get swimmer info
	$getSwimmer = $db->prepare("SELECT UserID, SquadID FROM members WHERE MemberID = ? AND Tenant = ?");
	$getSwimmer->execute([
    $_POST['swimmer'],
    $tenant->getId()
  ]);
  $swimmerDetails = $getSwimmer->fetch(PDO::FETCH_ASSOC);

  if ($swimmerDetails == null) {
    halt(404);
  }

  $swimmer = $swimmerDetails['UserID'];
  $squad = $swimmerDetails['SquadID'];

	if ($swimmer == null || ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent' && $swimmer != $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'])) {
		halt(404);
  }
  
  // Check galas
  $getGalas = $db->prepare("SELECT COUNT(*) FROM galas WHERE GalaID = ? AND Tenant = ?");
  $getGalas->execute([
    $_POST['gala'],
    $tenant->getId()
  ]);
  if ($getGalas->fetchColumn() == 0) {
    halt(404);
  }

  $swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
  $swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
  $swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];
  $entriesArray = [];
  $memberID = "";
  $galaID = "";
  $timesRequired = "";
  $added = false;
  $content = "";
  $counter = 0;
  $entryCount = -1;

  $galaData = new GalaPrices($db, $_POST['gala']);

  try {

    // JS Should catch existing entries but redirect if one exists
    $getGalaEntries = $db->prepare("SELECT COUNT(*) FROM galaEntries WHERE GalaID = ? AND MemberID = ?");
    $getGalaEntries->execute([
      $_POST['gala'],
      $_POST['swimmer']
    ]);
    if ($getGalaEntries->fetchColumn() > 0) {
      $getGalaEntries = $db->prepare("SELECT EntryID FROM galaEntries WHERE GalaID = ? AND MemberID = ?");
      $getGalaEntries->execute([
        $_POST['gala'],
        $_POST['swimmer']
      ]);
      header("Location: " . autoUrl("galas/entries/" . $getGalaEntries->fetchColumn()));
    } else {

      $allowedSwims = [];
      foreach ($swimsArray as $swim) {
        if ($galaData->getEvent($swim)->isEnabled()) {
          $allowedSwims[] = $swim;
        }
      }

      $totalPrice = 0;

      foreach ($allowedSwims as $swim) {
        if (bool($_POST[$swim])) {
          $entriesArray[] = 1;
          $counter++;
          $price += $galaData->getEvent($swim)->getPrice();
        } else {
          $entriesArray[] = 0;
        }
      }

      $swims = "";
      for ($i=0; $i<sizeof($allowedSwims); $i++) {
        if ($i < (sizeof($allowedSwims)-1)) {
          $swims .= "`" . $allowedSwims[$i] . "`, ";
        }
        else {
          $swims .= "`" . $allowedSwims[$i] . "` ";
        }
      }

      $values = "";
      for ($i=0; $i<sizeof($entriesArray); $i++) {
        if ($i < (sizeof($entriesArray)-1)) {
          $values .= "?, ";
        }
        else {
          $values .= "? ";
        }
      }

      $now = new DateTime('now', new DateTimeZone('Europe/London'));
      $getGalaInformation = $db->prepare("SELECT GalaFee, GalaFeeConstant, GalaName, HyTek, RequiresApproval FROM galas WHERE GalaID = ? AND NOT CoachEnters AND ClosingDate >= ?");
      $getGalaInformation->execute([$_POST['gala'], $now->format('Y-m-d')]);
      $row = $getGalaInformation->fetch(PDO::FETCH_ASSOC);

      if ($row == null) {
        halt(404);
      }

      $fee = (string) (\Brick\Math\BigInteger::of((string) $price))->toBigDecimal()->withPointMovedLeft(2);

      $getNumReps = $db->prepare("SELECT COUNT(*) FROM squadReps WHERE Squad = ?");
      $getNumReps->execute([
        $squad
      ]);
      $numReps = $getNumReps->fetchColumn();

      $hyTek = bool($row['HyTek']);
      $approved = 1;
      if (bool($row['RequiresApproval']) && $numReps > 0) {
        $approved = 0;
      }

      $insert = $db->prepare("INSERT INTO `galaEntries` (EntryProcessed, Charged, `MemberID`, `GalaID`, `Approved`, " . $swims . ", `TimesRequired`, `FeeToPay`) VALUES (?, ?, ?, ?, ?, " . $values . ", ?, ?)");

      $array = array_merge([0, 0, $_POST['swimmer'], $_POST['gala'], $approved], $entriesArray);
      $array = array_merge($array, [0, $fee]);

      $insert->execute($array);

      $entryList = "";
      $get = $db->prepare("SELECT * FROM (galaEntries INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galaEntries.MemberID = ? AND galaEntries.GalaID = ?");
      $get->execute([$_POST['swimmer'], $_POST['gala']]);
      $row = $get->fetch(PDO::FETCH_ASSOC);
      // Print <li>Swim Name</li> for each entry
      for ($y=0; $y<sizeof($swimsArray); $y++) {
        if (bool($row[$swimsArray[$y]])) {
          $entryList .= '<li>' . $swimsTextArray[$y] . ', <em>&pound;' . $galaData->getEvent($swimsArray[$y])->getPriceAsString() . '</em></li>';
        }
      }

      $get = $db->prepare("SELECT members.MForename, members.MSurname, galas.GalaName, galas.GalaFee, galas.GalaFeeConstant, users.EmailAddress, users.Forename, users.Surname FROM (((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) INNER JOIN users ON members.UserID = users.UserID) WHERE galaEntries.MemberID = ? AND galaEntries.GalaID = ?");
      $get->execute([$_POST['swimmer'], $_POST['gala']]);
      $row = $get->fetch(PDO::FETCH_ASSOC);
      $to = $row['Forename'] . " " . $row['Surname'] . "<" . $row['EmailAddress'] . ">";

      $subject = $row['MForename'] . "'s Gala Entry to " . $row['GalaName'];
      $message .= "<p>Here are the swims selected for " . htmlspecialchars($row['MForename'] . " " . $row['MSurname']) . "'s " . htmlspecialchars($row['GalaName']) . " entry.</p>";
      $message .= "<ul>" . $entryList . "</ul>";
      $message .= "<p>You have entered " . (new NumberFormatter("en", NumberFormatter::SPELLOUT))->format($counter) . " events. The <strong>total fee payable is &pound;" . $fee . "</strong>.</p>";
      if (bool($row['HyTek'])) {
        $message .= "<p><strong>This is a HyTek gala.</strong> Please remember to add times for this entry.</p>";
      }
      $message .= '<p>If you have any questions, please contact the ' . htmlspecialchars(app()->tenant->getKey('CLUB_NAME')) . ' gala team as soon as possible.</p>';
      $notify = "INSERT INTO notify (`UserID`, `Status`, `Subject`, `Message`,
      `ForceSend`, `EmailType`) VALUES (?, 'Queued', ?, ?, 1, 'Galas')";
      
      $db = app()->db;
      $email = $db->prepare($notify);
      $email->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID'], $subject, $message]);

      $_SESSION['TENANT-' . app()->tenant->getId()]['SuccessfulGalaEntry'] = [
        "Gala" => $_POST['gala'],
        "Swimmer" => $_POST['swimmer'],
        'HyTek' => $hyTek
      ];

      if ($_SESSION['TENANT-' . app()->tenant->getId()]['AccessLevel'] == 'Parent') {
        header("Location: " . autoUrl("galas/entergala"));
      } else {
        header("Location: " . autoUrl("swimmers/" . $_POST['swimmer'] . "/enter-gala-success"));
      }
    }

  } catch (Exception $e) {
    reportError($e);
    halt(500);
  }
}