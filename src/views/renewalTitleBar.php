<? $target = ""; ?>
<div class="nav-scroller bg-white box-shadow mb-3">
  <div class="nav nav-underline">
    <? if (user_needs_registration($_SESSION['UserID'])) {
    $target = "Club Registration";?>
    <span class="nav-link text-dark">Club Registration</span>
  <? } else if (!isset($renewalName)) {
    $target = "Membership Renewal System";?>
    <span class="nav-link text-dark">Membership Renewal System</span>
  <? } else {
    $target = $renewalName;?>
    <span class="nav-link text-dark"><? echo $renewalName; ?></span>
    <? } ?>
    <? if ($renewal_trap) { ?>
      <a class="nav-link" href="<?php echo autoUrl("renewal/go")?>">Return to <?= $target ?></a>
    <? } ?>
  </div>
</div>
