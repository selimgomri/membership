<?php

$disabled = "";

$sql = "SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = '$id' ORDER BY `galas`.`GalaDate` DESC;";
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
      include BASE_PATH . "views/header.php";
      include "galaMenu.php"; ?>
      <div class="container">
        <div class="my-3 p-3 bg-white rounded shadow">
          <h1><?php echo $row['MForename'] . " " . $row['MSurname']; ?></h1>
          <p class="lead">For <?php echo $row['GalaName']; ?>, Closing Date: <?php echo date('j F Y', strtotime($row['ClosingDate'])); ?></p>

          <?php
          $closingDate = new DateTime($row['ClosingDate']);
          $theDate = new DateTime('now');
          $closingDate = $closingDate->format('Y-m-d');
          $theDate = $theDate->format('Y-m-d');

          if ($row['EntryProcessed'] == 1 || ($closingDate <= $theDate)) { ?>
            <div class="alert alert-warning">
              <strong>We've already processed this gala entry, or our closing date has passed</strong> <br>If you need to make changes, contact the Gala Coordinator
            </div>
            <?php $disabled .= " onclick=\"return false;\" ";
          } else { ?>
            <h2>Select Swims</h2>
          <?php } ?>
          <form method="post">

          <?php
            for ($i=0; $i<sizeof($swimsArray); $i++) {
              if ($rowArray[$i] == 1) { ?>
                <div class="row mb-3">
              <?php }
              if ($row[$swimsArray[$i]] == 1) { ?>
                <div class="col-sm-4 col-md-2">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" value="1" class="custom-control-input" id="<?php echo $swimsArray[$i]; ?>" checked "<?php echo $disabled; ?>"  name="<?php echo $swimsArray[$i]; ?>">
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
            } ?>
            <input type="hidden" value="0" name="TimesRequired">
          <?php

          if ($row['EntryProcessed'] == 0 && ($closingDate >= $theDate)) {
            if ($row['GalaFeeConstant'] != 1) { ?>
            <div class="form-group">
              <label for="galaFee">Enter Total</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">&pound;</span>
                </div>
                <input aria-describedby="feeHelp" type="text" id="galaFee" name="galaFee" class="form-control" value="<?= number_format($row['FeeToPay'], 2, ',', '') ?>" required>
              </div>
              <small id="feeHelp" class="form-text text-muted">Sadly we can't automatically calculate the entry fee for this gala so we need you to tell us. If you enter this amount incorrectly or fail to tell us the amount, you may incur extra charges from the club or gala host.</small>
            </div>
            <? } ?>

            <input type="hidden" value="<?php echo $row['EntryID']; ?>" name="entryID">
            <p class="mb-0">
              <button type="submit" id="submit" class="btn btn-outline-dark">Update</button>
            </p>
          <?php } ?>

          </form>
        </div>
      </div>
    <?php
      include BASE_PATH . "views/footer.php";
    }
    else {
      halt(404);
    }

  } else {
    halt(404);
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
    include BASE_PATH . "views/header.php";
    include "galaMenu.php"; ?>
    <div class="container">
      <div class="mb-3 p-3 bg-white rounded shadow">
        <h1><?php echo $row['MForename'] . " " . $row['MSurname']; ?></h1>
        <p class="lead">For <?php echo $row['GalaName']; ?>, Closing Date: <?php echo date('j F Y', strtotime($row['ClosingDate'])); ?></p>

        <?php
        $closingDate = new DateTime($row['ClosingDate']);
        $theDate = new DateTime('now');
        $closingDate = $closingDate->format('Y-m-d');
        $theDate = $theDate->format('Y-m-d');

        if ($row['EntryProcessed'] == 1 || ($closingDate <= $theDate)) { ?>
          <div class="alert alert-warning">
            <strong>We've already processed this gala entry, or our closing date has passed</strong> <br>If you need to make changes, contact the Gala Coordinator directly
          </div>
          <?php $disabled .= " onclick=\"return false;\" ";
        } else { ?>
          <h2>Select Swims</h2>
        <?php } ?>
        <form method="post">

          <? for ($i=0; $i<sizeof($swimsArray); $i++) {
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

        if ($row['EntryProcessed'] == 0 && ($closingDate >= $theDate)) {
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
          <? } ?>

          <input type="hidden" value="<?php echo $row['EntryID']; ?>" name="entryID">
          <p class="mb-0">
            <button type="submit" id="submit" class="btn btn-outline-dark">Update</button>
          </p>
        <?php } ?>
      </form>
    </div>
  </div>
  <?php
  include BASE_PATH . "views/footer.php";
  } else {
    halt(404);
  }
}
?>
