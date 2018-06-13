<?php $access = $_SESSION['AccessLevel']; ?>
<div class="nav-scroller bg-white box-shadow mb-3">
  <nav class="nav nav-underline">
		<? if ($access == "Admin") { ?>
    <a class="nav-link" href="<?php echo autoUrl("payments")?>">Payments Home</a>
		<a class="nav-link" href="<?php echo autoUrl("payments/newcharge")?>">New Charge</a>
		<a class="nav-link" href="<?php echo autoUrl("payments/newrefund")?>">New Refund</a>
		<? } ?>
  </nav>
</div>
