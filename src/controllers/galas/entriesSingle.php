<?php

$disabled = "";

$sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = '$idLast' ORDER BY `galas`.`GalaDate` DESC;";
$result = mysqli_query($link, $sql);
$count = mysqli_num_rows($result);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

if ($_SESSION['AccessLevel'] == "Parent") {
  if ($_SESSION['UserID'] == $row['UserID']) {
    if ($count == 1) {
      $swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
      $swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];
      $swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
      $rowArray = [1, null, null, null, null, 2, 1,  null, 2, 1, null, 2, 1, null, 2, 1, null, null, 2];
      $rowArrayText = ["Freestyle", null, null, null, null, 2, "Breaststroke",  null, 2, "Butterfly", null, 2, "Freestyle", null, 2, "Individual Medley", null, null, 2];

      $pagetitle = $row['MForename'] . " " . $row['MSurname'] . " - " . $row['GalaName'] . "";
      $title = $row['MForename'] . " " . $row['MSurname'];
      $content = "<p class=\"lead\">For " . $row['GalaName'] . ", Closing Date: " . date('j F Y', strtotime($row['ClosingDate'])) . "</p>";

      $closingDate = new DateTime($row['ClosingDate']);
      $theDate = new DateTime('now');
      $closingDate = $closingDate->format('Y-m-d');
      $theDate = $theDate->format('Y-m-d');

      if ($row['EntryProcessed'] == 1 || ($closingDate <= $theDate)) {
        $content .= "<div class=\"alert alert-warning\"><strong>We've already processed this gala entry, or our closing date has passed</strong> <br>If you need to make changes, contact the Gala Coordinator directly</div>";
        $disabled .= " onclick=\"return false;\" ";
      }
      else {
        $content .= "<h2>Select Swims</h2>";
      }
      $content .= "<form method=\"post\" action=\"updategala-action\">";

      for ($i=0; $i<sizeof($swimsArray); $i++) {
        if ($rowArray[$i] == 1) {
          $content .= "<div class=\"row mb-3\">";
        }
        if ($row[$swimsArray[$i]] == 1) {
          $content .= "
          <div class=\"col-sm-4 col-md-2\">
          <div class=\"custom-control custom-checkbox\">
            <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"" . $swimsArray[$i] . "\" checked " . $disabled . " \  name=\"" . $swimsArray[$i] . "\">
            <label class=\"custom-control-label\" for=\"" . $swimsArray[$i] . "\">" . $swimsTextArray[$i] . "</label>
          </div>
          </div>";
        }
        else {
          $content .= "
          <div class=\"col-sm-4 col-md-2\">
          <div class=\"custom-control custom-checkbox\">
            <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"" . $swimsArray[$i] . "\" " . $disabled . "  name=\"" . $swimsArray[$i] . "\">
            <label class=\"custom-control-label\" for=\"" . $swimsArray[$i] . "\">" . $swimsTextArray[$i] . "</label>
          </div>
          </div>";
        }
        if ($rowArray[$i] == 2) {
          $content .= "</div>";
        }
      }
      $content .= '<input type="hidden" value="0" name="TimesRequired">';

      if ($row['EntryProcessed'] == 0 && ($closingDate >= $theDate)) {
        $content .= "
        <input type=\"hidden\" value=\"" . $row['EntryID'] . "\" name=\"entryID\"><p><button type=\"submit\" id=\"submit\" class=\"btn btn-outline-dark\">Update</button></p>";
      }

      $content .= "</form>";
    }
    else {
      $pagetitle = "No entries found";
      $title = "No entries found";
      $content = "<p class=\"lead\">Try going <a href=\"" . autoUrl('galas/entries') . "\">back to entries</a>.</p>";
    }

  }
}
else {
  if ($count == 1) {
    $swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
    $swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];
    $swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
    $rowArray = [1, null, null, null, null, 2, 1,  null, 2, 1, null, 2, 1, null, 2, 1, null, null, 2];
    $rowArrayText = ["Freestyle", null, null, null, null, 2, "Breaststroke",  null, 2, "Butterfly", null, 2, "Freestyle", null, 2, "Individual Medley", null, null, 2];

    $pagetitle = $row['MForename'] . " " . $row['MSurname'] . " - " . $row['GalaName'] . "";
    $title = $row['MForename'] . " " . $row['MSurname'];
    $content = "<p class=\"lead\">For " . $row['GalaName'] . ", Closing Date: " . date('j F Y', strtotime($row['ClosingDate'])) . "</p>";

    $closingDate = new DateTime($row['ClosingDate']);
    $theDate = new DateTime('now');
    $closingDate = $closingDate->format('Y-m-d');
    $theDate = $theDate->format('Y-m-d');

    if ($row['EntryProcessed'] == 1 || ($closingDate <= $theDate)) {
      $content .= "<div class=\"alert alert-warning\"><strong>We've already processed this gala entry, or our closing date has passed</strong> <br>If you need to make changes, contact the Gala Coordinator directly</div>";
      $disabled .= " onclick=\"return false;\" ";
    }
    elseif ($row['TimesRequired'] == 1) {
      $content .= "<div class=\"alert alert-warning\"><strong>Making Changes for a HyTek Gala</strong> <br>To make changes, remove the minutes, seconds and hundreths for a swim, or add the relevant information, then press update</div>";
      //$disabled .= " disabled ";
    }
    else {
      $content .= "<h2>Select Swims</h2>";
    }
    $content .= "<form method=\"post\" action=\"updategala-action\">";

    for ($i=0; $i<sizeof($swimsArray); $i++) {
      if ($rowArray[$i] == 1) {
        $content .= "<div class=\"row mb-3\">";
      }
      if ($row[$swimsArray[$i]] == 1) {
        $content .= "
        <div class=\"col-sm-4 col-md-2\">
        <div class=\"custom-control custom-checkbox\">
          <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"" . $swimsArray[$i] . "\" checked " . $disabled . " \  name=\"" . $swimsArray[$i] . "\">
          <label class=\"custom-control-label\" for=\"" . $swimsArray[$i] . "\">" . $swimsTextArray[$i] . "</label>
        </div>
        </div>";
      }
      else {
        $content .= "
        <div class=\"col-sm-4 col-md-2\">
        <div class=\"custom-control custom-checkbox\">
          <input type=\"checkbox\" value=\"1\" class=\"custom-control-input\" id=\"" . $swimsArray[$i] . "\" " . $disabled . "  name=\"" . $swimsArray[$i] . "\">
          <label class=\"custom-control-label\" for=\"" . $swimsArray[$i] . "\">" . $swimsTextArray[$i] . "</label>
        </div>
        </div>";
      }
      if ($rowArray[$i] == 2) {
        $content .= "</div>";
      }
    }
    $content .= '<input type="hidden" value="0" name="TimesRequired">';

    if ($row['EntryProcessed'] == 0 && ($closingDate >= $theDate)) {
      $content .= "
      <input type=\"hidden\" value=\"" . $row['EntryID'] . "\" name=\"entryID\"><p><button type=\"submit\" id=\"submit\" class=\"btn btn-outline-dark\">Update</button></p>";
    }

    $content .= "</form>";
  }
  else {
    $pagetitle = "No entries found";
    $title = "No entries found";
    $content = "<p class=\"lead\">Try going <a href=\"" . autoUrl('galas/entries') . "\">back to entries</a>.</p>";
  }

}


?>
