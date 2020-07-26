<?php

$db = app()->db;
$tenant = app()->tenant;

$canPayByCard = false;
if (getenv('STRIPE') && $tenant->getStripeAccount() && $tenant->getBooleanKey('GALA_CARD_PAYMENTS_ALLOWED')) {
  $canPayByCard = true;
}

$swimsArray = ['50Free','100Free','200Free','400Free','800Free','1500Free','50Breast','100Breast','200Breast','50Fly','100Fly','200Fly','50Back','100Back','200Back','100IM','150IM','200IM','400IM',];
$swimsTextArray = ['50 Free','100 Free','200 Free','400 Free','800 Free','1500 Free','50 Breast','100 Breast','200 Breast','50 Fly','100 Fly','200 Fly','50 Back','100 Back','200 Back','100 IM','150 IM','200 IM','400 IM',];
$swimsTimeArray = ['50FreeTime','100FreeTime','200FreeTime','400FreeTime','800FreeTime','1500FreeTime','50BreastTime','100BreastTime','200BreastTime','50FlyTime','100FlyTime','200FlyTime','50BackTime','100BackTime','200BackTime','100IMTime','150IMTime','200IMTime','400IMTime',];

$entryList = "";
$get = $db->prepare("SELECT * FROM (galaEntries INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) WHERE galaEntries.MemberID = ? AND galaEntries.GalaID = ? AND galas.Tenant = ?");
$get->execute([
  $_SESSION['TENANT-' . app()->tenant->getId()]['SuccessfulGalaEntry']['Swimmer'],
  $_SESSION['TENANT-' . app()->tenant->getId()]['SuccessfulGalaEntry']['Gala'],
  $tenant->getId()
]);
$row = $get->fetch(PDO::FETCH_ASSOC);
// Print <li>Swim Name</li> for each entry
for ($y=0; $y<sizeof($swimsArray); $y++) {
  if ($row[$swimsArray[$y]] == 1) {
    $entryList .= "<li>" . $swimsTextArray[$y] . "</li>";
  }
}

$get = $db->prepare("SELECT members.MForename, members.MSurname, galas.GalaName, galas.GalaFee, galas.GalaFeeConstant, users.EmailAddress, users.Forename, users.Surname, FeeToPay, EntryID FROM (((galaEntries INNER JOIN members ON galaEntries.MemberID = members.MemberID) INNER JOIN galas ON galaEntries.GalaID = galas.GalaID) INNER JOIN users ON members.UserID = users.UserID) WHERE galaEntries.MemberID = ? AND galaEntries.GalaID = ? AND galas.Tenant = ?");
$get->execute([
  $_SESSION['TENANT-' . app()->tenant->getId()]['SuccessfulGalaEntry']['Swimmer'],
  $_SESSION['TENANT-' . app()->tenant->getId()]['SuccessfulGalaEntry']['Gala'],
  $tenant->getId()
]);
$row = $get->fetch(PDO::FETCH_ASSOC);

$pagetitle = htmlspecialchars($row['MForename']) . "'s Gala Entry to " . htmlspecialchars($row['GalaName']);

include BASE_PATH . "views/header.php";

?>

