<?php

$pagetitle = "New Payment";

$db = app()->db;
$tenant = app()->tenant;

$categories = $db->prepare("SELECT UniqueID, Name FROM paymentCategories WHERE Tenant = ? ORDER BY `Name` ASC");
$categories->execute([
  $tenant->getId(),
]);
$category = $categories->fetch(PDO::FETCH_ASSOC);

include BASE_PATH . 'views/header.php';

?>

<div class="container">

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('payments')) ?>">Payments</a></li>
      <li class="breadcrumb-item"><a href="<?= htmlspecialchars(autoUrl('payments/invoice-payments')) ?>">Invoicing</a></li>
      <li class="breadcrumb-item active" aria-current="page">New</li>
    </ol>
  </nav>

  <div class="row">
    <div class="col-lg-8">
      <h1>New invoice payment</h1>
      <p class="lead">Add a new payment or credit to an account</p>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['NewPaymentSuccessMessage'])) { ?>
        <div class="alert alert-success">
          <?= htmlspecialchars($_SESSION['TENANT-' . app()->tenant->getId()]['NewPaymentSuccessMessage']) ?>
        </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['NewPaymentSuccessMessage']);
      } ?>

      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['NewPaymentErrorMessage'])) { ?>
        <div class="alert alert-danger">
          <p class="mb-0"><strong>An error occured and we have not added the payment</strong></p>
          <p class="mb-0"><?= htmlspecialchars($_SESSION['TENANT-' . app()->tenant->getId()]['NewPaymentErrorMessage']) ?></p>
        </div>
      <?php unset($_SESSION['TENANT-' . app()->tenant->getId()]['NewPaymentErrorMessage']);
      } ?>

      <form id="payment-form" method="post" class="needs-validation" novalidate>
        <div class="form-group">
          <label for="user-first-name">User's name</label>
          <input type="text" class="form-control" id="user-first-name" name="user-first-name" aria-describedby="user-first-name-help" data-ajax-url="<?= htmlspecialchars(autoUrl("payments/invoice-payments/new/search-users")) ?>">
          <small id="user-first-name-help" class="form-text text-muted">Search for a user by name.</small>
        </div>

        <div class="form-group">
          <label for="user-select">Select user</label>
          <select class="custom-select overflow-hidden" id="user-select" name="user-select" disabled data-ajax-url="<?= htmlspecialchars(autoUrl("payments/invoice-payments/new/get-user")) ?>">
            <option value="none" selected>Search for a user by name</option>
          </select>
          <small id="user-select-help" class="form-text text-muted">Pick a user from this drop down.</small>
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

          <div class="form-group">
            <label for="payment-category">
              Category
            </label>
            <select class="custom-select" id="payment-category" name="payment-category" <?php if (!$category) { ?>disabled<?php } ?>>
              <?php if (!$category) { ?>
                <option value="none" selected>No categories available</option>
              <?php } else { ?>
                <option value="none" selected>Uncategorised</option>
                <?php do { ?>
                  <option value="<?= htmlspecialchars($category['UniqueID']) ?>"><?= htmlspecialchars($category['Name']) ?></option>
                <?php } while ($category = $categories->fetch(PDO::FETCH_ASSOC)); ?>
              <?php } ?>
            </select>
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

          <?= SCDS\CSRF::write() ?>
          <?= SCDS\FormIdempotency::write() ?>

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

