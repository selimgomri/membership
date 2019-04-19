<?php

global $db;

$user = $_SESSION['UserId'];
$pagetitle = "Payments Administration";

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

require 'GoCardlessSetup.php';

$dateString = date("F Y");

$income = $db->query("SELECT `Date`, SUM(AMOUNT) AS Total FROM `payments` WHERE `Date` LIKE '%-01' GROUP BY `Date` ORDER BY `Date` DESC LIMIT 8");
$income = $income->fetchAll(PDO::FETCH_ASSOC);

 ?>

 <div class="front-page" style="margin-bottom: -1rem;">
   <div class="container">
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

          <a href="<?=autoUrl('payments/fees')?>">
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

          <a href="<?=autoUrl('payments/newcharge')?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Create a manual charge
  						</span>
  						<span>
  							Charge a parent for a non-automatic fee
  						</span>
  					</span>
            <span class="category">
  						Pay
  					</span>
          </a>

          <a href="<?=autoUrl('payments/galas')?>">
  					<span class="mb-3">
  	          <span class="title mb-0">
  							Charge for gala entries
  						</span>
  						<span>
  							After accepted entries are known
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
      <h2 class="mb-4">Income Statistics</h2>
      <canvas id="incomeChart" class="cell mb-1 bg-white"></canvas>
      <p class="small text-muted mb-4">
        This is the amount charged to parents before GoCardless handling fees.
      </p>
    </div>
  </div>
</div>

<script src="<?=autoUrl("public/js/Chart.min.js")?>"></script>
<script>
var ctx = document.getElementById('incomeChart').getContext('2d');
var chart = new Chart(ctx, {
  // The type of chart we want to create
  type: 'bar',

  // The data for our dataset
  data: {
    labels: [
      <?php for ($i = sizeof($income); $i > 0; $i--) { ?>
      "<?=date("F", strtotime($income[$i-1]['Date']))?>",
      <?php } ?>
    ],
    datasets: [{
        label: "Total charged (£ Pounds)",
        data: [
          <?php for ($i = sizeof($income); $i > 0; $i--) { ?>
          <?=((int) $income[$i-1]['Total'])/100?>,
          <?php } ?>
        ],
        backgroundColor: '#bd0000'
    }],
  },

  // Configuration options go here
  options: {
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true
        }
      }]
    }
  }
});
</script>

<?php include BASE_PATH . "views/footer.php";
