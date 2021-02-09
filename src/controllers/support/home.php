<?php

$pagetitle = "Membership System Help - SCDS Membership";

include BASE_PATH . "views/root/header.php";

?>

<div class="container">

  <div class="row justify-content-center py-3">
    <div class="col-lg-8 col-md-10">
      <!-- <div class="h4 text-muted mb-3 font-weight-normal">Help and Support</div> -->
      <div class="bg-primary text-white p-4 mb-4 d-inline-block rounded">
        <h1>Membership System Help</h1>
        <p class="mb-0">
          Get help with membership system features
        </p>
      </div>
      <p class="lead">
        The SCDS Online Membership System enables you to manage your swimmers, enter competitions, stay up to date by email and make payments by Direct Debit.
      </p>

      <div class="card card-body mb-3">
        <h2>Contact SCDS Support</h2>
        <p class="lead mb-0">
          Contact <a href="mailto:support@myswimmingclub.uk">support@myswimmingclub.uk</a> to start a new support request.
        </p>
      </div>

      <div class="card mb-3">
        <div class="card-body">
          <h2>Help categories</h2>
          <p class="mb-0">
            Help categories available on our new support site
          </p>
        </div>
        <div class="list-group list-group-flush">
          <a href="<?= htmlspecialchars('help-and-support/log-books') ?>" class="list-group-item list-group-item-action">Log books</a>
          <a href="<?= htmlspecialchars('help-and-support/documentation') ?>" class="list-group-item list-group-item-action">Contributing to our documentation</a>
        </div>
      </div>

      <div class="card card-body mb-3">
        <h2>Support articles</h2>
        <ul class="list-unstyled mb-0">
          <li>
            <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/adding-a-payment-card/" rel="bookmark" title="Adding a payment card">
              Adding a payment card
            </a>
          </li>
          <li>
            <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/adding-an-additional-email-address/" rel="bookmark" title="Adding an additional email address">
              Adding an additional email address
            </a>
          </li>
          <li>
            <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/adding-emergency-contact-details-to-your-club-account/" rel="bookmark" title="Adding Emergency Contact Details to your Club Account">
              Adding Emergency Contact Details to your Club Account
            </a>
          </li>
          <li>
            <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/adding-swimmers-to-your-account/" rel="bookmark" title="Adding Swimmers to your Account">
              Adding Swimmers to your Account
            </a>
          </li>
          <li>
            <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/direct-debit-transition/" rel="bookmark" title="Direct Debit Transition">
              Direct Debit Transition
            </a>
          </li>
          <li>
            <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/downloading-a-copy-of-your-personal-data/" rel="bookmark" title="Downloading a copy of your personal data">
              Downloading a copy of your personal data
            </a>
          </li>
          <li>
            <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/guidance/" rel="bookmark" title="Entering a Competition Online">
              Entering a Competition Online
            </a>
          </li>
          <li>
            <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/paying-for-gala-entries/" rel="bookmark" title="Paying for gala entries">
              Paying for gala entries
            </a>
          </li>
          <li>
            <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/paying-for-gala-entries-by-credit-or-debit-card/" rel="bookmark" title="Paying for gala entries by Credit or Debit Card">
              Paying for gala entries by Credit or Debit Card
            </a>
          </li>
          <li>
            <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/guidance-setting-up-your-online-account/" rel="bookmark" title="Setting up your Online Account">
              Setting up your Online Account
            </a>
          </li>
        </ul>
      </div>

      <p>
        We're busy transferring help and support documentation over from the Chester-le-Street ASC website. Once this is completed, we'll be expanding our help and support resources.
      </p>
    </div>
  </div>
</div>

<?php

$footer = new \SCDS\RootFooter();
$footer->render();
