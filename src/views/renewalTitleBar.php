<div class="nav-scroller bg-white box-shadow mb-3">
  <div class="nav nav-underline">
    <? if (user_needs_registration($_SESSION['UserID'])) { ?>
    <span class="nav-link text-dark">Club Registration</span>
    <? } else { ?>
    <span class="nav-link text-dark"><? echo $renewalName; ?></span>
    <? } ?>
  </div>
</div>
