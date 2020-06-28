<?php

\Stripe\Stripe::setApiKey(getenv('STRIPE'));

$db = app()->db;
$tenant = app()->tenant;

$getUserEmail = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile FROM users WHERE UserID = ?");
$getUserEmail->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
$user = $getUserEmail->fetch(PDO::FETCH_ASSOC);

// UPDATING
$customer = app()->user->getStripeCustomer();

$session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['bacs_debit'],
  'mode' => 'setup',
  'customer' => $customer->id,
  'success_url' => autoUrl('payments/direct-debit/set-up/success?session_id={CHECKOUT_SESSION_ID}'),
  'cancel_url' => autoUrl('payments/direct-debit/set-up'),
  'metadata' => [
    'session_type' => 'direct_debit_setup',
  ]
], [
  'stripe_account' => $tenant->getStripeAccount()
]);

$pagetitle = "Set up a Direct Debit";
include BASE_PATH . "views/header.php";

?>

<div id="stripe-data" data-stripe-publishable="<?= htmlspecialchars(getenv('STRIPE_PUBLISHABLE')) ?>" data-stripe-account-id="<?= htmlspecialchars($tenant->getStripeAccount()) ?>" data-session-id="<?= htmlspecialchars($session->id) ?>">
</div>

<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= autoUrl("payments") ?>">Payments</a></li>
      <li class="breadcrumb-item"><a href="<?= autoUrl("payments/direct-debit") ?>">Direct Debit</a></li>
      <li class="breadcrumb-item active" aria-current="page">Set up</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>Direct Debit</h1>
      <p class="lead">Create a direct debit</p>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDError']) && $_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDError']) { ?>
      <div class="alert alert-error">
        <p class="mb-0">
          <strong>We've encountered a problem setting up your direct debit</strong>
        </p>
        <p>
          You may not have supplied all of the information required or taken too long to complete the form.
        </p>

        <p class="mb-0">
          Please try again.
        </p>
      </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDError']); } ?>

      <p>
        <button id="set-up-button" class="btn btn-primary">
          Set up
        </button>
      </p>


    </div>
  </div>

</div>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/payments/direct-debit/setup.js");
$footer->render();
