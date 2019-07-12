<?php

global $db;

$use_white_background = true;
$disabled = "";

$sql = null;

if ($_SESSION['AccessLevel'] == "Parent") {
  $sql = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = ? AND members.UserID = ? ORDER BY `galas`.`GalaDate` DESC;");
  $sql->execute([$id, $_SESSION['UserID']]);
} else {
  $sql = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = ? ORDER BY `galas`.`GalaDate` DESC;");
  $sql->execute([$id]);
}
$row = $sql->fetch(PDO::FETCH_ASSOC);

if ($row == null) {
  halt(404);
}

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];
$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
$rowArray = [1, null, null, null, null, 2, 1,  null, 2, 1, null, 2, 1, null, 2, 1, null, null, 2];
$rowArrayText = ["Freestyle", null, null, null, null, 2, "Breaststroke",  null, 2, "Butterfly", null, 2, "Freestyle", null, 2, "Individual Medley", null, null, 2];

$disabled = "";

$pagetitle = htmlspecialchars($row['MForename'] . " " . $row['MSurname'] . " - " . $row['GalaName'] . "");
include BASE_PATH . "views/header.php";
include "galaMenu.php"; ?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas")?>">Galas</a></li>
      <li class="breadcrumb-item"><a href="<?=autoUrl("galas/entries")?>">My entries</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars($row['MForename'][0] . $row['MSurname'][0])?> (<?=htmlspecialchars($row['GalaName'])?>)</li>
    </ol>
  </nav>
    <div>
      <h1><?=htmlspecialchars($row['MForename'] . " " . $row['MSurname'][0])?>'s entry for <?=htmlspecialchars($row['GalaName'])?></h1>
      <p class="lead">For <?=htmlspecialchars($row['GalaName'])?>, Closing Date: <?=date('j F Y', strtotime($row['ClosingDate']))?></p>

      <?php if (isset($_SESSION['UpdateError']) && $_SESSION['UpdateError']) { ?>
      <div class="alert alert-danger">A database error occured which prevented us saving the changes.</div>
      <?php unset($_SESSION['UpdateError']); } ?>

      <?php if (isset($_SESSION['UpdateSuccess']) && $_SESSION['UpdateSuccess']) { ?>
      <div class="alert alert-success">All changes to your gala entry have been saved.</div>
      <?php unset($_SESSION['UpdateSuccess']); } ?>

      <?php
      $closingDate = new DateTime($row['ClosingDate'], new DateTimeZone('Europe/London'));
      $theDate = new DateTime('now', new DateTimeZone('Europe/London'));
      $closingDate = $closingDate->format('Y-m-d');
      $theDate = $theDate->format('Y-m-d');

      if ($row['Paid'] || $row['EntryProcessed'] || ($closingDate < $theDate) || $row['Locked']) { ?>
        <div class="alert alert-warning">
          <strong>We've already processed this gala entry, our closing date has passed or you have already paid</strong> <br>We can't let you make any changes here. Contact the Gala Administrator directly.
        </div>
        <?php $disabled .= " onclick=\"return false;\" ";
      } else { ?>
        <h2>Select Swims</h2>
      <?php } ?>
      <form method="post">

        <?php for ($i=0; $i<sizeof($swimsArray); $i++) {
        if ($rowArray[$i] == 1) { ?>
          <div class="row mb-3">
        <?php }
        if ($row[$swimsArray[$i]] == 1) { ?>
          <div class="col-sm-4 col-md-2">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1" class="custom-control-input" id="<?php echo $swimsArray[$i]; ?>" checked <?php echo $disabled; ?>  name="<?php echo $swimsArray[$i]; ?>">
              <label class="custom-control-label" for="<?php echo $swimsArray[$i]; ?>"><?php echo $swimsTextArray[$i]; ?></label>
            </div>
          </div>
        <?php }
        else { ?>
          <div class="col-sm-4 col-md-2">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1" class="custom-control-input" id="<?php echo $swimsArray[$i]; ?>" <?php echo $disabled; ?>  name="<?php echo $swimsArray[$i]; ?>">
              <label class="custom-control-label" for="<?php echo $swimsArray[$i]; ?>"><?php echo $swimsTextArray[$i]; ?></label>
            </div>
          </div>
        <?php }
        if ($rowArray[$i] == 2) { ?>
          </div>
        <?php }
      }

      if (!($closingDate < $theDate) && $row['EntryProcessed'] != 1 && $row['Paid'] != 1 && !$row['Locked']) {
        if ($row['GalaFeeConstant'] != 1) { ?>
        <div class="form-group">
          <label for="galaFee">Enter Total</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">&pound;</span>
            </div>
            <input aria-describedby="feeHelp" type="text" id="galaFee" name="galaFee" class="form-control" value="<?= number_format($row['FeeToPay'], 2, '.', '') ?>" required>
          </div>
          <small id="feeHelp" class="form-text text-muted">Sadly we can't automatically calculate the entry fee for this gala so we need you to tell us. If you enter this amount incorrectly or fail to tell us the amount, you may incur extra charges from the club or gala host.</small>
        </div>
        <?php } ?>

        <input type="hidden" value="<?=htmlspecialchars($row['EntryID'])?>" name="entryID">
        <p>
          <button type="submit" id="submit" class="btn btn-success">Update</button>
        </p>
      <?php } ?>
    </form>
  </div>
</div>
<?php
include BASE_PATH . "views/footer.php";