<script>
  function clearPaymentAreas() {
    document.getElementById('user-info-box').innerHTML = '';
    document.getElementById('payment-details').classList.add('d-none');
    document.getElementById('payment-submit-button').disabled = true;
  }

  function getUser(event) {
    var user = event.target.value;

    console.log(event.target)

    if (user != 'none') {
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          var result = JSON.parse(this.responseText);
          if (result.has_result) {
            document.getElementById('user-info-box').innerHTML = '<div class="cell"><h2 id="user-info-name"></h2><dl class="row mb-0"><dt class="col-sm-3">Email</dt><dd class="col-sm-9 text-truncate"><a href="" id="user-info-email"></a></dd><dt class="col-sm-3">Phone</dt><dd class="col-sm-9"><a href="" id="user-info-phone"></a></dd><dt class="col-sm-3">Address</dt><dd class="col-sm-9 mb-0"><span id="user-info-address"></span></dd></div>';
            document.getElementById('user-info-name').textContent = result.forename + ' ' + result.surname;
            document.getElementById('user-info-email').textContent = result.email;
            document.getElementById('user-info-email').href = 'mailto:' + result.email;
            document.getElementById('user-info-phone').textContent = result.mobile_national;
            document.getElementById('user-info-phone').href = result.mobile_rfc3966;

            var addressSpan = document.getElementById('user-info-address');
            if (result.hasOwnProperty('address') && result.address !== null) {
              if (result.address.hasOwnProperty('streetAndNumber')) {
                addressSpan.textContent = result.address.streetAndNumber;
              }
              if (result.address.hasOwnProperty('city')) {
                addressSpan.textContent = addressSpan.textContent + ', ' + result.address.city;
              }
              if (result.address.hasOwnProperty('postCode')) {
                addressSpan.textContent = addressSpan.textContent + ', ' + result.address.postCode;
              }
            } else {
              addressSpan.textContent = 'No address on file';
            }

            document.getElementById('user-id').value = result.id;

            document.getElementById('payment-submit-button').disabled = false;

            document.getElementById('payment-details').classList.remove('d-none');
          } else {
            clearPaymentAreas();
          }
        }
      }
      var target = event.target.dataset.ajaxUrl;
      var params = 'user=' + encodeURIComponent(user);
      // Make an ajax request to search for user
      xhr.open('POST', target, true);
      xhr.setRequestHeader('X-Requested-With', 'xhrRequest');
      xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhr.send(params);
    } else {
      clearPaymentAreas();
    }
  }

  let userSearch = document.getElementById('user-first-name');
  userSearch.addEventListener('input', (event) => {
    var req = new XMLHttpRequest();
    req.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        let json = JSON.parse(this.responseText);
        let select = document.getElementById('user-select');
        select.innerHTML = json.html;
        select.disabled = json.disabled;
      } else if (this.readyState == 4) {
        // Not ok
        alert('An error occurred.');
      }
    }
    req.open('POST', userSearch.dataset.ajaxUrl, true);
    req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    req.send('search=' + encodeURI(userSearch.value));
  });

  let userSelect = document.getElementById('user-select');
  userSelect.addEventListener('change', getUser);
  // getUser();

  var form = document.getElementById('payment-form');
  form.addEventListener('submit', function(event) {
    // Cancel form submission
    event.preventDefault();

    if (document.getElementById('payment-form').checkValidity()) {
      document.getElementById('error-notice').classList.add('d-none');

      var type = this.elements['type'];
      if (type.value === 'Payment') {
        document.getElementById('confirmation-modal-pay-credit').textContent = 'charge';
      } else {
        document.getElementById('confirmation-modal-pay-credit').textContent = 'credit/refund';
      }

      var amountString = (new BigNumber(document.getElementById('amount').value)).decimalPlaces(2).toFormat(2);
      document.getElementById('confirmation-modal-amount').textContent = amountString;

      document.getElementById('confirmation-modal-name').textContent = document.getElementById('user-info-name').textContent;

      document.getElementById('confirmation-modal-user-info-name').textContent = document.getElementById('user-info-name').textContent;

      var email = document.getElementById('confirmation-modal-user-info-email');
      var emailData = document.getElementById('user-info-email');
      email.textContent = emailData.textContent;
      email.href = emailData.href;

      var phone = document.getElementById('confirmation-modal-user-info-phone');
      var phoneData = document.getElementById('user-info-phone');
      phone.textContent = phoneData.textContent;
      phone.href = phoneData.href;

      document.getElementById('confirmation-modal-user-info-address').textContent = document.getElementById('user-info-address').textContent;

      document.getElementById('confirm').addEventListener('click', function(event) {
        document.getElementById('payment-form').submit();
      });

      // Show alert modal
      $('#confirmation-modal').modal('show');
    } else {
      document.getElementById('error-notice').classList.remove('d-none');
    }
  });

  var radios = document.getElementById('radios');

  if (radios !== null) {
    var options = radios.getElementsByTagName('input');
    for (var i = 0, len = options.length; i < len; i++) {
      if (options[i].type === 'radio') {
        options[i].addEventListener('change', function(event) {
          var type = document.getElementById('payment-form').elements['type'];
          if (type.value === 'Payment') {
            document.getElementById('amount-type').textContent = 'charge';
            document.getElementById('payment-submit-button').textContent = 'Add charge';
          } else {
            document.getElementById('amount-type').textContent = 'credit/refund';
            document.getElementById('payment-submit-button').textContent = 'Add credit/refund';
          }
        });
      }
    }
  }
</script>

<?php

$footer = new \SCDS\Footer();
$footer->addJs("public/js/numerical/bignumber.min.js");
$footer->addJs("public/js/payments/NewInvoice.js?v=1");
$footer->addJs("public/js/NeedsValidation.js");
$footer->render();
