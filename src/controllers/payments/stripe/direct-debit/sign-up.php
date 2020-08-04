<?php

\Stripe\Stripe::setApiKey(getenv('STRIPE'));

$db = app()->db;
$tenant = app()->tenant;

$getUserEmail = $db->prepare("SELECT Forename, Surname, EmailAddress, Mobile FROM users WHERE UserID = ?");
$getUserEmail->execute([$_SESSION['TENANT-' . app()->tenant->getId()]['UserID']]);
$user = $getUserEmail->fetch(PDO::FETCH_ASSOC);

// UPDATING
$customer = app()->user->getStripeCustomer();

$session = null;
if (isset($renewal_trap) && $renewal_trap) {
  $session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['bacs_debit'],
    'mode' => 'setup',
    'customer' => $customer->id,
    'success_url' => autoUrl('renewal/payments/direct-debit/set-up/success?session_id={CHECKOUT_SESSION_ID}'),
    'cancel_url' => autoUrl('renewal/payments/direct-debit/set-up'),
    'metadata' => [
      'session_type' => 'direct_debit_setup',
    ]
  ], [
    'stripe_account' => $tenant->getStripeAccount()
  ]);
} else {
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
}

$pagetitle = "Set up a Direct Debit";
include BASE_PATH . "views/header.php";
if (isset($renewal_trap) && $renewal_trap) {
  include BASE_PATH . "views/renewalTitleBar.php";
}

?>

<div class="bg-light mt-n3 py-3 mb-3">
  <div class="container">

    <?php if (!isset($renewal_trap) || !$renewal_trap) { ?>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= autoUrl("payments") ?>">Payments</a></li>
          <li class="breadcrumb-item"><a href="<?= autoUrl("payments/direct-debit") ?>">Direct Debit</a></li>
          <li class="breadcrumb-item active" aria-current="page">Set up</li>
        </ol>
      </nav>
    <?php } ?>

    <div class="row align-items-center">
      <div class="col">
        <h1>
          Set up a Direct Debit
        </h1>
        <p class="lead mb-0">
          Direct debit is the easiest way to pay your club fees
        </p>
      </div>
      <div class="d-none d-sm-flex col-sm-auto ml-auto">
        <img style="max-height:50px;" src="<?= htmlspecialchars(autoUrl("public/img/directdebit/directdebit.png")) ?>" srcset="<?= htmlspecialchars(autoUrl("public/img/directdebit/directdebit@2x.png")) ?> 2x, <?= htmlspecialchars(autoUrl("public/img/directdebit/directdebit@3x.png")) ?> 3x" alt="Direct
				Debit Logo">
      </div>
    </div>

  </div>
</div>

<div id="stripe-data" data-stripe-publishable="<?= htmlspecialchars(getenv('STRIPE_PUBLISHABLE')) ?>" data-stripe-account-id="<?= htmlspecialchars($tenant->getStripeAccount()) ?>" data-session-id="<?= htmlspecialchars($session->id) ?>">
</div>

<div class="container">

  <div class="row">
    <div class="col-lg-8">

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
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['StripeDDError']);
      } ?>

      <h2>To begin, you will need</h2>
      <ul>
        <li>The name of the bank account holder</li>
        <li>Your sort code and bank account number</li>
        <li>The address of the bank account holder</li>
      </ul>

      <p>You must be authorised to create a direct debit mandate on the account.</p>

      <h2>You will not need</h2>
      <ul>
        <li>The name or address of your bank - we'll fetch this automatically</li>
        <li>A second approval for most joint accounts - one person is almost always sufficient for approval</li>
      </ul>

      <p>
        Direct Debit makes payments simpler for everyone involved. Payments are taken automatically, so there is no need to adjust standing orders and payments are automatically marked as paid by our systems.
      </p>
      <p>
        We'll usually generate a bill and charge you your fees on or soon after the first working day of each month. It can take several days for the money to leave your bank account.
      </p>

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
