<?php

$pagetitle = 'My Squad and Extra Fees';

include BASE_PATH . 'views/header.php';

?>

<div class="container">
  <div class="row">
    <div class="col-lg-8">
      <h1>My monthly fees</h1>
      <p class="lead">These are the fees you pay each month.</p>
      <p>In some months, additional charges may be added to your account to cover the cost of gala entries and club membership.</p>

      <div class="mb-3">
				<?=myMonthlyFeeTable(null, $_SESSION['UserID'])?>
			</div>

      <p>Contact your club if you have any questions about these fees.</p>

    </div>
  </div>
</div>

<?php

$footer = new \SDCS\Footer();
$footer->render();