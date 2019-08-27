<?php

// Confirmation - Main form
// If a reference was used, this is the only form the treasurer will need.

$date = new DateTime('now', new DateTimeZone('Europe/London'));

?>

<form action="<?=autoUrl("payments/confirmation/form-2")?>" method="post">

  <div class="form-row">
    <div class="col-6">
      <div class="form-group">
        <label for="payment-amount">Amount</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <div class="input-group-text mono">&pound;</div>
          </div>
          <input type="number" class="mono form-control" id="payment-amount" name="payment-amount" aria-describedby="payment-amount-help" placeholder="0" pattern="[0-9]*([\.,][0-9]*)?" min="0" step="0.01">
        </div>
        <small id="payment-amount-help" class="form-text text-muted">
          The amount shown on your bank statement
        </small>
      </div>
    </div>

    <div class="col-6">
      <div class="form-group">
        <label for="payment-name">Surname</label>
        <input type="text" class="mono form-control" id="payment-name" name="payment-name" aria-describedby="payment-name-help" placeholder="eg <?=htmlspecialchars($_SESSION['Surname'])?>">
        <small id="payment-date-help" class="form-text text-muted">
          Enter the surname of the name shown on your bank statement*
        </small>
      </div>
    </div>
  </div>

  <p>
    <button class="btn btn-success" type="submit">
      Find payment
    </button>
  </p>

  <p>
    * Surnames are more likely to match if the bank account is in the name of the spouse/partner of the club account holder.
  </p>

</form>