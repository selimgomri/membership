<?php

$db = app()->db;
$tenant = app()->tenant;

$user = $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'];
$partial_reg = isPartialRegistration();

$hasStripeMandate = false;
$hasGCMandate = false;
if (stripeDirectDebit(true)) {
  // Work out if has mandates
  $getCountNewMandates = $db->prepare("SELECT COUNT(*) FROM stripeMandates INNER JOIN stripeCustomers ON stripeMandates.Customer = stripeCustomers.CustomerID WHERE stripeCustomers.User = ? AND stripeMandates.MandateStatus != 'inactive';");
  $getCountNewMandates->execute([
    $_SESSION['TENANT-' . app()->tenant->getId()]['UserID']
  ]);
  $hasStripeMandate = $getCountNewMandates->fetchColumn() > 0;
} else if (app()->tenant->getGoCardlessAccessToken()) {
  $hasGCMandate = userHasMandates($_SESSION['TENANT-' . app()->tenant->getId()]['UserID']);
} else {
}

$pagetitle = "Direct Debit Options";

include BASE_PATH . 'views/header.php';
include BASE_PATH . "views/renewalTitleBar.php";
?>

<div class="container">
  <h1>
    Direct Debit
  </h1>
  <div class="row">
    <div class="col-lg-8">
      <form method="post">
        <p class="lead">
          <?php if ($tenant->getBooleanKey('ALLOW_DIRECT_DEBIT_OPT_OUT')) { ?>
            Would you like to set up a Direct Debit Instruction? Direct Debit is the easiest way to make monthly payments to <?= htmlspecialchars($tenant->getName()) ?>.
          <?php } else { ?>
            Set up your Direct Debit Instruction to make monthly payments to <?= htmlspecialchars($tenant->getName()) ?>.
          <?php } ?>
        </p>

        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']) && $_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']) { ?>
          <div class="alert alert-success">
            <p class="mb-0">
              <strong>We've set up your new direct debit</strong>
            </p>
            <p class="mb-0">
              It will take a few days for the mandate to be confirmed at your bank.
            </p>
          </div>
        <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']);
        } ?>

        <?php if (stripeDirectDebit(true)) { ?>

          <p>
            <button type="submit" class="btn btn-success btn-lg btn-block">
              Setup direct debit
            </button>
          </p>

        <?php } else if (app()->tenant->getGoCardlessAccessToken()) { ?>
          <p>
            <button type="submit" class="btn btn-success btn-lg btn-block">
              Setup direct debit
            </button>
          </p>
        <?php } else { ?>
          <p>
            You should not be seeing this page.
          </p>
        <?php } ?>

        <?php if (true || !$hasStripeMandate && app()->tenant->getBooleanKey('ALLOW_DIRECT_DEBIT_OPT_OUT')) { ?>
          <p><button type="submit" name="avoid-dd" value="1" class="btn btn-outline-dark btn-sm btn-block">I want to pay my fees another way</button></p>
        <?php } ?>
      </form>

    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
