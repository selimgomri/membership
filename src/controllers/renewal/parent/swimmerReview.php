<?php

$rr = user_needs_registration($_SESSION['TENANT-' . app()->tenant->getId()]['UserID']);

$userID = $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'];
$pagetitle = "Member Review";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";

?>

<div class="container-xl">
  <div>
    <form method="post">
      <?php if (isset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'])) {
				echo $_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState'];
				unset($_SESSION['TENANT-' . app()->tenant->getId()]['ErrorState']);
				?>
      <hr>
      <?php } ?>
      <h1>Review your members</h1>
      <p class="lead">
			<?php if ($rr) { ?>Make sure all of your members are listed here before you continue.<?php } else { ?>Make sure all of your members are listed here. Make sure you <a target="_blank" href="<?=autoUrl("my-account/add-member")?>"> add them </a> if not.<?php } ?>
      </p>

      <p>
        If your members are not listed here then their membership cannot be renewed. Failure to renew can lead to them not being allowed to train or compete at galas.
      </p>

      <?=mySwimmersTable(null, $userID)?>

      <?php if ($rr) { ?>
      <p>
        The links to your members are unavailable until you have completed
        registration.
      </p>
      <?php } ?>

      <div>
        <button type="submit" class="btn btn-success">Save and Continue</button>
      </div>
    </form>
  </div>
</div>

<?php

$footer = new \SCDS\Footer();
$footer->render();