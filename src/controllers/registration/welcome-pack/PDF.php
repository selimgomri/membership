<?php

global $db;

$user = $db->prepare("SELECT Forename, Surname, EmailAddress FROM users WHERE UserID = ?");
$user->execute([2]);

$swimmers = $db->prepare("SELECT MForename fn, MSurname sn, SquadName squad, SquadFee fee FROM members INNER JOIN squads ON squads.SquadID = members.SquadID WHERE members.UserID = ? ORDER BY fn ASC");
$swimmers->execute([2]);
$swimmers = $swimmers->fetchAll(PDO::FETCH_ASSOC);

$email_info = $user->fetch(PDO::FETCH_ASSOC);

$pagetitle = env('CLUB_NAME') . " Welcome Pack";

ob_start();?>

<!DOCTYPE html>
<html>
  <head>
  <meta charset='utf-8'>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i" rel="stylesheet" type="text/css">
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
      <h1 class="mb-0">
        Welcome to <?=htmlspecialchars(env('CLUB_NAME'))?>
      </h1>
      <p class="lead">
        Your Welcome Pack
      </p>

      <p class="mb-0">
        <strong>This welcome pack covers these swimmers;</strong>
      </p>

      <ul class="mb-0 list-unstyled"> 
        <?php foreach ($swimmers as $s) { ?>
        <li><?=htmlspecialchars($s['fn'] . ' ' . $s['sn'])?>, <?=htmlspecialchars($s['squad'])?> Squad</li>
        <?php } ?>
      </ul>
    </div>

    <h2>
      What's in this welcome pack?
    </h2>

    <p class="lead">
      In this pack you'll find;
    </p>

    <ul>
      <li>Chairman's welcome</li>
      <li>Information about your squads</li>
      <li>Club Codes of Conduct (for you and your swimmers)</li>
      <li>Information about your fees</li>
      <li>Direct debit payment information</li>
      <li>What are galas?</li>
      <li>How to enter galas</li>
      <li>Welfare information</li>
      <li>More about club policies</li>
    </ul>

    <h2>
      First things first
    </h2>

    <p>
      If you haven't already done so, you'll need to set up your club account. Your account is an easy and secure way of managing your swimmers, gala (competition) entries, payments and more.
    </p>

    <p>
      If you haven't already done so, you'll need to finish setting up your club account. We've sent you an email containing instructions on how to do that. You'll be asked to;
    </p>

    <ul>
      <li>Create a password</li>
      <li>Confirm your email and sms options</li>
    </ul>

    <p>
      We'll then automatically log you in.
    </p>

    <div class="page-break"></div>

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

if (!isset($attachment)) {
  // Output the generated PDF to Browser
  header('Content-Description: File Transfer');
  header('Content-Type: application/pdf');
  header('Content-Disposition: inline');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  $dompdf->stream(str_replace(' ', '', $pagetitle) . ".pdf", ['Attachment' => 0]);
} else if ($attachment) {
  $pdfOutput = $dompdf->output();
}