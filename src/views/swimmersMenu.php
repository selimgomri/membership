<?php $access = $_SESSION['AccessLevel']; ?>
<div class="nav-scroller bg-white box-shadow mb-3">
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
