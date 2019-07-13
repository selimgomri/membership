<?php

global $db;
$user = $_SESSION['UserID'];

$sql = $payments = null;
$count = 0;

$club = json_decode(CLUB_JSON);

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

$sql = $db->prepare("SELECT `UserID`, `Name`, `Amount`, `Status`, `Date` FROM `payments` WHERE `PMkey` = ?");
$sql->execute([$PaymentID]);
$payment_info = $sql->fetch(PDO::FETCH_ASSOC);
$name = getUserName($payment_info['UserID']);

$use_white_background = true;
$PaymentID = mb_strtoupper($PaymentID);
$pagetitle = "Statement for " . $name . ", "
 . $PaymentID;

$userObj = new \User($payment_info['UserID'], $db);
$json = $userObj->getUserOption('MAIN_ADDRESS');
$address = null;
if ($json != null) {
  $address = json_decode($json);
}

require BASE_PATH . 'controllers/payments/GoCardlessSetup.php';

ob_start();?>

<!DOCTYPE html>
<html>
  <head>
  <meta charset='utf-8'>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i" rel="stylesheet" type="text/css">
  <!--<link href="https://fonts.googleapis.com/css?family=Open+Sans:700,700i" rel="stylesheet" type="text/css">-->
  <?php include BASE_PATH . 'helperclasses/PDFStyles/Main.php'; ?>
  <title><?=$pagetitle?></title>
  </head>
  <body>
    <?php include BASE_PATH . 'helperclasses/PDFStyles/Letterhead.php'; ?>

    <p>
      <?=date("d/m/Y", strtotime($payment_info['Date']))?>
    </p>

    <?php if ($address != null && isset($address->streetAndNumber)) { ?>
    <address class="mb-3">
      <strong><?=htmlspecialchars($name)?></strong><br>
      <?=htmlspecialchars($address->streetAndNumber)?><br>
      <?php if (isset($address->flatOrBuilding)) { ?>
      <?=htmlspecialchars($address->streetAndNumber)?><br>
      <?php } ?>
      <?=htmlspecialchars(mb_strtoupper($address->city))?><br>
      <?=htmlspecialchars(mb_strtoupper($address->postCode))?>
    </address>
    <?php } else { ?>
    <p>
      <strong><?=htmlspecialchars($name)?></strong><br>
      Parent
    </p>
    <?php } ?>

    <div class="primary-box mb-3" id="title">
      <h1 class="mb-0" title="<?=htmlspecialchars($payment_info['Name'])?>">
      <?=htmlspecialchars($payment_info['Name'])?>
      </h1>
      <p class="lead mb-0">
        Statement of Fees and Charges
      </p>
    </div>

    <h2 id="payment-info">About this payment</h2>
    <dl>
      <div class="row">
        <dt class="split-30">Payment Identifier</dt>
        <dd class="split-70">
          <span class="mono">
            <?=htmlspecialchars($PaymentID)?>
          </span>
        </dd>
      </div>

      <div class="row">
        <dt class="split-30">Total Fee</dt>
        <dd class="split-70">
          <span class="mono">
            &pound;<?=htmlspecialchars(number_format(($payment_info['Amount']/100),2,'.',''))?>
          </span>
        </dd>
      </div>

      <div class="row">
        <dt class="split-30">Payment Status as of <?=date("d/m/Y")?></dt>
        <dd class="split-70">
          <span class="mono">
            <?=htmlspecialchars(paymentStatusString($payment_info['Status']))?>
          </span>
        </dd>
      </div>
    </dl>

    <h2 id="payment-details">Itemised Details</h2>
    <p>Payments listed below were charged as part of one single Direct Debit</p>
    <?php if ($count == 0) { ?>
      <div class="">
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
      <table>
        <thead>
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
            <th>
              Status
            </th>
          </tr>
        </thead>
        <tbody>
        <?php
        do {
          //$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
          $data = "";
          if ($row['MetadataJSON'] != "" || $row['MetadataJSON'] != "") {
            $json = json_decode($row['MetadataJSON']);
            if ($json->PaymentType == "SquadFees"  || $json->PaymentType == "ExtraFees") {
              $data .= '<ul class="list-unstyled mb-0">';
              //echo sizeof($json->Members);
              //pre($json->Members);
              //echo $json->Members[0]->MemberName;
              $numMems = (int) sizeof($json->Members);
              for ($y = 0; $y < $numMems; $y++) {
                $data .= '<li>' . htmlspecialchars($json->Members[$y]->FeeName) . " (&pound;" . htmlspecialchars($json->Members[$y]->Fee) . ") for " . htmlspecialchars($json->Members[$y]->MemberName) . '</li>';
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
            <td>
              <?=htmlspecialchars($row['Status'])?>
            </td>
          </tr>
          <?php
          $row = $payments->fetch(PDO::FETCH_ASSOC);
        } while ($row != null); ?>
        </tbody>
      </table>
    <?php } ?>

    <div class="page-break"></div>

    <h2 id="payment-concerns">Got problems or concerns?</h2>
    <p>
      Problems are very rare but when they do happen we'll work to resolve them as quickly as possible.
    </p>
    <p>
      Contact the treasurer(s) as soon as possible if this happens.
    </p>

    <h2 id="payment-questions">Questions about Direct Debit?</h2>
    <p>Full help and support for payments by Direct Debit is available on the <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/" target="_blank">Membership System Support Website</a>. Help and Support Documentation is provided by Chester-le-Street ASC to all clubs and users that use this service. If you need somebody to help you, please contact your own club.</p>

    <div class="row" id="payment-dd-guarantee">
      <div class="split-75">
        <h2>The Direct Debit Guarantee</h2>
      </div>
      <div class="split-25 text-right">
        <img src="<?=BASE_PATH?>public/img/directdebit/directdebit@3x.png" style="height:1cm;" class="mb-3" alt="Direct Debit Logo">
      </div>
    </div>
    <p>The Direct Debit Guarantee applies to payments made to <?=htmlspecialchars(env('CLUB_NAME'))?></p>

    <ul>
      <li>
        This Guarantee is offered by all banks and building societies that accept instructions to pay Direct Debits
      </li>
      <li>
        If there are any changes to the amount, date or frequency of your Direct Debit <?=htmlspecialchars(env('CLUB_NAME'))?> will notify you three working days in advance of your account being debited or as otherwise agreed. If you request <?=htmlspecialchars(env('CLUB_NAME'))?> to collect a payment, confirmation of the amount and date will be given to you at the time of the request
      </li>
      <li>
        If an error is made in the payment of your Direct Debit, by <?=htmlspecialchars(env('CLUB_NAME'))?> or your bank or building society, you are entitled to a full and immediate refund of the amount paid from your bank or building society
      </li>
        <ul>
          <li>
            If you receive a refund you are not entitled to, you must pay it back when <?=htmlspecialchars(env('CLUB_NAME'))?> asks you to
          </li>
        </ul>
      <li>
        You can cancel a Direct Debit at any time by simply contacting your bank or building society. Written confirmation may be required. Please also notify us.
      </li>
    </ul>

    <p>Payments are handled by <a href="https://gocardless.com/">GoCardless</a> on behalf of <?=htmlspecialchars(env('CLUB_NAME'))?>.</p>

    <p>&copy; <?=htmlspecialchars(env('CLUB_NAME'))?> <?=date("Y", strtotime($payment_info['Date']))?></p>

    <?php include BASE_PATH . 'helperclasses/PDFStyles/PageNumbers.php'; ?>
  </body>
</html>

<?php

$html = ob_get_clean();

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();

// set font dir here
$dompdf->set_option('font_dir', BASE_PATH . 'fonts/');

$dompdf->set_option('defaultFont', 'Open Sans');
$dompdf->set_option('defaultMediaType', 'all');
$dompdf->set_option("isPhpEnabled", true);
$dompdf->set_option('isFontSubsettingEnabled', false);
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: inline');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$dompdf->stream(str_replace(' ', '', $pagetitle) . ".pdf", ['Attachment' => 0]);
