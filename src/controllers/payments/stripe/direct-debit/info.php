<?php

$db = app()->db;
$tenant = app()->tenant;

// Get mandates
$getMandates = $db->prepare("SELECT ID, Mandate, Last4, SortCode, `Address`, Reference, `URL`, `Status` FROM stripeMandates WHERE Customer = ? AND (`Status` = 'accepted' OR `Status` = 'pending')");
$getMandates->execute([
  app()->user->getStripeCustomer()->id,
]);
$mandate = $getMandates->fetch(PDO::FETCH_ASSOC);

$pagetitle = "Direct Debit";
include BASE_PATH . "views/header.php";

?>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= autoUrl("payments") ?>">Payments</a></li>
      <li class="breadcrumb-item active" aria-current="page">Direct Debit</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>Direct Debit</h1>
      <p class="lead">Welcome to our new Direct Debit system</p>
      <p>
        We've upgraded our infrastructure and made changes to our direct debit systems.
      </p>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']) && $_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']) { ?>
        <div class="alert alert-success">
          <p class="mb-0">
            <strong>We've set up your new direct debit</strong>
          </p>
          <p>
            It will take a few days for the mandate to be confirmed at your bank.
          </p>

          <p class="mb-0 small">
            At busy times, your mandate may take a few minutes to appear in our systems.
          </p>
        </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDSuccess']);
      } ?>

      <?php if ($mandate) {
        do { ?>
          <div class="card card-body mb-3">
            <h2>Your Direct Debit Mandate <span class="badge badge-secondary"><?php if ($mandate['Status'] == 'pending') { ?>Pending<?php } else if ($mandate['Status'] == 'accepted') { ?>Active<?php } ?></span></h2>
            <dl class="row">
              <dt class="col-sm-3">Sort code</dt>
              <dd class="col-sm-9 mono"><?= htmlspecialchars(implode("-", str_split($mandate['SortCode'], 2))) ?></dd>

              <dt class="col-sm-3">Account number</dt>
              <dd class="col-sm-9 mono">&middot;&middot;&middot;&middot;<?= htmlspecialchars($mandate['Last4']) ?></dd>

              <dt class="col-sm-3">Payment reference</dt>
              <dd class="col-sm-9 mono"><?= htmlspecialchars($mandate['Reference']) ?></dd>
            </dl>

            <p class="mb-0">
              <a href="<?= htmlspecialchars($mandate['URL']) ?>">
                View Direct Debit Instruction
              </a>
            </p>
          </div>
      <?php } while ($mandate = $getMandates->fetch(PDO::FETCH_ASSOC));
      } ?>

      <p>
        <a class="btn btn-primary" href="<?= htmlspecialchars(autoUrl("payments/direct-debit/set-up")) ?>">Set up a new mandate</a>
      </p>

      <p>
        We will always use the most recent mandate that you set up for your payments. Mandates are fully set up once they become active.
      </p>

    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
