<? if (!$renewal_trap) {
$access = $_SESSION['AccessLevel']; ?>
<div class="bg-warning box-shadow mb-3 py-2" style="margin-top:-1rem;">
  <div class="<?=$container_class?>">
    <nav class="nav nav-underline">
      <strong>
        Payments are under test. No payments will be sent and no money will
        change hands.
      </strong>
    </nav>
  </div>
</div>
<div class="nav-scroller bg-white box-shadow mb-3" style="margin-top:-1rem;">
  <div class="<?=$container_class?>">
    <span class="mono my-1">
      THIS MENU WILL BE REMOVED AND INTEGRATED INTO NAVBAR AFTER BETA
    </span>
    <nav class="nav nav-underline">
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
</div>
<? } else {
  include 'renewalTitleBar.php';
}
