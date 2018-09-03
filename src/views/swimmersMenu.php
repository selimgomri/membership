<?php $access = $_SESSION['AccessLevel'];
if (isset($_SESSION['Swimmers-FamilyMode']) && $_SESSION['AccessLevel'] != "Parent") { ?>
<div class="bg-secondary text-white shadow mb-3" style="margin-top:-1rem;">
  <div class="<?=$container_class?>">
    <nav class="nav nav-underline py-2">
      <p class="mb-0">
        You're in family mode. <strong><a class="text-white" href="<?php echo
        autoUrl("swimmers/family/exit")?>">Exit family mode</a></strong> before
        adding swimmers that don't belong to the current family.
      </p>
    </nav>
  </div>
</div>
<? } ?>
<!--
<div class="nav-scroller bg-white shadow mb-3">
  <div class="<?=$container_class?>">
    <nav class="nav nav-underline">
      <a class="nav-link" href="<?php echo autoUrl("swimmers")?>">Swimmer Directory</a>
      <? if ($access == "Admin") { ?>
      <a class="nav-link" href="<?php echo autoUrl("swimmers/addmember")?>">Add Member</a>
      <? } ?>
      <a class="nav-link" href="<?php echo autoUrl("squads")?>">Squads</a>
  		<a class="nav-link" href="<?php echo autoUrl("squads/moves")?>">Squad Moves</a>
      <a class="nav-link" href="<?php echo autoUrl("swimmers/accesskeys")?>">Access Keys</a>
      <? if ($access == "Admin") { ?>
      <a class="nav-link" href="<?php echo autoUrl("renewal")?>">Membership Renewal</a>
      <a class="nav-link" href="<?php echo autoUrl("swimmers/orphaned")?>">Orphan Swimmers</a>
      <? } ?>
    </nav>
  </div>
</div>
-->
