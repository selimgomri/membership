<?php

$fluidContainer = true;
$pagetitle = 'Credit and Debit Card Payments';

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
          Card payment services
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
              See x about getting started
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

          <p>
            Your Stripe account is currently connected.
          </p>

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
