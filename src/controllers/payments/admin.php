<?php

use Brick\Math\RoundingMode;

$db = app()->db;
$tenant = app()->tenant;

$user = $_SESSION['TENANT-' . app()->tenant->getId()]['UserID'];
$pagetitle = "Payments Administration";

// require 'GoCardlessSetup.php';

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

$dateString = date("F Y");

$income = $db->prepare("SELECT `Date`, SUM(AMOUNT) AS Total FROM `payments` INNER JOIN users ON users.UserID = payments.UserID WHERE users.Tenant = ? AND `Date` LIKE '%-01' GROUP BY `Date` ORDER BY `Date` DESC LIMIT 8");
$income->execute([
  $tenant->getId()
]);
$income = $income->fetchAll(PDO::FETCH_ASSOC);

 ?>

 <div class="front-page mb-n3">
   <div class="container">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-light">
        <li class="breadcrumb-item active" aria-current="page">Payments</li>
      </ol>
    </nav>

  	<h1>Payment Administration</h1>
  	<p class="lead">Control Direct Debit Payments</p>

    <div class="mb-4">
      <h2 class="mb-4">View Squad Fee Status</h2>
      <div class="mb-4">
        <div class="news-grid">

          <a href="<?=autoUrl("payments/history/squads/" . date("Y/m"))?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Squad Fees for <?=$dateString?>
  						</span>
  						<span>
  							View current payment status
  						</span>
  					</span>
            <span class="category">
  						Direct Debit
  					</span>
          </a>

          <a href="<?=autoUrl("payments/history/extras/" . date("Y/m"))?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Extra Fees for <?=$dateString?>
  						</span>
  						<span>
  							View current payment status
  						</span>
  					</span>
            <span class="category">
  						Direct Debit
  					</span>
          </a>
        </div>
      </div>
    </div>

    <div class="mb-4">
      <h2 class="mb-4">Manual Charges</h2>
      <div class="mb-4">
        <div class="news-grid">

          <a href="<?=autoUrl('payments/estimated-fees')?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Manual Billing Information
  						</span>
  						<span>
  							View all expected fees for all parents
  						</span>
  					</span>
            <span class="category">
  						Pay
  					</span>
          </a>

          <a href="<?=htmlspecialchars(autoUrl('payments/invoice-payments'))?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Invoice payments
  						</span>
  						<span>
  							Manually create charges and credits
  						</span>
  					</span>
            <span class="category">
  						Pay
  					</span>
          </a>

          <a href="<?=autoUrl('galas/charges-and-refunds')?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Charge for gala entries
  						</span>
  						<span>
  							Charge and issue refunds for gala entries
  						</span>
  					</span>
            <span class="category">
  						Pay
  					</span>
          </a>
        </div>
      </div>
    </div>

    <div class="mb-4">
      <h2 class="mb-4">Administrative Options</h2>
      <div class="mb-4">
        <div class="news-grid">

          <a href="<?=autoUrl('payments/categories')?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Payment categories
  						</span>
  						<span>
  							Create categories and assign them to payment items
  						</span>
  					</span>
            <span class="category">
  						Admin
  					</span>
          </a>
        </div>
      </div>

    <div class="mb-4">
      <?php

      $labels = [];
      for ($i = sizeof($income); $i > 0; $i--) {
        $date = new DateTime($income[$i-1]['Date'], new DateTimeZone('Europe/London'));
        $labels[] = $date->format("F");
      }

      $data = [];
      for ($i = sizeof($income); $i > 0; $i--) {
        $data[] = \Brick\Math\BigDecimal::of((string) $income[$i-1]['Total'])->withPointMovedLeft(2)->toScale(2, RoundingMode::HALF_UP);
      }

      $json = json_encode(['labels' => $labels, 'data' => $data]);

      ?>
      <h2 class="mb-4">Income Statistics</h2>
      <canvas id="incomeChart" data-data="<?=htmlspecialchars($json)?>" class="cell mb-1 bg-white"></canvas>
      <p class="small text-muted mb-4">
        This is the amount charged to parents before GoCardless handling fees.
      </p>
    </div>
  </div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->addJs('public/js/payments/admin-graph.js');
$footer->render();
