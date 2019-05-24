<?php

global $db;

$sql = "SELECT `SquadName`, `MForename`, `MSurname`, Forename, Surname, DateOfBirth, `SquadFee`, SquadCoC, `SquadTimetable`, `users`.`UserID`, MovingDate FROM (((`members` INNER JOIN `users` ON users.UserID = members.UserID) INNER JOIN `moves` ON members.MemberID = moves.MemberID) INNER JOIN `squads` ON moves.SquadID = squads.SquadID) WHERE members.MemberID = ?";
$email_info = $db->prepare($sql);
$email_info->execute([$id]);
$email_info = $email_info->fetch(PDO::FETCH_ASSOC);

$pagetitle = htmlspecialchars($email_info['MForename'] . " " . $email_info['MSurname']) . " Squad Move Contract";

ob_start();?>

<!DOCTYPE html>
<html>
  <head>
  <meta charset='utf-8'>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i" rel="stylesheet" type="text/css">
  <!--<link href="https://fonts.googleapis.com/css?family=Open+Sans:700,700i" rel="stylesheet" type="text/css">-->
  <style>
  .signature-box {
    padding: 5pt;
    margin-bottom: 16pt;
    border: 0.05cm solid #777;
    width: 8cm;
    height: 2cm;
    background: #fff;
  }
  .cell {
    padding: 10pt;
    background: #eee;
    margin: 0 0 16pt 0;
    display: block;
  }
  </style>
  <?php include BASE_PATH . 'helperclasses/PDFStyles/Main.php'; ?>
  <title><?=$pagetitle?></title>
  </head>
  <body>
    <?php include BASE_PATH . 'helperclasses/PDFStyles/Letterhead.php'; ?>

    <p>
      <?=date("d/m/Y")?>
    </p>

    <p>
      <strong><?=htmlspecialchars($email_info['Forename'] . " " . $email_info['Surname'])?></strong><br>
      Registered Parent/Carer
    </p>

    <div class="primary-box mb-3" id="title">
      <h1>
        <?=htmlspecialchars($email_info['MForename'] . " " . $email_info['MSurname'])?>'s move to <?=htmlspecialchars($email_info['SquadName'])?> Squad
      </h1>
      <p class="lead mb-0">
        Squad move contract
      </p>
    </div>

    <p>
      We're very excited to let you know that <?=htmlspecialchars($email_info['MForename'] . " " . $email_info['MSurname'])?> will be moving to <?=htmlspecialchars($email_info['SquadName'])?> Squad on <?=date("l j F Y", strtotime($email_info['MovingDate']))?>.
    </p>

    <p>
      The Squad Fee you will pay will be &pound;<?=number_format($email_info['SquadFee'], 2)?>*.
    </p>

    <p>
      <?php if ((defined('IS_CLS') && IS_CLS)) { ?>As you pay by Direct Debit<?php } else { ?>If you pay by Direct Debit<?php } ?>, you won't need to take any action. We'll automatically update your monthly fees.
    </p>

    <?php if ($email_info['SquadTimetable'] != "" && $email_info['SquadTimetable'] != null) { ?>
    <p>
      You can get the timetable for <?=htmlspecialchars($email_info['SquadName'])?> Squad at <a href="<?=htmlspecialchars($email_info['SquadTimetable'])?>" target="_blank"><?=htmlspecialchars($email_info['SquadTimetable'])?></a>.
    </p>
    <?php } ?>

    <?php if ((defined('IS_CLS') && IS_CLS)) { ?>
      <div class="cell">
        <h2>Instructions for parents</h2>
        <p>
          <strong>
            If you've been sent this document electronically, please print it out. Electronic signatures will not be accepted.
          </strong>
        </p>

        <p class="mb-0">
          This document contains a form which your swimmer <strong>must sign before starting in their new squad</strong>.
          <?php if (date_diff(date_create($email_info['DateOfBirth']), date_create('today'))->y < 18) { ?>
          As <?=htmlspecialchars($email_info['MForename'])?> is under the age of 18, you must also sign to confirm that you have explained the content and implications of the code of conduct to <?=htmlspecialchars($email_info['MForename'])?>.
          <?php } ?>
        </p>
      </div>
    <?php } ?>

    <div class="page-break"></div>

    <h1 id="payment-questions">Paying Squad Fees for <?=htmlspecialchars($email_info['SquadName'])?></h1>
    <?php if (defined('IS_CLS') && IS_CLS) { ?>
    <p>
      Your monthly direct debit will be automatically adjusted accordingly. Payments by Direct Debit are covered by the <a href="#payment-dd-guarantee">Direct Debit Guarantee</a>.
    </p>
    <?php } else { ?>
    <p>
      If your club uses Direct Debit payments, your monthly direct debit will be automatically adjusted accordingly. Payments by Direct Debit are covered by the <a href="#payment-dd-guarantee">Direct Debit Guarantee</a>.
    </p>
    <?php } ?>

    <p>
      Full help and support for payments by Direct Debit is available on the Membership System Support Website at <a href="https://www.chesterlestreetasc.co.uk/support/onlinemembership/">https://www.chesterlestreetasc.co.uk/support/onlinemembership/</a>. Help and Support Documentation is provided by Chester-le-Street ASC<?php if (!(defined('IS_CLS') && IS_CLS)) { ?> to all clubs and users that use this service. If you need somebody to help you, please contact your own club in the first instance<?php } ?>.
    </p>

    <div class="row" id="payment-dd-guarantee">
      <div class="split-75">
        <h2>The Direct Debit Guarantee</h2>
      </div>
      <div class="split-25 text-right">
        <img src="<?=BASE_PATH?>public/img/directdebit/directdebit@3x.png" style="height:1cm;" class="mb-3" alt="Direct Debit Logo">
      </div>
    </div>
    <p>The Direct Debit Guarantee applies to payments made to <?=htmlspecialchars(CLUB_NAME)?></p>

    <ul>
      <li>
        This Guarantee is offered by all banks and building societies that accept instructions to pay Direct Debits
      </li>
      <li>
        If there are any changes to the amount, date or frequency of your Direct Debit <?=htmlspecialchars(CLUB_NAME)?> will notify you three working days in advance of your account being debited or as otherwise agreed. If you request <?=htmlspecialchars(CLUB_NAME)?> to collect a payment, confirmation of the amount and date will be given to you at the time of the request
      </li>
      <li>
        If an error is made in the payment of your Direct Debit, by <?=htmlspecialchars(CLUB_NAME)?> or your bank or building society, you are entitled to a full and immediate refund of the amount paid from your bank or building society
      </li>
        <ul>
          <li>
            If you receive a refund you are not entitled to, you must pay it back when <?=htmlspecialchars(CLUB_NAME)?> asks you to
          </li>
        </ul>
      <li>
        You can cancel a Direct Debit at any time by simply contacting your bank or building society. Written confirmation may be required. Please also notify us.
      </li>
    </ul>

    <p>Payments are handled by <a href="https://gocardless.com/">GoCardless</a> on behalf of <?=htmlspecialchars(CLUB_NAME)?>.</p>

    <!--<p>&copy; <?=htmlspecialchars(CLUB_NAME)?> <?=date("Y")?></p>-->


    <?php if ($email_info['SquadCoC'] != "" && $email_info['SquadCoC'] != null) { ?>
    <div class="page-break"></div>

    <h1 id="squad-code-of-conduct">
      Code of conduct
    </h1>

    <p>
      By moving into this squad, you must agree to its code of conduct. <?php if ((defined('IS_CLS') && IS_CLS)) { ?><strong>You are required to sign and return this form to the club before <?=date("l j F Y", strtotime($email_info['MovingDate']))?>.<?php } else { ?><strong>If instructed to do so</strong> by your club, you should sign and return this document.<?php } ?>
    </p>

    <p>
      The code of conduct for <?=htmlspecialchars($email_info['SquadName'])?> Squad is as follows;
    </p>

    <?=getPostContent($email_info['SquadCoC'])?>

    <div class="page-break"></div>

    <h1 id="squad-code-of-conduct-agreement">
      Agreement to code of conduct
    </h1>

    <p><strong>
      <?php if ((defined('IS_CLS') && IS_CLS)) { ?>
        Please sign and return this form to any squad coach before you move into this squad on <?=date("l j F Y", strtotime($email_info['MovingDate']))?>.
        If you've been sent this form by email, please print it out. Electronic signatures will not be accepted.
      <?php } else { ?>
        If required to do so by your club, please sign and return this form.
      <?php } ?>
    </strong></p>

    <div class="prevent-page-break">
      <div class="cell">

        <?php if (date_diff(date_create($email_info['DateOfBirth']), date_create('today'))->y < 18) { ?>
          <p>
            As <?=htmlspecialchars($email_info['MForename'] . " " . $email_info['MSurname'])?> is under the age of 18 you, their parent/guardian, must sign to confirm you have explained the squad code of conduct to <?=htmlspecialchars($email_info['MForename'])?>.
          </p>

          <p>
            I, <span><?=htmlspecialchars($email_info['Forename'] . " " . $email_info['Surname'])?></span> have explained the content and implications to <?=htmlspecialchars($email_info['MForename'] . " " . $email_info['MSurname'])?> and can confirm that they understood.
          </p>

          <div class="signature-box">
            Signed by <span><?=htmlspecialchars($email_info['Forename'] . " " . $email_info['Surname'])?></span> (or print name)
          </div>
        <?php } ?>

        <p>
          I, <?=htmlspecialchars($email_info['MForename'] . " " . $email_info['MSurname'])?> agree to the Code of Conduct for <?=htmlspecialchars($email_info['SquadName'])?> Squad as outlined above as required by the Terms and Conditions of Membership of <?=CLUB_NAME?>.
        </p>

        <div class="signature-box mb-0">
          Signed by <span><?=htmlspecialchars($email_info['MForename'] . " " . $email_info['MSurname'])?></span>
        </div>
      </div>
    </div>

    <div class="prevent-page-break">
      <div class="cell">
        <div class="row">
          <div class="split-30">
            <img width="100" src="<?=autoUrl("services/qr-generator?size=600&text=" . urlencode(autoUrl("swimmers/id/agreement-to-code-of-conduct/squad")))?>">
          </div>
          <div class="split-70">
            <p class="mb-0">
              <strong>
                Staff Use
              </strong>
            </p>
            <p>
              Scan this code to mark form completed.
            </p>
            <p>
              All staff can mark this form as being complete.
          </div>
        </div>
      </div>
    </div>

    <p>
      You must abide by the above code of conduct if you take your place in this squad as per the Membership Terms and Conditions. This new code of conduct may be different to that for your current squad, so please read it carefully.
    </p>
    <?php } ?>

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
$dompdf->set_option('isRemoteEnabled',true);
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
