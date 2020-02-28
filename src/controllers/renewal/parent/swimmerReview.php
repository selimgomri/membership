<?php

$rr = user_needs_registration($_SESSION['UserID']);

$userID = $_SESSION['UserID'];
$pagetitle = "Swimmer Review";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/renewalTitleBar.php";

?>

<div class="container">
  <div>
    <form method="post">
      <?php if (isset($_SESSION['ErrorState'])) {
				echo $_SESSION['ErrorState'];
				unset($_SESSION['ErrorState']);
				?>
      <hr>
      <?php } ?>
      <h1>Review your swimmers</h1>
      <p class="lead">
			<?php if ($rr) { ?>Make sure all of your swimmers are listed here before you continue.<?php } else { ?>Make sure all of your swimmers are listed here. Make sure you <a target="_blank" href="<?=autoUrl("my-account/addswimmer")?>"> add them </a> if not.<?php } ?>
      </p>

      <p>
        If your swimmers are not listed here then their membership cannot be renewed. Failure to renew can lead to them not being allowed to train or compete at galas.
      </p>

      <?=mySwimmersTable(null, $userID)?>

      <?php if ($rr) { ?>
      <p>
        The links to your swimmers are unavailable until you have completed
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

$footer = new \SDCS\Footer();
$footer->render();