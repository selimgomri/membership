<?php

global $db;

$use_white_background = true;
$disabled = "";

$numFormat = new NumberFormatter("en", NumberFormatter::SPELLOUT);

$sql = null;

if ($_SESSION['AccessLevel'] == "Parent") {
  $sql = $db->prepare("SELECT * FROM ((((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) LEFT JOIN stripePayments ON galaEntries.StripePayment = stripePayments.ID) LEFT JOIN stripePayMethods ON stripePayMethods.ID = stripePayments.Method) WHERE `EntryID` = ? AND members.UserID = ? ORDER BY `galas`.`GalaDate` DESC;");
  $sql->execute([$id, $_SESSION['UserID']]);
} else {
  $sql = $db->prepare("SELECT * FROM ((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE `EntryID` = ? ORDER BY `galas`.`GalaDate` DESC;");
  $sql->execute([$id]);
}
$row = $sql->fetch(PDO::FETCH_ASSOC);

//pre($row);

if ($row == null) {
  halt(404);
}

$galaData = new GalaPrices($db, $row["GalaID"]);

$closingDate = new DateTime($row['ClosingDate'], new DateTimeZone('Europe/London'));
$theDate = new DateTime('now', new DateTimeZone('Europe/London'));

$locked = false;
if (bool($row['Charged']) || bool($row['EntryProcessed']) || ($closingDate < $theDate && ($_SESSION['AccessLevel'] != 'Admin' && $_SESSION['AccessLevel'] != 'Galas')) || bool($row['Locked'])) {
  $locked = true;
}

$closingDate = $closingDate->format('Y-m-d');
$theDate = $theDate->format('Y-m-d');

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
      <p class="lead">Closing date: <?=date('j F Y', strtotime($row['ClosingDate']))?></p>

      <?php if (isset($_SESSION['UpdateError']) && $_SESSION['UpdateError']) { ?>
      <div class="alert alert-danger">A database error occured which prevented us saving the changes.</div>
      <?php unset($_SESSION['UpdateError']); } ?>

      <?php if (isset($_SESSION['UpdateSuccess']) && $_SESSION['UpdateSuccess']) { ?>
      <div class="alert alert-success">All changes to your gala entry have been saved.</div>
      <?php unset($_SESSION['UpdateSuccess']); } ?>

      <?php if (bool($row['HyTek'])) { ?>
      <h2>Provide times</h2>
      <p class="lead">
        Times must be provided manually at this gala.
      </p>

      <p>
        We're sorry about the inconvenience this causes.
      </p>

      <p>
        <a href="<?=autoUrl("galas/entries/" . $row['EntryID'] . "/manual-time")?>" class="btn btn-primary">
          Provide times
        </a>
      </p>
      <?php } ?>

      <?php if ($locked) { ?>
        <div class="alert alert-warning">
          <strong>We've already processed this gala entry, our closing date has passed or you have already paid</strong> <br>If there are any changes you need to make, please contact the Gala Administrator directly.
        </div>
        <?php $disabled .= " onclick=\"return false;\" disabled ";
      } ?>
      <h2>Select Swims</h2>
      <p class="lead">Select the events to enter at this gala</p>
      <form method="post">

        <div id="gala-checkboxes">
        <?php for ($i=0; $i<sizeof($swimsArray); $i++) {
        if ($rowArray[$i] == 1) { ?>
          <div class="row mb-3">
        <?php }
        if ($galaData->getEvent($swimsArray[$i])->isEnabled()) {
          if ($row[$swimsArray[$i]] == 1) { ?>
            <div class="col-sm-4 col-md-2">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" value="1" class="custom-control-input" id="<?php echo $swimsArray[$i]; ?>" checked <?php echo $disabled; ?>  name="<?php echo $swimsArray[$i]; ?>" data-event-fee="<?=htmlspecialchars($galaData->getEvent($swimsArray[$i])->getPrice())?>">
                <label class="custom-control-label" for="<?php echo $swimsArray[$i]; ?>"><?php echo $swimsTextArray[$i]; ?></label>
              </div>
            </div>
          <?php } else { ?>
            <div class="col-sm-4 col-md-2">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" value="1" class="custom-control-input" id="<?php echo $swimsArray[$i]; ?>" <?php echo $disabled; ?>  name="<?php echo $swimsArray[$i]; ?>" data-event-fee="<?=htmlspecialchars($galaData->getEvent($swimsArray[$i])->getPrice())?>">
                <label class="custom-control-label" for="<?php echo $swimsArray[$i]; ?>"><?php echo $swimsTextArray[$i]; ?></label>
              </div>
            </div>
          <?php }
        }
        if ($rowArray[$i] == 2) { ?>
          </div>
        <?php }
        } ?>
        </div>

      <?php if (!$locked) { ?>
        <p>
          Your entry fee is <strong>&pound;<span id="total-field" data-total="<?=htmlspecialchars((string) (\Brick\Math\BigDecimal::of((string) $row['FeeToPay'])->withPointMovedRight(2)->toInt()))?>"><?=htmlspecialchars((string) (\Brick\Math\BigDecimal::of((string) $row['FeeToPay'])->toScale(2)))?></span></strong>.
        </p>

        <input type="hidden" value="<?=htmlspecialchars($row['EntryID'])?>" name="entryID">
        <p>
          <button type="submit" id="submit" class="btn btn-success">Update</button>
        </p>
      <?php } ?>

      <?php if ($row['StripePayment'] != null && bool($row['Paid'])) {
        $getEntryPaymentCount = $db->prepare("SELECT COUNT(*) FROM galaEntries WHERE galaEntries.StripePayment = ?");
        $getEntryPaymentCount->execute([$row['StripePayment']]);
        $countPaid = $getEntryPaymentCount->fetchColumn(); ?>
        <h2>Payment</h2>
        <p class="lead">You paid for this gala entry by card</p>
        <div class="row align-items-center mb-2">
          <div class="col-auto">
            <img src="<?=autoUrl("public/img/stripe/" . $row['Brand'] . ".png")?>" srcset="<?=autoUrl("public/img/stripe/" . $row['Brand'] . "@2x.png")?> 2x, <?=autoUrl("public/img/stripe/" . $row['Brand'] . "@3x.png")?> 3x" style="width:40px;"> <span class="sr-only"><?=htmlspecialchars(getCardBrand($row['Brand']))?></span>
          </div>
          <div class="col-auto">
            <p class="my-0">
              &#0149;&#0149;&#0149;&#0149; <?=htmlspecialchars($row['Last4'])?> 
            </p>
          </div>
        </div>
        <?php if ($countPaid > 1) { ?>
        <p>
          You paid for <?=htmlspecialchars($numFormat->format($countPaid))?> gala entries as part of this transaction.
        </p>
        <?php } ?>
        <p>
          <a href="<?=htmlspecialchars(autoUrl("payments/card-transactions/" . $row['StripePayment']))?>" class="btn btn-primary">View transaction</a>
        </p>
      <?php } ?>
    </form>
  </div>
</div>

<?php if (!$locked) { ?>
<script src="<?=autoUrl("public/js/numerical/bignumber.min.js")?>"></script>
<script src="<?=autoUrl("public/js/gala-entries/EditEntry.js")?>"></script>
<?php } ?>

<?php
include BASE_PATH . "views/footer.php";