<div class="container">
  <div class="row">
    <div class="col-md-8">
      <h1>
        <?=htmlspecialchars($row['MForename'])?>'s Gala Entry to <?=htmlspecialchars($row['GalaName'])?>
      </h1>
      <p class="lead">
        Here are the details...
      </p>
      <div class="alert alert-success">
        <p class="mb-0">
          An email confirmation is on it's way to
        </p>
        <p class="text-truncate mb-0 mono">
          <?=htmlspecialchars($row['EmailAddress'])?>
        </p>
      </div>

      <h2>Swims</h2>

      <p>
        You have entered <?=htmlspecialchars($row['MForename'])?> into;
      </p>

      <ul>
        <?=$entryList?>
      </ul>

      <p>
        The <strong>total fee payable is &pound;<?=htmlspecialchars((string) (\Brick\Math\BigDecimal::of((string) $row['FeeToPay'])->toScale(2)))?></strong>. You can view prices for each swim online.
      </p>

      <h2>Next steps</h2>
      <p class="lead">
        What do you need to do now?
      </p>

      <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['SuccessfulGalaEntry']['HyTek']) { ?>
      <div class="cell">
        <h3>Provide entry times</h3>
        <p>
          As this is a HyTek gala, we need you to provide times for each of your swimmers.
        </p>

        <p>
          <a href="#why">Why is this?</a>
        </p>

        <p class="mb-0">
          <a href="<?=autoUrl("galas/entries/" . $row['EntryID'] . "/manual-time")?>" class="btn btn btn-primary">Provide times</a>
        </p>
      </div>

      <?php } ?>

      <div class="cell">
        <h3>Enter another gala</h3>

        <p>Need to enter another gala?</p>

        <p class="mb-0">
          <a href="<?=autoUrl("galas/entergala")?>" class="btn btn-primary">
            Enter a gala
          </a>
        </p>
      </div>

      <?php if ($canPayByCard) { ?>
      <div class="cell">
        <h3>Pay by card</h3>
        <p>
          Would you like to pay for this gala entry by card now?
        </p>

        <div class="accepted-network-logos mb-3">
          <p>
            We proudly accept all major credit and debit cards. We also accept Apple Pay and Google Pay.
          </p>
          <p>
            <img class="apple-pay-row" src="<?=autoUrl("public/img/stripe/apple-pay-mark.svg")?>" aria-hidden="true"><img class="google-pay-row" src="<?=autoUrl("public/img/stripe/google-pay-mark.svg")?>" aria-hidden="true"><img class="visa-row" src="<?=autoUrl("public/img/stripe/visa.svg")?>" aria-hidden="true"><img class="mastercard-row" src="<?=autoUrl("public/img/stripe/mastercard.svg")?>" aria-hidden="true"><img class="amex-row" src="<?=autoUrl("public/img/stripe/amex.svg")?>" aria-hidden="true">
          </p>
        </div>

        <p>
          <a href="<?=autoUrl("galas/pay-for-entries")?>" class="btn btn btn-primary">
            Pay now
          </a>
        </p>

        <p>
          If you have more entries to complete, please do them before paying as you can pay for all or some of your entries at once.
        </p>
      </div>

      <?php } ?>

      <div class="cell">
        <h3>If you're finished here</h3>

        <p>If you've finished making entries, return to the gala homepage.</p>

        <p class="mb-0">
          <a href="<?=autoUrl("galas")?>" class="btn btn-primary">
            Gala home
          </a>
        </p>
      </div>

      <?php if ($_SESSION['TENANT-' . app()->tenant->getId()]['SuccessfulGalaEntry']['HyTek']) { ?>
      <h2 id="why">Why do I have to provide times?</h2>
      <p>
        There are two main providers of software for running galas in the UK: SPORTSYSTEMS Meet Manager and HyTek Meet Manager.
      </p>
        
      <p>
        SPORTSYSTEMS is widely used and is used by Swim England and British Swimming for national championships. SPORTSYSTEMS also provide the software that runs the rankings database, meaning for a gala which is run using SPORTSYSTEMS, personal bests can be obtained automatically.
      </p>

      <p>
        HyTek originated in the United States and is used as the meet software for most galas in the US. HyTek cannot automatically get a swimmer's times from the rankings, mostly because British Swimming and SPORTSYSTEMS won't make it possible for software that is not made by SPORTSYSTEMS to access times from the rankings.
      </p>

      <p>
        We appreciate that this is an inconvenience for a lot of people in our sport. If the situation changes, we'll update our software so that providing manual times is no longer required.
      </p>

      <?php } ?>

    </div>
  </div>
</div>

<?php

if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['SuccessfulGalaEntry'])) {
  unset($_SESSION['TENANT-' . app()->tenant->getId()]['SuccessfulGalaEntry']);
}

$footer = new \SCDS\Footer();
$footer->render();