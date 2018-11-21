<? $target = ""; ?>
<div class="bg-white mb-3 py-2" style="margin-top:-1rem;">
  <div class="<?=$container_class?>">
    <div class="nav nav-underline">
      <? if (user_needs_registration($_SESSION['UserID'])) {
      $target = "Club Registration";?>
      <span class="text-dark mr-3">Club Registration</span>
    <? } else if (!isset($renewalName)) {
      $target = "Membership Renewal System";?>
      <span class="text-dark mr-3">Membership Renewal System</span>
    <? } else {
      $target = $renewalName;?>
      <span class="text-dark mr-3"><? echo $renewalName; ?></span>
      <? } ?>
      <? if ($renewal_trap) { ?>
        <a class="" href="<?php echo autoUrl("renewal/go")?>">Return to <?= $target ?></a>
      <? } ?>
    </div>
  </div>
</div>
