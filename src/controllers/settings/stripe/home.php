<?php

$fluidContainer = true;
$pagetitle = 'Stripe Payment Services Options';

$db = app()->db;
$tenant = app()->tenant;

$vars = [
  'GALA_CARD_PAYMENTS_ALLOWED' => true,
  'USE_STRIPE_DIRECT_DEBIT' => false,
];
$disabled = [
  'GALA_CARD_PAYMENTS_ALLOWED' => '',
  'USE_STRIPE_DIRECT_DEBIT' => '',
];

foreach ($vars as $key => $value) {
  if (($value = $tenant->getKey($key)) != null) {
    $vars[$key] = bool($value);
  }
}

if ($vars['USE_STRIPE_DIRECT_DEBIT']) {
  $disabled['USE_STRIPE_DIRECT_DEBIT'] = ' disabled ';
}

include BASE_PATH . 'views/header.php';

?>

<div class="container-fluid">
  <div class="row justify-content-between">
    <aside class="col-md-3 d-none d-md-block">
      <?php
      $list = new \CLSASC\BootstrapComponents\ListGroup(file_get_contents(BASE_PATH . 'controllers/settings/SettingsLinkGroup.json'));
      echo $list->render('settings-stripe');
      ?>
    </aside>
    <div class="col-md-9">
      <main>

        <h1>
          Payment services
        </h1>
        <p class="lead">
          Manage your Stripe connection
        </p>

        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['Stripe-Reg-Success'])) { ?>
          <div class="alert alert-success">
            <p class="mb-0">
              <strong>We've connected your Stripe Account</strong>
            </p>
            <p class="mb-0">
              Find out about Stripe <a href="https://stripe.com/gb" target="_blank">on their website</a>.
            </p>
          </div>
        <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['Stripe-Reg-Success']);
        } ?>

        <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['Stripe-Reg-Error'])) { ?>
          <div class="alert alert-danger">
            <p class="mb-0">
              <strong>We were unable to connect your Stripe Account</strong>
            </p>
            <p class="mb-0">
              <a href="<?= htmlspecialchars(autoUrl("settings/stripe/register")) ?>" class="alert-link">Try again now</a> or try again later.
            </p>
          </div>
        <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['Stripe-Reg-Error']);
        } ?>

        <?php if ($at = app()->tenant->getStripeAccount()) { ?>

          <h2>
            Stripe Account
          </h2>

          <p>
            Your Stripe account is currently connected.
          </p>

          <p>
            <a href="https://dashboard.stripe.com" class="btn btn-primary">
              Stripe Dashboard
            </a>
          </p>

          <?php if (bool(getenv('IS_DEV'))) { ?>
            <h2>
              Options
            </h2>

            <form method="post">
              <div class="form-group">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="GALA_CARD_PAYMENTS_ALLOWED" name="GALA_CARD_PAYMENTS_ALLOWED" <?php if (bool($vars['GALA_CARD_PAYMENTS_ALLOWED'])) { ?>checked<?php } ?> <?= $disabled['GALA_CARD_PAYMENTS_ALLOWED'] ?>>
                  <label class="custom-control-label" for="GALA_CARD_PAYMENTS_ALLOWED">Allow card payments for gala entries</label>
                </div>
              </div>

              <div class="form-group">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="USE_STRIPE_DIRECT_DEBIT" name="USE_STRIPE_DIRECT_DEBIT" <?php if (bool($vars['USE_STRIPE_DIRECT_DEBIT'])) { ?>checked<?php } ?> <?= $disabled['USE_STRIPE_DIRECT_DEBIT'] ?> aria-describedby="USE_STRIPE_DIRECT_DEBIT-help">
                  <label class="custom-control-label" for="USE_STRIPE_DIRECT_DEBIT">Use Stripe for Direct Debit rather than GoCardless</label>
                </div>
                <small id="USE_STRIPE_DIRECT_DEBIT-help">Once you enable Stripe Direct Debit, GoCardless will stop working and this change can not be reversed.</small>
              </div>

              <p>
                <button type="submit" class="btn btn-success">
                  Save
                </button>
              </p>
            </form>
          <?php } ?>

        <?php } else { ?>

          <p>
            The membership system supports credit and debit card payments for gala entries. We hope to bring support for card payments in other areas, such as registration and renewal in due course.
          </p>

          <p>
            <a href="<?= htmlspecialchars(autoUrl("settings/stripe/register")) ?>" class="btn btn-primary">Get started</a>
          </p>

          <p>
            We'll send you to Stripe and ask you to sign in or create an account. Find out <a href="https://stripe.com/gb/payments" target="_blank">more about Stripe</a> and <a href="https://stripe.com/gb/pricing" target="_blank">their pricing</a>.
          </p>

        <?php } ?>

      </main>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->useFluidContainer();
$footer->render();
