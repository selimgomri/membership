<?php

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

global $db;
$user = $_SESSION['UserID'];

$sql = $payments = null;
$count = 0;

// Check the thing exists

if ($_SESSION['AccessLevel'] == "Parent") {
  // Check the payment exists and belongs to the user
  $sql = $db->prepare("SELECT COUNT(*) FROM payments WHERE PMkey = ? AND UserID = ?");
  $sql->execute([$PaymentID, $user]);
  if ($sql->fetchColumn() == 0) {
    halt(404);
  }

  $sql = $db->prepare("SELECT COUNT(*) FROM `paymentsPending` INNER JOIN `users` ON users.UserID = paymentsPending.UserID WHERE `PMkey` = ? AND paymentsPending.UserID = ?");
  $sql->execute([$PaymentID, $user]);
  $count = $sql->fetchColumn();

	$payments = $db->prepare("SELECT * FROM `paymentsPending` INNER JOIN `users` ON users.UserID = paymentsPending.UserID WHERE `PMkey` = ? AND paymentsPending.UserID = ?");
  $payments->execute([$PaymentID, $user]);
} else {
  $sql = $db->prepare("SELECT COUNT(*) FROM payments WHERE PMkey = ?");
  $sql->execute([$PaymentID]);
  if ($sql->fetchColumn() == 0) {
    halt(404);
  }

  $sql = $db->prepare("SELECT COUNT(*) FROM `paymentsPending` INNER JOIN `users` ON users.UserID = paymentsPending.UserID WHERE `PMkey` = ?");
  $sql->execute([$PaymentID]);
  $count = $sql->fetchColumn();

	$payments = $db->prepare("SELECT * FROM `paymentsPending` INNER JOIN `users` ON users.UserID = paymentsPending.UserID WHERE `PMkey` = ?");
  $payments->execute([$PaymentID]);
}

$row = $payments->fetch(PDO::FETCH_ASSOC);

$sql = $db->prepare("SELECT payments.`UserID`, payments.`Name`, `Amount`, `Status`, `Date`, BankName, AccountHolderName, AccountNumEnd FROM `payments` LEFT JOIN paymentMandates ON payments.MandateID = paymentMandates.MandateID WHERE `PMkey` = ?");
$sql->execute([$PaymentID]);
$payment_info = $sql->fetch(PDO::FETCH_ASSOC);
$name = getUserName($payment_info['UserID']);

$use_white_background = true;
$PaymentID = mb_strtoupper($PaymentID);
$pagetitle = "Statement for " . htmlspecialchars($name) . ", " . htmlspecialchars($PaymentID);

$_SESSION['qr'][0]['text'] = autoUrl("payments/history/statement/" . htmlspecialchars(strtoupper($PaymentID)));

$billDate = null;
try {
  $billDate = new DateTime($payment_info['Date'], new DateTimeZone('UTC'));
  $billDate->setTimezone(new DateTimeZone('Europe/London'));
} catch (Exception $e) {
  $billDate = new DateTime('now', new DateTimeZone('Europe/London'));
}

include BASE_PATH . "views/header.php";
include BASE_PATH . "views/paymentsMenu.php";

 ?>

