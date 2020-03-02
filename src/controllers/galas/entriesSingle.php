<?php

global $db;

$disabled = "";

$sql = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = ? ORDER BY `galas`.`GalaDate` DESC;");
$sql->execute([
  $idLast
]);
$row = $sql->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
  halt(404);
}

if ($_SESSION['AccessLevel'] == 'Parent' && $row['UserID'] != $_SESSION['UserID']) {
  halt(404);
}

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];
$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
$rowArray = [1, null, null, null, null, 2, 1,  null, 2, 1, null, 2, 1, null, 2, 1, null, null, 2];
$rowArrayText = ["Freestyle", null, null, null, null, 2, "Breaststroke",  null, 2, "Butterfly", null, 2, "Freestyle", null, 2, "Individual Medley", null, null, 2];

$pagetitle = htmlspecialchars($row['MForename'] . " " . $row['MSurname']) . " - " . htmlspecialchars($row['GalaName']);

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <h1><?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'])?></h1>
  <p class="lead">For <?=htmlspecialchars($row['GalaName'])?>, Closing Date: <?=htmlspecialchars(date('j F Y', strtotime($row['ClosingDate'])))?></p>

<?php

$closingDate = new DateTime($row['ClosingDate']);
$theDate = new DateTime('now');
$closingDate = $closingDate->format('Y-m-d');
$theDate = $theDate->format('Y-m-d');

if ($row['EntryProcessed'] == 1 || ($closingDate <= $theDate)) {

?>
  <div class="alert alert-warning">
    <strong>We've already processed this gala entry, or our closing date has passed</strong> <br>If you need to make changes, contact the Gala Coordinator directly
  </div>

<?php
  $disabled .= " onclick=\"return false;\" disabled ";
}
else { ?>
  <h2>Select Swims</h2>
<?php } ?>

<form method="post" action="updategala-action">

<?php for ($i=0; $i<sizeof($swimsArray); $i++) {
  if ($rowArray[$i] == 1) { ?>
    <div class="row mb-3">
  <?php } ?>
    <div class="col-sm-4 col-md-2">
    <div class="custom-control custom-checkbox">
  <input type="checkbox" value="1" class="custom-control-input" id="<?=$swimsArray[$i]?>" <?php if ($row[$swimsArray[$i]] == 1) { ?>checked<?php } ?> <?=$disabled?> name="<?=$swimsArray[$i]?>">
      <label class="custom-control-label" for="<?=$swimsArray[$i]?>">
        <?=$swimsTextArray[$i]?>
      </label>
    </div>
  </div>
  <?php if ($rowArray[$i] == 2) { ?>
    </div>
  <?php }
} ?>

<input type="hidden" value="0" name="TimesRequired">

<?php if ($row['EntryProcessed'] == 0 && ($closingDate >= $theDate)) { ?>
<input type="hidden" value="<?=$row['EntryID']?> name="entryID"><p><button type="submit" id="submit" class="btn btn-outline-dark">Update</button></p>
<?php } ?>

</form>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();