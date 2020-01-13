<?php

global $db;
$query = null;

$markdown = new ParsedownExtra();

// Safe mode is disabled during the transition to markdown
// $markdown->setSafeMode(true);

if ($_SESSION['AccessLevel'] == 'Parent') {
  $sql = "SELECT COUNT(*) FROM `members` WHERE `UserID` = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([$_SESSION['UserID']]);
    if ($query->fetchColumn() == 0) {
      halt(404);
    }
	} catch (PDOException $e) {
		halt(500);
	}
}

if ($int) {
	$sql = "SELECT * FROM `posts` WHERE `ID` = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([$id]);
	} catch (PDOException $e) {
		halt(500);
	}
} else {
	$sql = "SELECT * FROM `posts` WHERE `Path` = ?";
	try {
		$query = $db->prepare($sql);
		$query->execute([$id]);
	} catch (PDOException $e) {
		halt(500);
	}
}
$row = $query->fetch(PDO::FETCH_ASSOC);

if (!$row) {
  halt(404);
}

$date = new DateTime('now', new DateTimeZone('Europe/London'));
	
ob_start();?>

<!DOCTYPE html>
<html>
  <head>
  <meta charset='utf-8'>
  <?php if (bool(env('IS_CLS'))) { ?>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i" rel="stylesheet" type="text/css">
  <?php } else { ?>
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,400i" rel="stylesheet" type="text/css">
  <?php } ?>
  <?php include BASE_PATH . 'helperclasses/PDFStyles/Main.php'; ?>
  <title><?= htmlentities($row['Title']) ?></title>
  </head>
  <body>
    <?php include BASE_PATH . 'helperclasses/PDFStyles/Letterhead.php'; ?>

		<div class="row mb-3 text-right">
      <div class="split-50">
      </div>
      <div class="split-50">
        <p>
          <?=$date->format("d/m/Y")?>
        </p>
      </div>
    </div>

    <div class="" id="title">
      <h1>
				<?=htmlspecialchars($row['Title'])?>
      </h1>
    </div>

    <?=$markdown->text($row['Content'])?>

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
$dompdf->set_option('isFontSubsettingEnabled', true);
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