<div class="container">
  <?php if ($_SESSION['AccessLevel'] == 'Parent') { ?>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=autoUrl("payments")?>">Payments</a></li>
			<li class="breadcrumb-item"><a href="<?=autoUrl("payments/transactions")?>">History</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=htmlspecialchars($billDate->format("j M Y"))?></li>
    </ol>
  </nav>
  <?php } ?>

	<div class="">
    <span class="d-none d-print-block h1"><?=htmlspecialchars(env('CLUB_NAME'))?> Payments</span>
    <?php if ($_SESSION['AccessLevel'] == "Parent") { ?>
    <h1 class="border-bottom border-gray pb-2 mb-2"><?=htmlspecialchars($payment_info['Name'])?> Statement</h1>
    <?php } else { ?>
		<h1 class="border-bottom border-gray pb-2 mb-2">Statement for <?=htmlspecialchars($name)?></h1>
    <?php } ?>
    <dl class="row">
      <dt class="col-md-4">Payment Identifier</dt>
      <dd class="col-md-8"><span class="mono"><?=htmlspecialchars($PaymentID)?></span></dd>

      <dt class="col-md-4">Total Fee</dt>
      <dd class="col-md-8"><span class="mono">&pound;<?=htmlspecialchars(number_format(($payment_info['Amount']/100),2,'.',''))?></span></dd>

      <dt class="col-md-4">Payment Status</dt>
      <dd class="col-md-8"><span class=""><?=htmlspecialchars(paymentStatusString($payment_info['Status']))?></span></dd>

      <?php if ($payment_info['BankName'] != null || $payment_info['AccountNumEnd'] != null || $payment_info['AccountHolderName'] != null) { ?>

      <dt class="col-md-4">Bank</dt>
      <dd class="col-md-8">
        <span>
          <?=htmlspecialchars(getBankName($payment_info['BankName']))?>
        </span>
      </dd>

      <dt class="col-md-4">Bank Account</dt>
      <dd class="col-md-8">
        <span>
          &middot;&middot;&middot;&middot;&middot;&middot;<?=htmlspecialchars($payment_info['AccountNumEnd'])?>
        </span>
      </dd>
      
      <dt class="col-md-4">Account Name</dt>
      <dd class="col-md-8">
        <span class="mono">
          <?=htmlspecialchars(mb_strtoupper($payment_info['AccountHolderName']))?>
        </span>
      </dd>

      <?php } ?>

    </dl>

    <?php if ($_SESSION['AccessLevel'] == "Admin" && ($payment_info['Status'] == 'customer_approval_denied' || $payment_info['Status'] == 'failed')) {
    $_SESSION['Token' . $PaymentID] = hash('sha256', random_int(0, 999999));
    ?>
    <p>
      <a href="<?=currentUrl()?>markpaid/<?=$_SESSION['Token' . $PaymentID]?>" class="btn btn-primary">
        Mark as Paid
      </a>
    </p>
    <p class="text-muted small">
      If this payment failed and/or has been paid manually, by Cash, Cheque or
      Bank Transfer, mark it as paid here.
    </p>
    <?php } ?>

    <div class="mb-4">
      <h2>Itemised Details</h2>
  		<p>Payments listed below were charged as part of one single Direct Debit</p>
  		<?php if ($count == 0) { ?>
  			<div class="alert alert-warning mb-0">
  				<p class="mb-0">
  					<strong>
  						No fees can be found for this statement
  					</strong>
  				</p>
  				<p class="mb-0">
  					This usually means that the payment was created via the GoCardless
  					User Interface and not directly in this system. Please speak to the
            treasurer to find out more.
  				</p>
  			</div>
  		<?php } else { ?>
  		<div class="table-responsive-md">
  			<table class="table">
  				<thead class="thead-light">
  					<tr>
  						<th>
  							Date
  						</th>
  						<th>
  							Description
  						</th>
  						<th>
  							Amount
  						</th>
  					</tr>
  				</thead>
  				<tbody>
  				<?php
  				do {
  					$data = "";
  					if ($row['MetadataJSON'] != "" || $row['MetadataJSON'] != "") {
  						$json = json_decode($row['MetadataJSON']);
  						if ($json->PaymentType == "SquadFees"  || $json->PaymentType == "ExtraFees") {
  							$data .= '<ul class="list-unstyled mb-0">';
  							//echo sizeof($json->Members);
  							//pre($json->Members);
                //echo $json->Members[0]->MemberName;
                $numMems = 0;
                if (isset($json->Members) && $json->Members != null) {
                  $numMems = (int) sizeof($json->Members);
                  for ($y = 0; $y < $numMems; $y++) {
                    $data .= '<li>' . htmlspecialchars($json->Members[$y]->FeeName) . " (&pound;" . htmlspecialchars($json->Members[$y]->Fee) . ") for " . htmlspecialchars($json->Members[$y]->MemberName) . '</li>';
                  }
                }
  							$data .= '</ul>';
  						}
  					}
  					?>
  					<tr>
  						<td>
  							<?=date("D j M Y",strtotime($row['Date']))?>
  						</td>
  						<td>
  							<?=htmlspecialchars($row['Name'])?>
  							<em><?=$data?></em>
  						</td>
  						<td>
                <?php if ($row['Type'] == "Payment") { ?>
  							&pound;<?=htmlspecialchars(number_format(($row['Amount']/100),2,'.',''))?>
                <?php } else { ?>
                -&pound;<?=htmlspecialchars(number_format(($row['Amount']/100),2,'.',''))?> (Credit)
                <?php } ?>
  						</td>
  					</tr>
  					<?php
            $row = $payments->fetch(PDO::FETCH_ASSOC);
  				} while ($row != null); ?>
  				</tbody>
  			</table>
  		</div>
  		<?php } ?>
    </div>

    <p>
      <a href="<?=currentUrl()?>pdf" target="_blank" class="btn btn-primary">
        PDF Download
      </a>
    </p>

    <div class="row">
      <div class="col-md-10 col-lg-8">
        <h2>Got problems or concerns?</h2>
        <p class="lead">
          Problems are very rare but when they do happen we'll work to resolve them
          as quickly as possible.
        </p>
        <!--<p>
          Print off this page and club staff can scan this code to access your
          statement.
        </p>
        <img class="img-fluid d-block mb-3" src="<?=autoUrl("services/qr/0/150")?>" srcset="<?=autoUrl("services/qr/0/300")?> 2x, <?=autoUrl("services/qr/0/450")?> 3x" alt="<?=htmlspecialchars(autoUrl("payments/history/statement/" . strtoupper($PaymentID)))?>">
      -->

        <h2>Questions about Direct Debit</h2>
        <p>
          Full help and support for payments by Direct Debit is available on the <a
          href="https://www.chesterlestreetasc.co.uk/support/directdebit/"
          target="_blank">Chester-le-Street ASC website</a>.
        </p>
        <p>
          Payments to <?=htmlspecialchars(env('CLUB_NAME'))?> are covered by the Direct Debit Guarantee.
        </p>
      </div>
    </div>
	</div>
</div>

<?php $footer = new \SCDS\Footer();
$footer->render();
