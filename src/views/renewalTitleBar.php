<? $target = ""; ?>
<div class="bg-white shadow box-shadow mb-3 py-2" style="margin-top:-1rem;">
  <div class="<?=$container_class?>">
    <div class="nav nav-underline">
      <? if (user_needs_registration($_SESSION['UserID'])) {
      $target = "Club Registration";?>
      <span class="text-dark">Club Registration</span>
    <? } else if (!isset($renewalName)) {
      $target = "Membership Renewal System";?>
      <span class="text-dark">Membership Renewal System</span>
    <? } else {
      $target = $renewalName;?>
      <span class="text-dark"><? echo $renewalName; ?></span>
      <? } ?>
      <? if ($renewal_trap) { ?>
        <a class="nav-link" href="<?php echo autoUrl("renewal/go")?>">Return to <?= $target ?></a>
      <? } ?>
    </div>
  </div>
</div>
