<? if (!$renewal_trap) {
$access = $_SESSION['AccessLevel']; ?>
<div class="bg-warning box-shadow mb-3" style="margin-top:-1rem;">
  <nav class="nav nav-underline">
    <span class="nav-link">
      <strong>
        Payments are under test. No payments will be sent and no money will
        change hands.
      </strong>
    </span>
  </nav>
</div>
<div class="nav-scroller bg-white box-shadow mb-3">
  <nav class="nav nav-underline">
		<? if ($access == "Admin") { ?>
    <a class="nav-link" href="<?php echo autoUrl("payments")?>">Payments Home</a>
    <a class="nav-link" href="<?php echo autoUrl("payments/history")?>">Payments &amp; History</a>
    <a class="nav-link" href="<?php echo autoUrl("payments/current")?>">Current Fees</a>
		<a class="nav-link" href="<?php echo autoUrl("payments/newcharge")?>">New Charge</a>
		<a class="nav-link" href="<?php echo autoUrl("payments/galas")?>">Gala Charges</a>
    <a class="nav-link" href="<?php echo autoUrl("payments/extrafees")?>">Extras</a>
		<? } ?>
    <? if ($access == "Galas") { ?>
		<a class="nav-link" href="<?php echo autoUrl("payments")?>">Gala Charges</a>
		<? } ?>
    <? if ($access == "Parent") { ?>
    <a class="nav-link" href="<?php echo autoUrl("payments")?>">Payments Home</a>
		<a class="nav-link" href="<?php echo autoUrl("payments/fees")?>">Current Charges</a>
		<a class="nav-link" href="<?php echo autoUrl("payments/transactions")?>">Previous Transactions</a>
    <a class="nav-link" href="<?php echo autoUrl("payments/mandates")?>">Bank Options</a>
		<? } ?>
  </nav>
</div>
<? } else {
  include 'renewalTitleBar.php';
}
