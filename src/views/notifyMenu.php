<!--<div class="bg-warning box-shadow mb-3" style="margin-top:-1rem;">
  <nav class="nav nav-underline">
    <span class="nav-link">
      <strong>
        The Notify Service is currently closed for maintenance. Attempts to send
        messages will fail.
      </strong>
    </span>
  </nav>
</div>-->
<div class="nav-scroller bg-white box-shadow mb-3">
  <nav class="nav nav-underline">
		<a class="nav-link" href="<?php echo autoUrl("notify")?>">Home</a>
		<a class="nav-link" href="<?php echo autoUrl("notify/newemail")?>">New Message</a>
    <a class="nav-link" href="<?php echo autoUrl("notify/lists")?>">Targeted Lists</a>
    <? if ($_SESSION['AccessLevel'] == "Admin") { ?>
    <a class="nav-link" href="<?php echo autoUrl("notify/sms")?>">SMS Lists</a>
    <? } ?>
		<a class="nav-link" href="<?php echo autoUrl("notify/email")?>">Pending Messages</a>
    <a class="nav-link" href="<?php echo autoUrl("notify/history")?>">Previous Messages</a>
	</nav>
</div>
