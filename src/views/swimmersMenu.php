<?php $access = $_SESSION['AccessLevel']; ?>
<div class="nav-scroller bg-white box-shadow mb-3">
  <nav class="nav nav-underline">
    <a class="nav-link" href="<?php echo autoUrl("swimmers")?>">Swimmers</a>
		<a class="nav-link" href="<?php echo autoUrl("squads/moves")?>">Squad Moves</a>
    <? if ($access == "Admin") { ?>
    <a class="nav-link" href="<?php echo autoUrl("renewal")?>">Membership Renewal</a>
    <? } ?>
   </nav>
</div>
