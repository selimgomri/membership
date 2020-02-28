<?php

$pagetitle = "New Payment";

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl('payments'))?>">Payments</a></li>
      <li class="breadcrumb-item"><a href="<?=htmlspecialchars(autoUrl('payments/invoice-payments'))?>">Invoicing</a></li>
      <li class="breadcrumb-item active" aria-current="page">New</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>New invoice payment</h1>
      <p class="lead">Add a new payment or credit to an account</p>

      <?php if (isset($_SESSION['NewPaymentSuccessMessage'])) { ?>
      <div class="alert alert-success">
        <?=htmlspecialchars($_SESSION['NewPaymentSuccessMessage'])?>
      </div>
      <?php unset($_SESSION['NewPaymentSuccessMessage']); } ?>

      <?php if (isset($_SESSION['NewPaymentErrorMessage'])) { ?>
      <div class="alert alert-danger">
        <p class="mb-0"><strong>An error occured and we have not added the payment</strong></p>
        <p class="mb-0"><?=htmlspecialchars($_SESSION['NewPaymentErrorMessage'])?></p>
      </div>
      <?php unset($_SESSION['NewPaymentErrorMessage']); } ?>

      <form id="payment-form" method="post" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="user-email">User email address</label>
          <input type="text" class="form-control" id="user-email" name="user-email" aria-describedby="user-email-help" data-ajax-url="<?=htmlspecialchars(autoUrl("payments/invoice-payments/new/get-user"))?>">
          <small id="user-email-help" class="form-text text-muted">Search for the user by email.</small>
        </div>

        <div id="user-info-box"></div>

        <input type="hidden" name="user-id" id="user-id">

        <div id="payment-details" class="d-none">

          <div class="form-group">
            <label for="description">Payment description</label>
            <input required mmaxlength="500" type="text" class="form-control" name="description" id="description" aria-describedby="description-help">
            <div class="invalid-feedback">
              Invalid description
            </div>
            <small id="description-help" class="form-text text-muted">The description for this payment shown on the user's billing statement. Make it descriptive enough that they will be able to identify the reason for this item.</small>
          </div>

          <div class="form-group">
            <label for="amount">
              Amount to <span id="amount-type">charge</span>
            </label>
            <div class="input-group">
              <div class="input-group-prepend">
                <div class="input-group-text mono">&pound;</div>
              </div>
              <input required type="number" pattern="[0-9]*([\.,][0-9]*)?" class="form-control mono" id="amount" name="amount" placeholder="0.00" min="0" max="1000" step="0.01">
              <div class="invalid-feedback">
                You must enter an amount between &pound;0 and £1000.
              </div>
            </div>
          </div>

          <div class="form-group" id="radios">
            <label>Type</label>
            <div class="custom-control custom-radio">
              <input type="radio" id="type-payment" name="type" class="custom-control-input" checked required value="Payment">
              <label class="custom-control-label" for="type-payment">Payment</label>
            </div>
            <div class="custom-control custom-radio">
              <input type="radio" id="type-credit" name="type" class="custom-control-input" required value="Credit">
              <label class="custom-control-label" for="type-credit">Credit (refund)</label>
            </div>
          </div>

          <?=SCDS\CSRF::write()?>
          <?=SCDS\FormIdempotency::write()?>

          <p>Once you add a charge to an account, you won't be able to cancel it. If you make a mistake or need to reverse it, you would need to add another charge or a credit (refund).</p>

          <p>
            <button type="submit" class="btn btn-primary" id="payment-submit-button" disabled>Add charge</button>
          </p>

          <div id="error-notice" class="alert alert-warning d-none">
            <p class="mb-0">
              <strong>Please complete the form</strong>
            </p>
            <p class="mb-0">
              A description, amount and type (charge or credit) is required.
            </p>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-labelledby="confirmation-modal-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmation-modal-label">Confirmation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Please confirm that you want to make a <span id="confirmation-modal-pay-credit"></span> of &pound;<span id="confirmation-modal-amount"></span> to <span id="confirmation-modal-name"></span>'s account. Additional account details are shown below.</p>
        <div class="cell">
          <h2 id="confirmation-modal-user-info-name"></h2>
          <dl class="row mb-0">
            <dt class="col-sm-3">Email</dt>
            <dd class="col-sm-9 text-truncate"><a href="" id="confirmation-modal-user-info-email"></a></dd>
            
            <dt class="col-sm-3">Phone</dt>
            <dd class="col-sm-9"><a href="" id="confirmation-modal-user-info-phone"></a></dd>

            <dt class="col-sm-3">Address</dt>
            <dd class="col-sm-9 mb-0"><span id="confirmation-modal-user-info-address"></span></dd>
        </div>
        <p class="mb-0">A confirmation email detailing this statement item will be sent to the user.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" id="confirm" class="btn btn-primary">Confirm</button>
      </div>
    </div>
  </div>
</div>

<?php

$footer = new \SDCS\Footer();
$footer->addJs("public/js/numerical/bignumber.min.js");
$footer->addJs("public/js/payments/NewInvoice.js");
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();
