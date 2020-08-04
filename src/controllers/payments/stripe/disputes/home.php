<?php

if (!app()->user->hasPermission('Admin')) {
  halt(404);
}

$db = app()->db;
$tenant = app()->tenant;

$pagetitle = "Disputes";
include BASE_PATH . 'views/header.php';

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('payments')) ?>">Payments</a></li>
        <li class="breadcrumb-item active" aria-current="page">Disputes</li>
      </ol>
    </nav>

    <div class="row align-items-center">
      <div class="col">
        <h1>
          Payment Disputes
        </h1>
        <p class="lead mb-0">
          Dispute information for card and direct debit payments
        </p>
      </div>
    </div>

  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-lg-8">

      <p>
        <a href="https://dashboard.stripe.com/disputes" class="btn btn-primary" target="_blank">View in Stripe Dashboard <i class="fa fa-external-link" aria-hidden="true"></i></a>
      </p>

      <p>
        We'll soon be displaying information within the membership system about credit/debit card and direct debit payment disputes. Disputes include retrievals/inquiries and chargebacks. When customers chargeback payments, their bank will refund their account and the money plus a dispute fee will be withdrawn from your Stripe account.
      </p>

      <p>
        You can provide evidence to dispute to a credit/debit card chargeback via the Stripe dashboard. Direct Debit chargebacks are final and cannot be disputed. It is illegal for customers to make a fraudulent chargeback and chargebacks do not absolve customers of their contactual payment obligations.
      </p>

      <p>
        The membership system will display information about all disputes and will mark monthly fee payments as failed if they are disputed.
      </p>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();
