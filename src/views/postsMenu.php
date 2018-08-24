<? if ($_SESSION['AccessLevel'] != "Parent" && $_SESSION['AccessLevel'] != "Coach") { ?>
<div class="nav-scroller bg-white box-shadow mb-3">
  <nav class="nav nav-underline">
		<a class="nav-link" href="<?php echo autoUrl("posts")?>">Home</a>
		<a class="nav-link" href="<?php echo autoUrl("posts/new")?>">New Page</a>
		<? if ($allow_edit && $_SESSION['AccessLevel'] != "Parent" &&
		$_SESSION['AccessLevel'] != "Coach") { ?>
		<a class="nav-link" href="<?=app('request')->curl?>edit">Edit Current Page</a>
		<? } ?>
		<? if ($exit_edit && $_SESSION['AccessLevel'] != "Parent" &&
		$_SESSION['AccessLevel'] != "Coach") { ?>
		<a class="nav-link" href="<?=autoUrl("posts/" . $id)?>">View Page</a>
		<? } ?>
	</nav>
</div>
<? } ?>
